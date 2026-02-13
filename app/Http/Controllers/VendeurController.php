<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendeur;
use App\Models\Message;
use App\Models\Client;
use App\Models\Administrateur;
use App\Models\Commande;

class VendeurController extends Controller
{
    public function index(Request $request)
    {
        $vendeurs = Vendeur::all();
        if ($request->ajax()) {
            return view('vendeurs._main', compact('vendeurs'))->render();
        }
        return view('vendeurs.index', compact('vendeurs'));
    }


    public function FormulaireVendeur(Request $request)
    {
       // Validation basique
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'nomboutique' => 'required|string|max:255',
            'mail' => 'required|email|max:255',
            'motdepasse' => 'required|string|min:4|max:8',
        ]);
        $vend = Vendeur::create([
            'Nom' => $request->nom,
            'Prenom' => $request->prenom,
            'Adresse' => $request->adresse,
            'TelVendeur' => $request->tel,
            'email' => $request->mail,
            'NomBoutique' => $request->nomboutique,
            'MotDePasse' => Hash::make($request->motdepasse),
            'DateCreation' => now(),
        ]);
        // Ne pas créer automatiquement de compte `Client` ici.

            // Connecte automatiquement le vendeur créé et redirige vers son tableau de bord
            Auth::guard('vendeur')->login($vend);
            $request->session()->regenerate();

            return redirect('/PagePrincipale');
    }

    public function parametres(Request $request)
    {
        $vendeur = Auth::guard('vendeur')->user();

        // Detect AJAX/partial requests and return only the partial when appropriate
        $isAjax = $request->header('X-Requested-With') === 'XMLHttpRequest' || $request->ajax() || $request->wantsJson();

        if ($isAjax) {
            return view('vendeurs.parametres', compact('vendeur'));
        }

        // Full page request -> render PageVendeur with the parametres partial
        return view('PageVendeur', [
            'partial' => 'vendeurs.parametres',
            'vendeur' => $vendeur,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $vendeur = Auth::guard('vendeur')->user();
        if (!$vendeur) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 401);
        }
        // Base rules for profile fields
        $rules = [
            'NomBoutique' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:vendeurs,email,' . $vendeur->idVendeur . ',idVendeur',
            'Nom' => 'nullable|string|max:255',
            'Prenom' => 'nullable|string|max:255',
            'Adresse' => 'nullable|string|max:500',
            'TelVendeur' => 'nullable|string|max:50',
        ];

        // If user is changing password, require and validate password fields
        if ($request->filled('new_password')) {
            $rules['current_password'] = 'required|string';
            $rules['new_password'] = 'required|string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        // If changing password, verify current password
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password ?? '', $vendeur->MotDePasse)) {
                return response()->json(['success' => false, 'message' => 'Mot de passe actuel incorrect.'], 422);
            }
            $vendeur->MotDePasse = Hash::make($request->new_password);
        }

        $vendeur->fill([
            'NomBoutique' => $validated['NomBoutique'] ?? $vendeur->NomBoutique,
            'email' => $validated['email'] ?? $vendeur->email,
            'Nom' => $validated['Nom'] ?? $vendeur->Nom,
            'Prenom' => $validated['Prenom'] ?? $vendeur->Prenom,
            'Adresse' => $validated['Adresse'] ?? $vendeur->Adresse,
            'TelVendeur' => $validated['TelVendeur'] ?? $vendeur->TelVendeur,
        ]);

        $vendeur->save();

        return response()->json(['success' => true, 'message' => 'Paramètres mis à jour', 'vendeur' => $vendeur]);
    }

    /**
     * Affiche la page des messages pour le vendeur avec conversations groupées.
     */
    public function messages(Request $request)
    {
        $vendeur = Auth::guard('vendeur')->user();
        if (!$vendeur) {
            return redirect()->route('connexion');
        }

        // Récupérer tous les messages avec relations
        $messages = Message::with(['client','vendeur','administrateur'])
            ->where('Vendeur_idVendeur', $vendeur->idVendeur)
            ->orderBy('DateEnvoi', 'desc')
            ->get();

        // Grouper les messages en conversations par expéditeur
        $conversations = [];
        foreach ($messages as $message) {
            $key = '';
            $sender = null;
            if ($message->client) {
                $key = 'client_' . $message->client->idClient;
                $sender = $message->client;
                $senderType = 'client';
            } elseif ($message->administrateur) {
                $key = 'admin_' . $message->administrateur->idAdmi;
                $sender = $message->administrateur;
                $senderType = 'admin';
            }

            if ($key && !isset($conversations[$key])) {
                $conversations[$key] = [
                    'sender' => $sender,
                    'senderType' => $senderType,
                    'lastMessage' => $message,
                    'unreadCount' => 0,
                    'lastMessageDate' => $message->DateEnvoi,
                ];
            } elseif ($key) {
                // Mettre à jour le dernier message si plus récent
                if ($message->DateEnvoi > $conversations[$key]['lastMessageDate']) {
                    $conversations[$key]['lastMessage'] = $message;
                    $conversations[$key]['lastMessageDate'] = $message->DateEnvoi;
                }
            }
        }

        // Calculer le nombre de messages non lus pour chaque conversation
        foreach ($conversations as $key => &$conv) {
            $query = Message::where('Vendeur_idVendeur', $vendeur->idVendeur);
            if ($conv['senderType'] === 'client') {
                $query->where('Client_idClient', $conv['sender']->idClient);
            } elseif ($conv['senderType'] === 'admin') {
                $query->where('Administrateur_idAdministrateur', $conv['sender']->idAdmi);
            }
            $conv['unreadCount'] = $query->where('sender_type', '!=', 'vendeur')->unread()->count();
        }

        // Trier les conversations par date du dernier message
        usort($conversations, function($a, $b) {
            return $b['lastMessageDate'] <=> $a['lastMessageDate'];
        });

        // Attach blocked status from sender model when available (DB column is `Bloque`)
        $conversations = collect($conversations)->map(function($conv){
            $sender = $conv['sender'] ?? null;
            $isBlocked = false;
            if ($sender && isset($sender->Bloque)) {
                $isBlocked = (bool)$sender->Bloque;
            }
            $conv['isBlocked'] = $isBlocked;
            return $conv;
        })->values();

        // Detect AJAX/partial requests and return only the partial when appropriate
        $isAjax = $request->header('X-Requested-With') === 'XMLHttpRequest' || $request->ajax() || $request->wantsJson();

        if ($isAjax) {
            return view('vendeurs.messages', compact('vendeur', 'conversations'));
        }

        // Full page request -> render PageVendeur with the messages partial
        return view('PageVendeur', [
            'partial' => 'vendeurs.messages',
            'vendeur' => $vendeur,
            'conversations' => $conversations,
        ]);
    }

    /**
     * Récupère les messages d'une conversation spécifique avec un client ou admin.
     */
    public function getConversation($type, $id)
    {
        $vendeur = Auth::guard('vendeur')->user();
        if (!$vendeur) return response()->json(['error' => 'Non authentifié'], 401);

        if ($type === 'client') {
            $target_id = $id;
            $target_column = 'Client_idClient';
        } elseif ($type === 'admin') {
            $target_id = $id;
            $target_column = 'Administrateur_idAdministrateur';
        } else {
            return response()->json(['error' => 'Type invalide'], 400);
        }

        $messages = Message::with(['client', 'administrateur'])
            ->where('Vendeur_idVendeur', $vendeur->idVendeur)
            ->where($target_column, $target_id)
            ->orderBy('DateEnvoi', 'asc')
            ->get();

        // Marquer comme lus uniquement pour les messages entrants non lus
        foreach ($messages as $message) {
            $isFromOther = (isset($message->sender_type) && $message->sender_type !== 'vendeur')
                || (!isset($message->sender_type) && ($message->Client_idClient ?? null) !== null);

            if ($isFromOther && $message->isUnread()) {
                $message->markAsRead();
            }
        }

        return response()->json($messages->map(function($m) use ($vendeur) {
            return [
                'id' => $m->idMessage,
                'content' => $m->Contenu,
                'date' => $m->DateEnvoi->format('d/m/Y H:i'),
                'isOutgoing' => $m->sender_type === 'vendeur',
            ];
        }));
    }

    /**
     * Envoie un message à un client ou admin (nouveau ou réponse).
     */
    public function sendMessage(Request $request)
    {
        $vendeur = Auth::guard('vendeur')->user();
        if (!$vendeur) return response()->json(['error' => 'Non authentifié'], 401);

        $data = $request->validate([
            'recipient' => 'required|string',
            'body' => 'required|string',
            'subject' => 'nullable|string'
        ]);

        $recipient = $data['recipient'];
        $targetUser = null;
        $targetType = null;
        $targetId = null;

        // Check if recipient is in type:id format (for replies)
        if (strpos($recipient, ':') !== false) {
            list($type, $id) = explode(':', $recipient, 2);
            if ($type === 'client') {
                $targetUser = Client::find($id);
                $targetType = 'client';
                $targetId = $id;
            } elseif ($type === 'admin') {
                $targetUser = Administrateur::find($id);
                $targetType = 'admin';
                $targetId = $id;
            } else {
                return response()->json(['success' => false, 'message' => 'Type de destinataire invalide.'], 400);
            }
            if (!$targetUser) {
                return response()->json(['success' => false, 'message' => 'Destinataire introuvable.'], 404);
            }
            if ($targetType === 'client' && ($targetUser->Bloque ?? false)) {
                return response()->json(['success' => false, 'message' => 'Vous ne pouvez pas envoyer de message à ce client.'], 422);
            }
        } else {
            // Assume it's an email for new message
            $client = Client::where('email', $recipient)->first();
            if (!$client) {
                return response()->json(['success' => false, 'message' => 'Client introuvable avec cet email.'], 404);
            }
            if (!empty($client->Bloque)) {
                return response()->json(['success' => false, 'message' => 'Vous ne pouvez pas envoyer de message à ce client.'], 422);
            }
            $targetUser = $client;
            $targetType = 'client';
            $targetId = $client->idClient;
        }

        $m = new Message();
        $m->Contenu = trim($data['body']);
        $m->DateEnvoi = now();
        // Store as 'non lu' so recipient sees it as unread until opened
        $m->Statut = 'non lu';
        $m->Vendeur_idVendeur = $vendeur->idVendeur;
        if ($targetType === 'client') {
            $m->Client_idClient = $targetId;
        } elseif ($targetType === 'admin') {
            $m->Administrateur_idAdministrateur = $targetId;
        }
        $m->sender_type = 'vendeur';
        $m->save();

        return response()->json(['success' => true, 'message' => 'Message envoyé.']);
    }

    /**
     * Supprime un message spécifique.
     */
    public function deleteMessage(Request $request, $id)
    {
        $vendeur = Auth::guard('vendeur')->user();
        if (!$vendeur) return response()->json(['error' => 'Non authentifié'], 401);

        $message = Message::where('idMessage', $id)
            ->where('Vendeur_idVendeur', $vendeur->idVendeur)
            ->first();

        if (!$message) {
            return response()->json(['success' => false, 'message' => 'Message introuvable'], 404);
        }

        $message->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Supprime une conversation entière (tous les messages d'un expéditeur).
     */
    public function deleteConversation(Request $request, $type, $id)
    {
        $vendeur = Auth::guard('vendeur')->user();
        if (!$vendeur) return response()->json(['error' => 'Non authentifié'], 401);

        if (!in_array($type, ['client', 'admin'])) {
            return response()->json(['error' => 'Type invalide'], 400);
        }

        $query = Message::where('Vendeur_idVendeur', $vendeur->idVendeur);
        if ($type === 'client') {
            $query->where('Client_idClient', $id);
        } elseif ($type === 'admin') {
            $query->where('Administrateur_idAdministrateur', $id);
        }
        $query->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Bloque un utilisateur (client).
     */
    public function blockUser(Request $request, $type, $id)
    {
        $vendeur = Auth::guard('vendeur')->user();
        if (!$vendeur) return response()->json(['error' => 'Non authentifié'], 401);

        if ($type === 'client') {
            $client = Client::find($id);
            if ($client) {
                $client->Bloque = true;
                $client->save();
                return response()->json(['success' => true, 'message' => 'Client bloqué.']);
            }
        }
        return response()->json(['success' => false, 'message' => 'Utilisateur introuvable.'], 404);
    }

    /**
     * Débloque un utilisateur (client).
     */
    public function unblockUser(Request $request, $type, $id)
    {
        $vendeur = Auth::guard('vendeur')->user();
        if (!$vendeur) return response()->json(['error' => 'Non authentifié'], 401);

        if ($type === 'client') {
            $client = Client::find($id);
            if ($client) {
                $client->Bloque = false;
                $client->save();
                return response()->json(['success' => true, 'message' => 'Client débloqué.']);
            }
        }
        return response()->json(['success' => false, 'message' => 'Utilisateur introuvable.'], 404);
    }

    /**
     * Marquer une commande comme livrée.
     */
    public function markDelivered(Request $request, $id)
    {
        $vendeur = Auth::guard('vendeur')->user();
        if (!$vendeur) return response()->json(['success' => false, 'message' => 'Non authentifié'], 401);

        $commande = Commande::where('idCommande', $id)->first();
        if (!$commande) {
            return response()->json(['success' => false, 'message' => 'Commande introuvable.'], 404);
        }

        // Vérifier si le vendeur possède au moins un produit dans cette commande
        $vendeurOwnsProduct = $commande->Produit()->where('Vendeur_idVendeur', $vendeur->idVendeur)->exists();
        if (!$vendeurOwnsProduct) {
            return response()->json(['success' => false, 'message' => 'Vous n\'êtes pas autorisé à modifier cette commande.'], 403);
        }

        // Mettre à jour le statut
        $commande->Statut = 'Livrée';
        $commande->save();

        return response()->json(['success' => true, 'message' => 'Commande marquée comme livrée.']);
    }

    /**
     * Supprimer une commande.
     */
    public function deleteCommande(Request $request, $id)
    {
        $vendeur = Auth::guard('vendeur')->user();
        if (!$vendeur) return response()->json(['success' => false, 'message' => 'Non authentifié'], 401);

        $commande = Commande::where('idCommande', $id)->first();
        if (!$commande) {
            return response()->json(['success' => false, 'message' => 'Commande introuvable.'], 404);
        }

        // Vérifier si le vendeur possède au moins un produit dans cette commande
        $vendeurOwnsProduct = $commande->Produit()->where('Vendeur_idVendeur', $vendeur->idVendeur)->exists();
        if (!$vendeurOwnsProduct) {
            return response()->json(['success' => false, 'message' => 'Vous n\'êtes pas autorisé à supprimer cette commande.'], 403);
        }

        // Delete related Produitcommande records
        \App\Models\Produitcommande::where('Commande_idCommande', $commande->idCommande)->delete();

        // Delete the commande
        $commande->delete();

        return response()->json(['success' => true, 'message' => 'Commande supprimée avec succès.']);
    }

    /**
     * Supprimer une commande du vendeur (en tant que client).
     */
    public function deleteMesCommande(Request $request, $id)
    {
        $vendeur = Auth::guard('vendeur')->user();
        if (!$vendeur) {
            return redirect()->route('connexion');
        }

        // Trouver le compte client associé au vendeur
        $client = Client::where('email', $vendeur->email)->first();
        if (!$client) {
            abort(404, 'Compte client introuvable.');
        }

        $commande = Commande::where('idCommande', $id)->where('Client_idClient', $client->idClient)->first();
        if (!$commande) {
            abort(404, 'Commande introuvable.');
        }

        // Delete related Produitcommande records
        \App\Models\Produitcommande::where('Commande_idCommande', $commande->idCommande)->delete();

        // Delete the commande
        $commande->delete();

        return redirect()->back();
    }

    /**
     * Affiche la page détaillée d'un client pour le vendeur (seulement si le client a acheté chez ce vendeur).
     */
    public function showClient($id)
    {
        $vendeur = Auth::guard('vendeur')->user();
        if (!$vendeur) {
            return redirect()->route('connexion');
        }

        $client = Client::whereHas('commandes', function($q) use ($vendeur) {
            $q->where('Statut', 'Livrée')->whereHas('Produit', function($p) use ($vendeur) {
                $p->where('Vendeur_idVendeur', $vendeur->idVendeur);
            });
        })->find($id);

        if (!$client) {
            abort(404, 'Client introuvable ou accès non autorisé.');
        }

        // Detect AJAX/partial requests and return only the partial when appropriate
        $isAjax = request()->header('X-Requested-With') === 'XMLHttpRequest' || request()->ajax() || request()->wantsJson();

        if ($isAjax) {
            return view('vendeurs.client_show', compact('vendeur', 'client'));
        }

        // Full page request -> render PageVendeur with the client_show partial
        return view('PageVendeur', [
            'partial' => 'vendeurs.client_show',
            'vendeur' => $vendeur,
            'client' => $client
        ]);
    }
}
