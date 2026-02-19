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


}