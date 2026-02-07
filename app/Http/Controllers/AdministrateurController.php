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
            'ia_alertes' => Ia_alerte::count(),
        ];
        return view('admin.dashboard', compact('counts', 'admin'));
    }

    public function iaAlerts()
    {
        $alerts = Ia_alerte::orderBy('DateCreation', 'desc')->get();
        return view('admin.ia_alertes', compact('alerts'));
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
                    'unreadCount' => $message->Statut === 'envoye' ? 1 : 0,
                    'lastMessageDate' => $message->DateEnvoi,
                ];
            } elseif ($key) {
                // Mettre à jour le dernier message si plus récent
                if ($message->DateEnvoi > $conversations[$key]['lastMessageDate']) {
                    $conversations[$key]['lastMessage'] = $message;
                    $conversations[$key]['lastMessageDate'] = $message->DateEnvoi;
                }
                if ($message->Statut === 'envoye') {
                    $conversations[$key]['unreadCount']++;
                }
            }
        }

        // Trier les conversations par date du dernier message
        usort($conversations, function($a, $b) {
            return $b['lastMessageDate'] <=> $a['lastMessageDate'];
        });

        $conversations = collect($conversations);

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

        // Marquer comme lus
        foreach ($messages as $message) {
            if ($message->Statut === 'envoye') {
                $message->Statut = 'lu';
                $message->save();
            }
        }

        return response()->json($messages->map(function($m) use ($admin) {
            return [
                'id' => $m->idMessage,
                'content' => $m->Contenu,
                'date' => $m->DateEnvoi->format('d/m/Y H:i'),
                'isOutgoing' => $m->Administrateur_idAdministrateur == $admin->idAdmi,
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
     * Affiche la page des paramètres pour l'administrateur.
     */
    public function parametres()
    {
        $admin = Auth::guard('administrateur')->user();
        return view('admin.parametres', compact('admin'));
    }

    /**
     * Met à jour les paramètres de l'administrateur.
     */
    public function updateSettings(Request $request)
    {
        $admin = Auth::guard('administrateur')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }

        $data = $request->only(['email', 'Nom', 'Prenom', 'current_password', 'new_password', 'new_password_confirmation']);
        $rules = [
            'email' => 'nullable|email',
            'Nom' => 'nullable|string|max:100',
            'Prenom' => 'nullable|string|max:100',
        ];

        // If user is changing password, require and validate password fields
        if ($request->filled('new_password')) {
            $rules['new_password'] = 'required|string|min:8|confirmed';
        }

        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // If changing password, verify current password
        if ($request->filled('new_password')) {
            if (!\Illuminate\Support\Facades\Hash::check($data['current_password'] ?? '', $admin->MotDePasse)) {
                return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.']);
            }
            $admin->MotDePasse = \Illuminate\Support\Facades\Hash::make($data['new_password']);
        }

        // Update other fields
        $admin->fill($request->only(['email', 'Nom', 'Prenom']));
        $admin->save();

        return redirect()->back()->with('status', 'Paramètres mis à jour');
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

        $content = trim(($data['subject'] ? $data['subject']."\n\n" : '') . $data['body']);
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
                $m = new Message();
                $m->Contenu = $content;
                $m->DateEnvoi = $now;
                $m->Statut = 'envoye';
                $m->Client_idClient = $id;
                if ($sender) $m->Administrateur_idAdministrateur = $sender->idAdmi;
                $m->save();
                $created = 1;
            } elseif ($type === 'vendeur' && $id) {
                $m = new Message();
                $m->Contenu = $content;
                $m->DateEnvoi = $now;
                $m->Statut = 'envoye';
                $m->Vendeur_idVendeur = $id;
                if ($sender) $m->Administrateur_idAdministrateur = $sender->idAdmi;
                $m->save();
                $created = 1;
            } elseif ($type === 'admin' && $id) {
                // Sending to another admin: keep recording under recipient admin (legacy behavior)
                $m = new Message();
                $m->Contenu = $content;
                $m->DateEnvoi = $now;
                $m->Statut = 'envoye';
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
                    $m = new Message();
                    $m->Contenu = $content;
                    $m->DateEnvoi = $now;
                    $m->Statut = 'envoye';
                    $m->Client_idClient = $c->idClient;
                    if ($sender) $m->Administrateur_idAdministrateur = $sender->idAdmi;
                    $m->save();
                    $created++;
                }
            }
            if ($data['recipient_type'] === 'vendeurs' || $data['recipient_type'] === 'all') {
                $vendeurs = Vendeur::all();
                foreach ($vendeurs as $v) {
                    $m = new Message();
                    $m->Contenu = $content;
                    $m->DateEnvoi = $now;
                    $m->Statut = 'envoye';
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
                    $m->Statut = 'envoye';
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
}
