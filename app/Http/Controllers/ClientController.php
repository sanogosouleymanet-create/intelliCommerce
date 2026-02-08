<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Message;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->filled('recherche')) {
            $term = $request->recherche;
            $query->where('Nom', 'like', "%{$term}%")->orWhere('Prenom', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%");
        }

        $clients = $query->get();

        if ($request->ajax()) {
            return view('clients._main', compact('clients'))->render();
        }

        return view('clients.index', compact('clients'));
    }

    public function show($id)
    {
        $client = Client::where('idClient', $id)->firstOrFail();
        return view('clients.show', compact('client'));
    }

    public function FormulaireClient(Request $request)
    {
        // Validation basique
    $message = "Client enregistré avec succès.";
    $validated = $request->validate([
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'mail' => 'required|email|max:255',
        'motdepasse' => 'required|string|min:4|max:8',


    ]);
        $client = Client::create([
                    'Nom' => $request->nom,
                    'Prenom' => $request->prenom,
                    'DateDeNaissance' => $request->datenaissance ?? null,
                    'Adresse' => $request->adresse,
                    'TelClient' => $request->tel,
                    'email' => $request->mail,
                    'MotDePasse' => Hash::make($request->motdepasse),
                    'DateCreation' => now(),
        ]);

            // Connecte automatiquement le client créé et redirige vers la page principale
            Auth::guard('client')->login($client);
            $request->session()->regenerate();

            return redirect('/PagePrincipale');
    }

    /**
     * Récupère les messages d'une conversation spécifique avec un vendeur ou admin.
     */
    public function getConversation($type, $id)
    {
        $client = Auth::guard('client')->user();
        if (!$client) return response()->json(['error' => 'Non authentifié'], 401);

        if ($type === 'vendeur') {
            $target_id = $id;
            $target_column = 'Vendeur_idVendeur';
        } elseif ($type === 'admin') {
            $target_id = $id;
            $target_column = 'Administrateur_idAdministrateur';
        } else {
            return response()->json(['error' => 'Type invalide'], 400);
        }

        $messages = Message::with(['vendeur', 'administrateur'])
            ->where('Client_idClient', $client->idClient)
            ->where($target_column, $target_id)
            ->orderBy('DateEnvoi', 'asc')
            ->get();

        // Marquer comme lus
        foreach ($messages as $message) {
            if ($message->Statut === 'envoye') {
                $message->Statut = 'lu';
                $message->save();
            }
        }

        return response()->json($messages->map(function($m) use ($client) {
            return [
                'id' => $m->idMessage,
                'content' => $m->Contenu,
                'date' => $m->DateEnvoi->format('d/m/Y H:i'),
                'isOutgoing' => $m->sender_type === 'client',
            ];
        }));
    }

    /**
     * Envoie un message à un vendeur ou admin (nouveau ou réponse).
     */
    public function sendMessage(Request $request)
    {
        $client = Auth::guard('client')->user();
        if (!$client) return response()->json(['error' => 'Non authentifié'], 401);

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
            if ($type === 'vendeur') {
                $targetUser = \App\Models\Vendeur::find($id);
                $targetType = 'vendeur';
                $targetId = $id;
            } elseif ($type === 'admin') {
                $targetUser = \App\Models\Administrateur::find($id);
                $targetType = 'admin';
                $targetId = $id;
            } else {
                return response()->json(['success' => false, 'message' => 'Type de destinataire invalide.'], 400);
            }
            if (!$targetUser) {
                return response()->json(['success' => false, 'message' => 'Destinataire introuvable.'], 404);
            }
            if ($targetType === 'vendeur' && ($targetUser->Bloque ?? false)) {
                return response()->json(['success' => false, 'message' => 'Vous ne pouvez pas envoyer de message à ce vendeur.'], 422);
            }
        } else {
            // Assume it's an email for new message
            $vendeur = \App\Models\Vendeur::where('email', $recipient)->first();
            if (!$vendeur) {
                return response()->json(['success' => false, 'message' => 'Vendeur introuvable avec cet email.'], 404);
            }
            if (!empty($vendeur->Bloque)) {
                return response()->json(['success' => false, 'message' => 'Vous ne pouvez pas envoyer de message à ce vendeur.'], 422);
            }
            $targetUser = $vendeur;
            $targetType = 'vendeur';
            $targetId = $vendeur->idVendeur;
        }

        $m = new Message();
        $m->Contenu = trim($data['body']);
        $m->DateEnvoi = now();
        $m->Statut = 'envoye';
        $m->Client_idClient = $client->idClient;
        if ($targetType === 'vendeur') {
            $m->Vendeur_idVendeur = $targetId;
        } elseif ($targetType === 'admin') {
            $m->Administrateur_idAdministrateur = $targetId;
        }
        $m->sender_type = 'client';
        $m->save();

        return response()->json(['success' => true, 'message' => 'Message envoyé.']);
    }

    /**
     * Supprime un message spécifique.
     */
    public function deleteMessage(Request $request, $id)
    {
        $client = Auth::guard('client')->user();
        if (!$client) return response()->json(['error' => 'Non authentifié'], 401);

        $message = Message::where('idMessage', $id)
            ->where('Client_idClient', $client->idClient)
            ->first();

        if (!$message) {
            return response()->json(['success' => false, 'message' => 'Message introuvable'], 404);
        }

        $message->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Bloque un utilisateur (vendeur).
     */
    public function blockUser(Request $request, $type, $id)
    {
        $client = Auth::guard('client')->user();
        if (!$client) return response()->json(['error' => 'Non authentifié'], 401);

        if ($type === 'vendeur') {
            $vendeur = \App\Models\Vendeur::find($id);
                if ($vendeur) {
                    $vendeur->Bloque = true;
                    $vendeur->save();
                    return response()->json(['success' => true, 'message' => 'Vendeur bloqué.']);
                }
        }
        return response()->json(['success' => false, 'message' => 'Utilisateur introuvable.'], 404);
    }

    /**
     * Débloque un utilisateur (vendeur).
     */
    public function unblockUser(Request $request, $type, $id)
    {
        $client = Auth::guard('client')->user();
        if (!$client) return response()->json(['error' => 'Non authentifié'], 401);

        if ($type === 'vendeur') {
            $vendeur = \App\Models\Vendeur::find($id);
                if ($vendeur) {
                    $vendeur->Bloque = false;
                    $vendeur->save();
                    return response()->json(['success' => true, 'message' => 'Vendeur débloqué.']);
                }
        }
        return response()->json(['success' => false, 'message' => 'Utilisateur introuvable.'], 404);
    }

    /**
     * Affiche les détails d'une commande spécifique pour le client authentifié.
     */
    public function showCommande(Request $request, $id)
    {
        $client = Auth::guard('client')->user();
        if (!$client) {
            return redirect()->route('connexion');
        }

        $commande = \App\Models\Commande::with(['Produit', 'Client'])->where('idCommande', $id)->where('Client_idClient', $client->idClient)->first();

        if (!$commande) {
            abort(404, 'Commande introuvable ou accès non autorisé.');
        }

        if ($request->ajax()) {
            return view('clients.commande_detail', compact('commande', 'client'))->with('is_partial', true);
        }

        return view('PageClient', ['partial' => 'clients.commande_detail', 'client' => $client, 'commande' => $commande]);
    }
}
