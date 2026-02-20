<?php

namespace App\Services;

use App\Models\Ia_alerte;
use App\Models\Message;
use App\Models\Produit;
use Carbon\Carbon;
use App\Models\MotInterdit;
use Illuminate\Support\Facades\Auth;

class IAService
{
    

    
    private function creerAlerte($typeAlerte, $description, $niveau, $destType, $destId, $sourceType = null, $sourceId = null, $messageContent = null)
    {
        $data = [
            'TypeAlerte'       => $typeAlerte,
            'Description'      => $description,
            'DateCreation'     => Carbon::now(),
            'NiveauGravité'    => $niveau,
            'destinataire_type'=> $destType,
            'destinataire_id'  => $destId,
            'lu'               => false,
        ];

        // New column names: Expediteur_type / Expediteur_id and Message
        if ($sourceType !== null) $data['Expediteur_type'] = $sourceType;
        if ($sourceId !== null) $data['Expediteur_id'] = $sourceId;
        if ($messageContent !== null) $data['Message'] = $messageContent;

        Ia_alerte::create($data);
    }

    public function verifierStockProduit(Produit $produit)
    {
        if ($produit->Stock < 5) {

            $this->creerAlerte(
                'Stock',
                "Stock faible pour le produit {$produit->Nom}, ({$produit->Stock} unités restantes)",
                'Rappelle',
                'vendeur',
                $produit->Vendeur_idVendeur
            );
        }
    }

    public function verifierProduitPeuVendu(Produit $produit)
    {
        // Récupère la dernière commande associée au produit
        $derniereCommande = $produit->commandes()
                                    ->orderBy('DateCommande', 'desc')
                                    ->first();

        // Vérifie si le produit a déjà été commandé
        if ($derniereCommande) {

            // Convertit la date en objet Carbon
            $dateCommande = Carbon::parse($derniereCommande->DateCommande);

            // Calcule la différence en jours avec aujourd’hui
            if ($dateCommande->diffInDays(Carbon::now()) > 14) {

                $this->creerAlerte(
                    'Vente',
                    "Le produit {$produit->Nom} n’a pas été vendu depuis plus de 30 jours.",
                    'info', // Gravité faible
                    'vendeur',
                    $produit->Vendeur_idVendeur
                );
            }
        }
    }

    /*
      Analyse automatique d’un message après sa création
      Si contenu interdit détecté → alerte admin
     */
    public function analyserMessage(Message $message)
{
    if (!$message->Contenu) {
        return;
    }

    $contenu = strtolower($message->Contenu);

    // Récupère tous les mots interdits depuis la base
    $motsInterdits = MotInterdit::all();

    $scoreToxicite = 0;

    foreach ($motsInterdits as $motInterdit) {

        if (str_contains($contenu, strtolower($motInterdit->mot))) {

            // Ajoute le poids au score total
            $scoreToxicite += $motInterdit->poids;
        }
    }

    // Si aucun mot détecté → on arrête
    if ($scoreToxicite == 0) {
        return;
    }

    // Déterminer le niveau selon le score
    if ($scoreToxicite <= 2) {
        $niveau = 'warning';
    } elseif ($scoreToxicite <= 4) {
        $niveau = 'danger';
    } else {
        $niveau = 'critique';
    }

    // Determine sender (Expediteur) using authenticated guard as primary source
    $senderType = null;
    $senderId = null;

    if (Auth::guard('vendeur')->check()) {
        $senderType = 'Vendeur';
        $senderId = Auth::guard('vendeur')->id();
    } elseif (Auth::guard('client')->check()) {
        $senderType = 'Client';
        $senderId = Auth::guard('client')->id();
    } elseif (!empty($message->Administrateur_idAdministrateur)) {
        $senderType = 'Administrateur';
        $senderId = $message->Administrateur_idAdministrateur;
    } else {
        // Fallback: infer from message fields (if only one is present)
        if (!empty($message->Client_idClient) && empty($message->Vendeur_idVendeur)) {
            $senderType = 'Client';
            $senderId = $message->Client_idClient;
        } elseif (!empty($message->Vendeur_idVendeur) && empty($message->Client_idClient)) {
            $senderType = 'Vendeur';
            $senderId = $message->Vendeur_idVendeur;
        }
    }

    // Determine recipient (destinataire): choose the other party if possible
    $destType = 'admin';
    $destId = $message->Administrateur_idAdministrateur ?? 1;

    if (!empty($message->Vendeur_idVendeur) && $senderType !== 'Vendeur') {
        // If message is for a vendeur recipient, prefer explicit destinataire if set
        if (!empty($message->VendeurDestinataire_idVendeur)) {
            $destType = 'Vendeur';
            $destId = $message->VendeurDestinataire_idVendeur;
        } else {
            $destType = 'Vendeur';
            $destId = $message->Vendeur_idVendeur;
        }
    } elseif (!empty($message->Client_idClient) && $senderType !== 'Client') {
        $destType = 'Client';
        $destId = $message->Client_idClient;
    }

    // Ne pas envoyer d'alertes de type "Message" aux vendeurs :
    // rediriger vers l'administrateur pour modération
    if ($destType === 'Vendeur') {
        $destType = 'admin';
        $destId = $message->Administrateur_idAdministrateur ?? 1;
    }

    $this->creerAlerte(
        'Message',
        "Message toxique détecté (Score: {$scoreToxicite}) : \"{$message->Contenu}\"",
        $niveau,
        $destType,
        $destId,
        $senderType,
        $senderId,
        $message->Contenu
    );
}

        // Analyse automatique des données de la plateforme
        public function analyserPlateforme()
        {
            $anomalies = [];
            $anomalies['produits'] = $this->analyseProduits();
            $anomalies['messages'] = $this->analyseMessages();
            $anomalies['vendeurs'] = $this->analyseVendeurs();
            $anomalies['activite'] = $this->analyseActivite();
            return $anomalies;
        }


        // Tableau de bord des anomalies détectées
        public function getTableauDeBord()
        {
            return $this->analyserPlateforme();
        }

        // Résumés intelligents de l’activité quotidienne ou hebdomadaire
        public function getResumeActivite($periode = 'jour')
        {
        $resume = [];
        // Définir la période
        $dateDebut = null;
        if ($periode === 'jour') {
            $dateDebut = Carbon::now()->subDay();
        } elseif ($periode === 'semaine') {
            $dateDebut = Carbon::now()->subWeek();
        } else {
            $dateDebut = Carbon::now()->subDay();
        }

        // Résumé des messages
        $nbMessages = Message::where('DateEnvoi', '>=', $dateDebut)->count();
        $resume['messages'] = $nbMessages;

        // Résumé des produits ajoutés
        $nbProduits = Produit::where('DateAjout', '>=', $dateDebut)->count();
        $resume['produits_ajoutes'] = $nbProduits;

        // Résumé des vendeurs actifs
        $vendeursActifs = \App\Models\Vendeur::whereHas('produits', function($q) use ($dateDebut) {
            $q->where('DateAjout', '>=', $dateDebut);
        })->orWhereHas('messages', function($q) use ($dateDebut) {
            $q->where('DateCreation', '>=', $dateDebut);
        })->count();
        $resume['vendeurs_actifs'] = $vendeursActifs;

        // Résumé des anomalies détectées
        $resume['anomalies'] = [
            'prix_anormal' => count(array_filter($this->analyseProduits(), function($a) { return $a['type'] === 'PrixAnormal'; })),
            'description_suspecte' => count(array_filter($this->analyseProduits(), function($a) { return $a['type'] === 'DescriptionSuspecte'; })),
            'vendeurs_inactifs' => count($this->analyseVendeurs()),
            'pics_activite' => count($this->analyseActivite()),
        ];

        return $resume;
        }

        // Analyse des produits (prix, description)
        private function analyseProduits()
        {
        $anomalies = [];
        $produits = Produit::all();
        foreach ($produits as $produit) {
            // Recherche des produits similaires (même Nom et Categorie)
            $similaires = Produit::where('Nom', $produit->Nom)
                ->where('Categorie', $produit->Categorie)
                ->where('idProduit', '!=', $produit->idProduit)
                ->get();
            if ($similaires->count() > 0) {
                $prixSimilaires = $similaires->pluck('Prix');
                $prixMoyen = $prixSimilaires->avg();
                $seuilBas = $prixMoyen * 0.7;
                $seuilHaut = $prixMoyen * 1.3;
                if ($produit->Prix < $seuilBas || $produit->Prix > $seuilHaut) {
                    $anomalies[] = [
                        'type' => 'PrixAnormal',
                        'produit' => $produit,
                        'prixMoyen' => $prixMoyen,
                        'seuilBas' => $seuilBas,
                        'seuilHaut' => $seuilHaut,
                    ];
                }
            }
            // Vérification description suspecte (ex: trop courte)
            if (strlen($produit->Description) < 10) {
                $anomalies[] = [
                    'type' => 'DescriptionSuspecte',
                    'produit' => $produit,
                ];
            }
        }
        return $anomalies;
        }


        // Analyse des vendeurs (inactivité)
        private function analyseVendeurs()
        {
        $anomalies = [];
        $vendeurs = \App\Models\Vendeur::all();
        $seuilJours = 30;
        foreach ($vendeurs as $vendeur) {
            // Recherche du dernier produit ajouté
            $dernierProduit = $vendeur->produits()->orderBy('DateAjout', 'desc')->first();
            // Recherche du dernier message envoyé
            $dernierMessage = $vendeur->messages()->orderBy('DateEnvoi', 'desc')->first();
            $dernierDate = null;
            if ($dernierProduit && $dernierMessage) {
                $dateProduit = \Carbon\Carbon::parse($dernierProduit->DateAjout);
                $dateMessage = \Carbon\Carbon::parse($dernierMessage->DateCreation);
                $dernierDate = $dateProduit->greaterThan($dateMessage) ? $dateProduit : $dateMessage;
            } elseif ($dernierProduit) {
                $dernierDate = \Carbon\Carbon::parse($dernierProduit->DateAjout);
            } elseif ($dernierMessage) {
                $dernierDate = \Carbon\Carbon::parse($dernierMessage->DateCreation);
            }
            // Si aucune activité ou dernière activité > seuil
            if (!$dernierDate || $dernierDate->diffInDays(\Carbon\Carbon::now()) > $seuilJours) {
                $anomalies[] = [
                    'type' => 'VendeurInactif',
                    'vendeur' => $vendeur,
                    'dernierDate' => $dernierDate ? $dernierDate->toDateString() : null,
                ];
            }
        }
        return $anomalies;
        }

        // Analyse de l’activité (pics anormaux)
        private function analyseActivite()
        {
        $anomalies = [];
        // Analyse des pics de messages
        $messagesParJour = \App\Models\Message::selectRaw('DATE(DateEnvoi) as jour, COUNT(*) as total')
            ->groupBy('jour')
            ->orderBy('jour', 'desc')
            ->limit(30)
            ->get();
        $totaux = $messagesParJour->pluck('total');
        if ($totaux->count() > 0) {
            $moyenne = $totaux->avg();
            $ecartType = sqrt($totaux->map(function($v) use ($moyenne) { return pow($v - $moyenne, 2); })->avg());
            foreach ($messagesParJour as $jour) {
                // Pic anormal si > moyenne + 2*écart-type
                if ($jour->total > $moyenne + 2 * $ecartType) {
                    $anomalies[] = [
                        'type' => 'PicMessages',
                        'jour' => $jour->jour,
                        'total' => $jour->total,
                        'moyenne' => $moyenne,
                        'ecartType' => $ecartType,
                    ];
                }
            }
        }
        // Analyse des pics de publications (produits ajoutés)
        $produitsParJour = \App\Models\Produit::selectRaw('DATE(DateAjout) as jour, COUNT(*) as total')
            ->groupBy('jour')
            ->orderBy('jour', 'desc')
            ->limit(30)
            ->get();
        $totauxProd = $produitsParJour->pluck('total');
        if ($totauxProd->count() > 0) {
            $moyenneProd = $totauxProd->avg();
            $ecartTypeProd = sqrt($totauxProd->map(function($v) use ($moyenneProd) { return pow($v - $moyenneProd, 2); })->avg());
            foreach ($produitsParJour as $jour) {
                if ($jour->total > $moyenneProd + 2 * $ecartTypeProd) {
                    $anomalies[] = [
                        'type' => 'PicProduits',
                        'jour' => $jour->jour,
                        'total' => $jour->total,
                        'moyenne' => $moyenneProd,
                        'ecartType' => $ecartTypeProd,
                    ];
                }
            }
        }
        return $anomalies;
        }

}