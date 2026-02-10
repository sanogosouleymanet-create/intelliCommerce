<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Administrateur;
use App\Models\Produit;
use App\Models\Vendeur;
use App\Models\Client;
use App\Models\Ia_alerte;
use App\Models\Message;
use App\Models\Commande;
use App\Models\Produitcommande;
use Illuminate\Support\Facades\Storage;


class AdministrateurController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'motdepasse' => 'required|string',
        ]);

        $email = trim(strtolower($request->email));
        $pwd = trim($request->motdepasse);
        $admin = Administrateur::whereRaw('LOWER(email) = ?', [$email])->first();

        if ($admin) {
            $stored = $admin->MotDePasse;
            $isHashed = $stored && (preg_match('/^\\$2[aby]\\$|^\\$argon2/', $stored) === 1);

            if (Hash::check($pwd, $stored) || (!$isHashed && $stored === $pwd)) {
                if (!$isHashed && $stored === $pwd) {
                    $admin->MotDePasse = Hash::make($pwd);
                    $admin->save();
                }

                Auth::guard('administrateur')->login($admin);
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }
        }

        return back()->withErrors(['credentials' => 'Identifiants invalides'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('administrateur')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/PagePrincipale');
    }

    public function dashboard(Request $request)
    {
        $admin = Auth::guard('administrateur')->user();
        $counts = [
            'produits' => Produit::count(),
            'vendeurs' => Vendeur::count(),
            'clients' => Client::count(),
            'administrateurs' => Administrateur::count(),
            // Exclude IA alerts targeted to vendeurs — admin should not see vendor-specific alerts
            'ia_alertes' => Ia_alerte::where(function($q) {
                $q->where('destinataire_type', '!=', 'vendeur')
                  ->orWhereNull('destinataire_type');
            })->count(),
        ];
        // Count unread messages for this administrator (messages sent by others)
        try {
            $messagesUnread = Message::where('Administrateur_idAdministrateur', $admin->idAdministrateur)
                ->where('sender_type', '!=', 'administrateur')
                ->unread()
                ->count();
            $counts['messages_unread'] = $messagesUnread;
        } catch (\Throwable $e) {
            $counts['messages_unread'] = 0;
        }
        $client = \App\Models\Client::where('email', $admin->email)->first();
        $commandes = $client ? $client->commandes()->with(['produitcommandes.produit'])->orderBy('DateCommande', 'desc')->take(5)->get() : collect();
        return view('admin.dashboard', compact('counts', 'admin', 'commandes'));
    }

    public function iaAlerts()
    {
        // Only show alerts not targeted at vendeurs (i.e. admin/global alerts)
        // Eager-load source and destinataire to show message details when available
        $alerts = Ia_alerte::with(['source', 'destinataire'])->where(function($q) {
            $q->where('destinataire_type', '!=', 'vendeur')
              ->orWhereNull('destinataire_type');
        })->orderBy('DateCreation', 'desc')->get();
        return view('admin.ia_alertes', compact('alerts'));
    }

    /**
     * Affiche une alerte IA en détail pour l'administrateur.
     */
    public function showAlerte($id)
    {
        $alert = Ia_alerte::with(['source', 'destinataire'])->where('idAlerte', $id)->first();
        if (!$alert) {
            abort(404);
        }
        return view('admin.ia_alerte_show', compact('alert'));
    }

    /**
     * Supprime plusieurs alertes IA sélectionnées.
     */
    public function deleteAlerts(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array',
            'ids.*' => 'integer'
        ]);

        $ids = $request->input('ids', []);
        try {
            \App\Models\Ia_alerte::whereIn('idAlerte', $ids)->delete();
        } catch (\Throwable $e) {
            return back()->withErrors(['message' => 'Impossible de supprimer les alertes sélectionnées.']);
        }

        return redirect()->route('admin.ia_alertes')->with('status', 'Alertes supprimées');
    }
    

    public function produits(Request $request)
    {
        $query = \App\Models\Produit::with('vendeur');

        // quick search
        if ($request->filled('recherche')) {
            $term = $request->recherche;
            $query->where(function($q) use ($term) {
                $q->where('Nom', 'like', '%' . $term . '%')
                  ->orWhere('Description', 'like', '%' . $term . '%')
                  ->orWhere('Categorie', 'like', '%' . $term . '%');
            });
        }

        // periode filter
        if ($request->filled('periode')) {
            if ($request->periode === '24h') {
                $query->where('DateAjout', '>=', now()->subDay());
            } elseif ($request->periode === '7j') {
                $query->where('DateAjout', '>=', now()->subDays(7));
            } elseif ($request->periode === '30j') {
                $query->where('DateAjout', '>=', now()->subDays(30));
            }
        }

        // category
        if ($request->filled('categorie')) {
            $query->where('Categorie', $request->categorie);
        }

        // sort
        if ($request->filled('tri_prix')) {
            if ($request->tri_prix === 'asc') $query->orderBy('Prix', 'asc');
            elseif ($request->tri_prix === 'desc') $query->orderBy('Prix', 'desc');
            elseif ($request->tri_prix === 'recente') $query->orderBy('DateAjout', 'desc');
        } else {
            $query->orderBy('DateAjout', 'desc');
        }

        $produits = $query->get();
        return view('admin.produits', compact('produits'));
    }

    public function clients()
    {
        $clients = Client::orderBy('Nom')->get();
        return view('admin.clients', compact('clients'));
    }

    /**
     * Affiche la page détaillée d'un produit pour l'administrateur (partial ajax-compatible).
     */
    public function showProduit($id)
    {
        $produit = Produit::with('vendeur')->find($id);
        if (!$produit) return abort(404);
        $vendeur = $produit->vendeur ?? null;
        return view('admin.produits.show', compact('produit', 'vendeur'));
    }

    /**
     * Supprime un produit (action réservée aux administrateurs).
     */
    public function deleteProduit(Request $request, $id)
    {
        $produit = Produit::find($id);
        if (!$produit) return response()->json(['success' => false, 'message' => 'Produit introuvable'], 404);
        try{
            // delete image file if present
            if ($produit->Image) {
                try{ \Illuminate\Support\Facades\Storage::disk('public')->delete($produit->Image); } catch(\Throwable $e){}
            }
            $produit->delete();
        }catch(\Throwable $e){
            if ($request->ajax() || $request->wantsJson()) return response()->json(['success'=>false,'message'=>$e->getMessage()], 500);
            return back()->withErrors(['message' => 'Impossible de supprimer le produit']);
        }

        if ($request->ajax() || $request->wantsJson()) return response()->json(['success' => true]);
        return redirect()->route('admin.produits');
    }

    /**
     * Affiche la vue détaillée d'un client pour l'administrateur.
     */
    public function showClient($id)
    {
        $client = Client::find($id);
        if (!$client) {
            abort(404);
        }
        return view('admin.client_show', compact('client'));
    }

    /**
     * Toggle the 'active' flag for a client (enable/disable).
     */
    public function toggleClient(Request $request, $id)
    {
        $client = Client::find($id);
        if (!$client) return abort(404);
        // Ensure column exists; toggle value
        $client->active = !$client->active;
        $client->save();

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true, 'active' => (bool)$client->active]);
        }
        return redirect()->route('admin.clients');
    }

    /**
     * Delete a client from database.
     */
    public function deleteClient(Request $request, $id)
    {
        $client = Client::find($id);
        if (!$client) return abort(404);
        try{
            $client->delete();
        }catch(\Exception $e){
            if ($request->ajax() || $request->wantsJson()) return response()->json(['success'=>false,'message'=>$e->getMessage()], 500);
            return back()->withErrors(['message' => 'Impossible de supprimer le client']);
        }
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true]);
        }
        return redirect()->route('admin.clients');
    }

    public function vendeurs()
    {
        $vendeurs = Vendeur::orderBy('Nom')->get();
        return view('admin.vendeurs', compact('vendeurs'));
    }

    public function commandes(Request $request)
    {
        $query = \App\Models\Commande::with(['client', 'produitcommandes.produit.vendeur']);

        // Filter by status if provided (case-insensitive)
        if ($request->filled('statut')) {
            $statut = $request->statut;
            $query->whereRaw('LOWER(Statut) = ?', [mb_strtolower($statut)]);
        }

        // Search by client name or product name
        if ($request->filled('recherche')) {
            $term = $request->recherche;
            $query->where(function($q) use ($term) {
                $q->whereHas('client', function($clientQuery) use ($term) {
                    $clientQuery->where('Nom', 'like', '%' . $term . '%')
                               ->orWhere('Prenom', 'like', '%' . $term . '%');
                })
                ->orWhereHas('produitcommandes.produit', function($produitQuery) use ($term) {
                    $produitQuery->where('Nom', 'like', '%' . $term . '%');
                });
            });
        }

        $commandes = $query->orderBy('DateCommande', 'desc')->get();
        return view('admin.commandes', compact('commandes'));
    }

    public function mesCommandes(Request $request)
    {
        $admin = Auth::guard('administrateur')->user();
        if (!$admin) {
            return redirect()->route('admin.login')->withErrors('Veuillez vous connecter.');
        }
        $client = \App\Models\Client::where('email', $admin->email)->first();
        $commandes = $client ? $client->commandes()->with(['produitcommandes.produit'])->orderBy('DateCommande', 'desc')->get() : collect();
        return view('admin.mes-commandes', compact('commandes', 'admin'));
    }

    /**
     * Affiche les détails d'une commande pour l'administrateur.
     */
    public function showCommande($id)
    {
        $commande = \App\Models\Commande::with(['client', 'produitcommandes.produit.vendeur'])->find($id);
        if (!$commande) {
            return response()->json(['error' => 'Commande introuvable'], 404);
        }

        // Format the response to avoid null reference errors
        $formattedCommande = [
            'idCommande' => $commande->idCommande,
            'DateCommande' => $commande->DateCommande ? $commande->DateCommande->toISOString() : null,
            'montant_total' => $commande->montant_total,
            'Statut' => $commande->Statut,
            'client' => $commande->client ? [
                'Nom' => $commande->client->Nom,
                'Prenom' => $commande->client->Prenom,
                'email' => $commande->client->email,
                'TelClient' => $commande->client->TelClient,
                'Adresse' => $commande->client->Adresse,
            ] : null,
            'produitcommandes' => $commande->produitcommandes->map(function($pc) {
                return [
                    'Quantite' => $pc->Quantite,
                    'PrixUnitaire' => $pc->PrixUnitaire,
                    'produit' => $pc->produit ? [
                        'idProduit' => $pc->produit->idProduit ?? null,
                        'Nom' => $pc->produit->Nom ?? null,
                        'Image' => ($pc->produit->Image) ? Storage::url($pc->produit->Image) : null,
                    ] : null,
                ];
            })->toArray(),
        ];

        return response()->json($formattedCommande);
    }

    /**
     * Affiche la page détaillée d'un vendeur pour l'administrateur (partial ajax-compatible).
     */
    public function showVendeur($id)
    {
        $vendeur = Vendeur::find($id);
        if (!$vendeur) return abort(404);
        return view('admin.vendeur_show', compact('vendeur'));
    }

    /**
     * Supprime un vendeur (action réservée aux administrateurs).
     */
    public function deleteVendeur(Request $request, $id)
    {
        $vendeur = Vendeur::find($id);
        if (!$vendeur) return response()->json(['success' => false, 'message' => 'Vendeur introuvable'], 404);
        try{
            // Optionnel: supprimer image/avatar si stocké
            if (!empty($vendeur->Image)) {
                try{ \Illuminate\Support\Facades\Storage::disk('public')->delete($vendeur->Image); } catch(\Throwable $e){}
            }
            $vendeur->delete();
        }catch(\Throwable $e){
            if ($request->ajax() || $request->wantsJson()) return response()->json(['success'=>false,'message'=>$e->getMessage()], 500);
            return back()->withErrors(['message' => 'Impossible de supprimer le vendeur']);
        }

        if ($request->ajax() || $request->wantsJson()) return response()->json(['success' => true]);
        return redirect()->route('admin.vendeurs');
    }

    /**
     * Affiche la boite de réception des messages pour l'admin avec conversations groupées.
     */
    public function messages()
    {
        // Récupérer tous les messages avec relations
        $messages = Message::with(['client','vendeur','administrateur'])->orderBy('DateEnvoi', 'desc')->get();

        // Grouper les messages en conversations par expéditeur
        $conversations = [];
        foreach ($messages as $message) {
            $key = '';
            $sender = null;
            if ($message->client) {
                $key = 'client_' . $message->client->idClient;
                $sender = $message->client;
                $senderType = 'client';
            } elseif ($message->vendeur) {
                $key = 'vendeur_' . $message->vendeur->idVendeur;
                $sender = $message->vendeur;
                $senderType = 'vendeur';
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
                    'unreadCount' => $message->isUnread() ? 1 : 0,
                    'lastMessageDate' => $message->DateEnvoi,
                ];
            } elseif ($key) {
                // Mettre à jour le dernier message si plus récent
                if ($message->DateEnvoi > $conversations[$key]['lastMessageDate']) {
                    $conversations[$key]['lastMessage'] = $message;
                    $conversations[$key]['lastMessageDate'] = $message->DateEnvoi;
                }
                if ($message->isUnread()) {
                    $conversations[$key]['unreadCount']++;
                }
            }
        }

        // Trier les conversations par date du dernier message
        usort($conversations, function($a, $b) {
            return $b['lastMessageDate'] <=> $a['lastMessageDate'];
        });

        // Attach blocked status from sender model when available
        $conversations = collect($conversations)->map(function($conv){
            $sender = $conv['sender'] ?? null;
            $isBlocked = false;
            if ($sender) {
                // Many models may use PascalCase column 'Bloque'
                $isBlocked = isset($sender->Bloque) ? (bool)$sender->Bloque : false;
            }
            $conv['isBlocked'] = $isBlocked;
            return $conv;
        })->values();

        $clients = Client::orderBy('Nom')->get();
        $vendeurs = Vendeur::orderBy('Nom')->get();
        $admins = Administrateur::orderBy('Nom')->get();
        return view('admin.inbox', compact('conversations', 'clients','vendeurs','admins'));
    }

    /**
     * Récupère les messages d'une conversation spécifique.
     */
    public function getConversation($type, $id)
    {
        if (!in_array($type, ['client', 'vendeur', 'admin'])) {
            return response()->json(['error' => 'Type invalide'], 400);
        }

        $admin = Auth::guard('administrateur')->user();

        $messages = Message::with(['client','vendeur','administrateur'])
            ->where(function($query) use ($type, $id) {
                if ($type === 'client') {
                    $query->where('Client_idClient', $id);
                } elseif ($type === 'vendeur') {
                    $query->where('Vendeur_idVendeur', $id);
                } elseif ($type === 'admin') {
                    $query->where('Administrateur_idAdministrateur', $id);
                }
            })
            ->orderBy('DateEnvoi', 'asc')
            ->get();

        // Marquer comme lus uniquement pour les messages entrants non lus
        foreach ($messages as $message) {
            $isFromOther = (isset($message->sender_type) && $message->sender_type !== 'administrateur')
                || (!isset($message->sender_type) && (($message->Client_idClient ?? null) !== null || ($message->Vendeur_idVendeur ?? null) !== null));

            if ($isFromOther && $message->isUnread()) {
                $message->markAsRead();
            }
        }

        return response()->json($messages->map(function($m) use ($admin) {
            // provide sender_type and sender_id to make client-side alignment robust
            $senderType = $m->sender_type ?? null;
            $senderId = $m->Administrateur_idAdministrateur ?? ($m->Vendeur_idVendeur ?? $m->Client_idClient);
            return [
                'id' => $m->idMessage,
                'content' => $m->Contenu,
                'date' => $m->DateEnvoi->format('d/m/Y H:i'),
                'isOutgoing' => ($m->Administrateur_idAdministrateur == $admin->idAdmi),
                'sender_type' => $senderType,
                'sender_id' => $senderId,
            ];
        }));
    }

    /**
     * Supprime un message spécifique.
     */
    public function deleteMessage(Request $request, $id)
    {
        $message = Message::find($id);
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
        if (!in_array($type, ['client', 'vendeur', 'admin'])) {
            return response()->json(['error' => 'Type invalide'], 400);
        }

        $query = Message::query();
        if ($type === 'client') {
            $query->where('Client_idClient', $id);
        } elseif ($type === 'vendeur') {
            $query->where('Vendeur_idVendeur', $id);
        } elseif ($type === 'admin') {
            $query->where('Administrateur_idAdministrateur', $id);
        }
        $query->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Affiche la page des paramètres du site pour l'administrateur.
     */
    public function parametres()
    {
        $admin = Auth::guard('administrateur')->user();
        return view('admin.parametres', compact('admin'));
    }

    /**
     * Met à jour les paramètres du site.
     */
    public function updateSettings(Request $request)
    {
        $admin = Auth::guard('administrateur')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }

        $data = $request->only(['site_name', 'site_description', 'contact_email', 'contact_phone', 'address']);
        $rules = [
            'site_name' => 'nullable|string|max:255',
            'site_description' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Save settings to database
        foreach ($data as $key => $value) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()->with('status', 'Paramètres du site mis à jour');
    }

    /**
     * Envoie un message (supporte destinataires uniques ou groupés).
     */
    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'recipient_type' => 'required|string|in:single,clients,vendeurs,admins,all',
            'recipient' => 'nullable|string',
            'subject' => 'nullable|string|max:191',
            'body' => 'required|string'
        ]);

        $subject = $data['subject'] ?? null;
        $content = trim(($subject ? $subject."\n\n" : '') . $data['body']);
        $now = now();

        $created = 0;

        // current administrator who sends the message
        $sender = Auth::guard('administrateur')->user();

        if ($data['recipient_type'] === 'single') {
            if (empty($data['recipient'])) {
                return response()->json(['success' => false, 'message' => 'Destinataire requis pour un envoi unique.'], 422);
            }
            $parts = explode(':', $data['recipient']);
            $type = $parts[0] ?? '';
            $id = isset($parts[1]) ? intval($parts[1]) : 0;
            if ($type === 'client' && $id) {
                // create message for conversation with client and mark sender as current admin
                $client = Client::find($id);
                if (!$client) return response()->json(['success' => false, 'message' => 'Client introuvable.'], 404);
                if (!empty($client->Bloque)) {
                    return response()->json(['success' => false, 'message' => 'Impossible d\'envoyer : destinataire bloqué.'], 422);
                }
                $m = new Message();
                $m->Contenu = $content;
                $m->DateEnvoi = $now;
                $m->Statut = 'non lu';
                $m->Client_idClient = $id;
                if ($sender) $m->Administrateur_idAdministrateur = $sender->idAdmi;
                $m->save();
                $created = 1;
            } elseif ($type === 'vendeur' && $id) {
                $vendeur = Vendeur::find($id);
                if (!$vendeur) return response()->json(['success' => false, 'message' => 'Vendeur introuvable.'], 404);
                if (!empty($vendeur->Bloque)) {
                    return response()->json(['success' => false, 'message' => 'Impossible d\'envoyer : destinataire bloqué.'], 422);
                }
                $m = new Message();
                $m->Contenu = $content;
                $m->DateEnvoi = $now;
                $m->Statut = 'non lu';
                $m->Vendeur_idVendeur = $id;
                if ($sender) $m->Administrateur_idAdministrateur = $sender->idAdmi;
                $m->save();
                $created = 1;
            } elseif ($type === 'admin' && $id) {
                // Sending to another admin: keep recording under recipient admin (legacy behavior)
                $m = new Message();
                $m->Contenu = $content;
                $m->DateEnvoi = $now;
                $m->Statut = 'non lu';
                $m->Administrateur_idAdministrateur = $id;
                $m->save();
                $created = 1;
            } else {
                return response()->json(['success' => false, 'message' => 'Destinataire invalide.'], 422);
            }
        } else {
            if ($data['recipient_type'] === 'clients' || $data['recipient_type'] === 'all') {
                $clients = Client::all();
                foreach ($clients as $c) {
                    if (!empty($c->Bloque)) continue; // skip blocked recipients
                $m = new Message();
                $m->Contenu = $content;
                $m->DateEnvoi = $now;
                $m->Statut = 'non lu';
                $m->Client_idClient = $c->idClient;
                if ($sender) $m->Administrateur_idAdministrateur = $sender->idAdmi;
                $m->save();
                    $created++;
                }
            }
            if ($data['recipient_type'] === 'vendeurs' || $data['recipient_type'] === 'all') {
                $vendeurs = Vendeur::all();
                foreach ($vendeurs as $v) {
                    if (!empty($v->Bloque)) continue; // skip blocked recipients
                    $m = new Message();
                    $m->Contenu = $content;
                    $m->DateEnvoi = $now;
                    $m->Statut = 'non lu';
                    $m->Vendeur_idVendeur = $v->idVendeur;
                    if ($sender) $m->Administrateur_idAdministrateur = $sender->idAdmi;
                    $m->save();
                    $created++;
                }
            }
            if ($data['recipient_type'] === 'admins' || $data['recipient_type'] === 'all') {
                $admins = Administrateur::all();
                foreach ($admins as $a) {
                    // keep existing behaviour for admin-targeted messages
                    $m = new Message();
                    $m->Contenu = $content;
                    $m->DateEnvoi = $now;
                    $m->Statut = 'non lu';
                    $m->Administrateur_idAdministrateur = $a->idAdmi;
                    $m->save();
                    $created++;
                }
            }
        }

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true, 'created' => $created, 'message' => 'Message envoyé.']);
        }
        return redirect()->route('admin.messages')->with('status', 'Message envoyé');
    }

    /**
     * Block a user (client or vendeur) so admin can manage conversations.
     */
    public function blockUser(Request $request, $type, $id)
    {
        if (!in_array($type, ['client', 'vendeur'])) {
            return response()->json(['error' => 'Type invalide'], 400);
        }
        if ($type === 'client') {
            $model = Client::find($id);
        } else {
            $model = Vendeur::find($id);
        }
        if (!$model) return response()->json(['error' => 'Utilisateur introuvable'], 404);
        $model->Bloque = true;
        $model->save();
        return response()->json(['success' => true]);
    }

    /**
     * Unblock a user.
     */
    public function unblockUser(Request $request, $type, $id)
    {
        if (!in_array($type, ['client', 'vendeur'])) {
            return response()->json(['error' => 'Type invalide'], 400);
        }
        if ($type === 'client') {
            $model = Client::find($id);
        } else {
            $model = Vendeur::find($id);
        }
        if (!$model) return response()->json(['error' => 'Utilisateur introuvable'], 404);
        $model->Bloque = false;
        $model->save();
        return response()->json(['success' => true]);
    }

    /**
     * Show admin's cart page.
     */
    public function cart(Request $request)
    {
        $admin = Auth::guard('administrateur')->user();
        $key = 'cart_admin_' . $admin->idAdmi;
        $cart = session($key, []);
        $items = [];
        $total = 0;
        foreach ($cart as $id => $qty) {
            $p = Produit::where('idProduit', $id)->first();
            if (!$p) continue;
            $subtotal = ($p->Prix ?? 0) * $qty;
            $items[] = ['produit' => $p, 'qty' => $qty, 'subtotal' => $subtotal];
            $total += $subtotal;
        }
        return view('admin.cart', compact('items', 'total', 'admin'));
    }

    /**
     * Add a product to admin's session cart (AJAX-friendly).
     */
    public function addToCart(Request $request)
    {
        $admin = Auth::guard('administrateur')->user();
        if (!$admin) return response()->json(['success' => false, 'message' => 'Non authentifié'], 401);
        $id = $request->input('id');
        $qty = max(1, intval($request->input('qty', 1)));
        $key = 'cart_admin_' . $admin->idAdmi;
        $cart = session($key, []);
        if (isset($cart[$id])) $cart[$id] = $cart[$id] + $qty; else $cart[$id] = $qty;
        session([$key => $cart]);

        // compute totals
        $count = array_sum(array_values($cart));
        $total = 0;
        foreach ($cart as $pid => $q) {
            $p = Produit::where('idProduit', $pid)->first();
            if ($p) $total += ($p->Prix ?? 0) * $q;
        }

        if ($request->ajax() || $request->wantsJson()) return response()->json(['success' => true, 'count' => $count, 'total' => $total]);
        return redirect()->back();
    }

    /**
     * Remove a product from admin's session cart.
     */
    public function removeFromCart(Request $request)
    {
        $admin = Auth::guard('administrateur')->user();
        if (!$admin) return response()->json(['success' => false, 'message' => 'Non authentifié'], 401);
        $id = $request->input('id');
        $key = 'cart_admin_' . $admin->idAdmi;
        $cart = session($key, []);
        if (isset($cart[$id])) unset($cart[$id]);
        session([$key => $cart]);

        $count = array_sum(array_values($cart));
        $total = 0; foreach ($cart as $pid => $q) { $p = Produit::where('idProduit', $pid)->first(); if ($p) $total += ($p->Prix ?? 0) * $q; }

        if ($request->ajax() || $request->wantsJson()) return response()->json(['success' => true, 'count' => $count, 'total' => $total]);
        return redirect()->back();
    }

    /**
     * Update quantity for a product in admin's cart.
     */
    public function updateCart(Request $request)
    {
        $admin = Auth::guard('administrateur')->user();
        if (!$admin) return response()->json(['success' => false, 'message' => 'Non authentifié'], 401);
        $id = $request->input('id');
        $qty = intval($request->input('qty', 0));
        $key = 'cart_admin_' . $admin->idAdmi;
        $cart = session($key, []);
        if ($qty <= 0) { if (isset($cart[$id])) unset($cart[$id]); }
        else { $cart[$id] = $qty; }
        session([$key => $cart]);

        $count = array_sum(array_values($cart));
        $total = 0; foreach ($cart as $pid => $q) { $p = Produit::where('idProduit', $pid)->first(); if ($p) $total += ($p->Prix ?? 0) * $q; }

        if ($request->ajax() || $request->wantsJson()) return response()->json(['success' => true, 'count' => $count, 'total' => $total]);
        return redirect()->back();
    }

    /**
     * Place an order for selected products.
     */
    public function placeOrder(Request $request)
    {
        $admin = Auth::guard('administrateur')->user();
        if (!$admin) return response()->json(['success' => false, 'message' => 'Non authentifié'], 401);
        $selected = $request->input('selected_products', []);
        $key = 'cart_admin_' . $admin->idAdmi;
        $cart = session($key, []);
        if (empty($selected)) return response()->json(['success' => false, 'message' => 'Aucun produit sélectionné'], 422);

        // Find or create client record for admin
        $client = Client::where('email', $admin->email)->first();
        if (!$client) {
            $client = Client::create([
                'Nom' => $admin->Nom,
                'Prenom' => $admin->Prenom ?? '',
                'email' => $admin->email,
                'MotDePasse' => $admin->MotDePasse,
                'TelClient' => $admin->TelAdmin ?? '',
                'Adresse' => $admin->Adresse ?? '',
                'active' => true,
            ]);
        }

        $totalAmount = 0;
        $orderItems = [];

        // Calculate total and prepare order items.
        // Allow selected product ids even if they are not present in the session cart (fallback to qty = 1).
        foreach ($selected as $productId) {
            $productId = intval($productId);
            $product = Produit::find($productId);
            if (!$product) continue;

            $qty = isset($cart[$productId]) ? intval($cart[$productId]) : 1;

            $subtotal = ($product->Prix ?? 0) * $qty;
            $totalAmount += $subtotal;

            $orderItems[] = [
                'product' => $product,
                'qty' => $qty,
                'prix_unitaire' => $product->Prix,
                'vendeur_id' => $product->Vendeur_idVendeur,
            ];
        }

        if (empty($orderItems)) {
            // Return debug info to help identify why selected ids don't match session cart
            return response()->json([
                'success' => false,
                'message' => 'Aucun produit valide sélectionné',
                'selected' => $selected,
                'cart' => $cart,
                'admin_id' => $admin->idAdmi ?? null,
                'session_id' => session()->getId(),
                'session_key' => $key
            ], 422);
        }

        // Create the order (use the column name defined in migrations: MontantTotal)
        $commande = Commande::create([
            'DateCommande' => now(),
            'Statut' => 'En cours',
            'MontantTotal' => $totalAmount,
            'Client_idClient' => $client->idClient,
            'Vendeur_idVendeur' => $orderItems[0]['vendeur_id'], // Use first product's seller
        ]);

        // Create produitcommande records
        foreach ($orderItems as $item) {
            Produitcommande::create([
                'Produit_idProduit' => $item['product']->idProduit,
                'Commande_idCommande' => $commande->idCommande,
                'Quantite' => $item['qty'],
                'PrixUnitaire' => $item['prix_unitaire'],
                'DateAjout' => now(),
            ]);
        }

        // Keep items in cart after placing order
        $count = array_sum(array_values($cart));
        $total = 0;
        foreach ($cart as $pid => $q) {
            $p = Produit::where('idProduit', $pid)->first();
            if ($p) $total += ($p->Prix ?? 0) * $q;
        }

        return response()->json([
            'success' => true,
            'message' => 'Commande passée avec succès.',
            'order_id' => $commande->idCommande,
            'count' => $count,
            'total' => $total
        ]);
    }
}
