    
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\CartController;
use App\Models\Produit;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\VendeurController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PageVendeurController;
use App\Http\Controllers\AnalysesController;
use App\Http\Controllers\AdministrateurController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Vendeur;
use App\Models\Client;
use App\Models\Administrateur;
use App\Models\Message;


// Routes SPA pour PageVendeur (injection partielle)
Route::middleware(['auth:vendeur'])->group(function () {
    Route::post('/vendeur/produits/promotion', function(Request $request) {
        $vendeur = Auth::guard('vendeur')->user();
        $ids = $request->input('produits', []);
        $reduction = (int) $request->input('reduction', 0);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Aucun produit sélectionné.']);
        }
        if ($reduction < 1 || $reduction > 100) {
            return response()->json(['success' => false, 'message' => 'Réduction invalide.']);
        }
        $produits = \App\Models\Produit::whereIn('idProduit', $ids)
            ->where('Vendeur_idVendeur', $vendeur->idVendeur)
            ->get();
        $updated = 0;
        foreach ($produits as $produit) {
            if (!$produit->PrixOriginal) {
                $produit->PrixOriginal = $produit->Prix;
            }
            $nouveauPrix = round($produit->PrixOriginal * (1 - $reduction / 100));
            $produit->Prix = $nouveauPrix;
            $produit->Promotion = 1;
            $produit->Reduction = $reduction;
            $produit->save();
            $updated++;
        }
        if ($updated > 0) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Aucune mise à jour effectuée.']);
        }
    });
    Route::get('/vendeur/produits', function(Request $request) {
        $vendeur = Auth::guard('vendeur')->user();
        // Build query from the seller's produits relation so we can apply filters
        $query = $vendeur->produits();

        // Filter by category
        if ($request->filled('categorie')) {
            $query->where('Categorie', $request->categorie);
        }

        // Quick search on name/description/category
        if ($request->filled('recherche')) {
            $term = $request->recherche;
            $query->where(function($q) use ($term) {
                $q->where('Nom', 'like', '%' . $term . '%')
                  ->orWhere('Description', 'like', '%' . $term . '%')
                  ->orWhere('Categorie', 'like', '%' . $term . '%');
            });
        }

        // Sorting
        if ($request->filled('tri_prix')) {
            if ($request->tri_prix === 'asc') {
                $query->orderBy('Prix', 'asc');
            } elseif ($request->tri_prix === 'desc') {
                $query->orderBy('Prix', 'desc');
            } elseif ($request->tri_prix === 'recente') {
                $query->orderBy('DateAjout', 'desc');
            }
        } else {
            $query->orderBy('DateAjout', 'desc');
        }

        $produits = $query->get();

        if ($request->ajax()) {
            return view('vendeurs.produits', compact('vendeur', 'produits'));
        } else {
            return view('PageVendeur', [
                'partial' => 'vendeurs.produits',
                'vendeur' => $vendeur,
                'produits' => $produits
            ]);
        }
    });
    Route::get('/vendeur/commandes', function(Request $request) {
        $vendeur = Auth::guard('vendeur')->user();
        $commandes = \App\Models\Commande::whereHas('Produit', function($q) use ($vendeur) {
            $q->where('Vendeur_idVendeur', $vendeur->idVendeur);
        })->with(['Produit', 'Client'])->get();
        if ($request->ajax()) {
            return view('vendeurs.commandes', compact('vendeur', 'commandes'));
        } else {
            return view('PageVendeur', [
                'partial' => 'vendeurs.commandes',
                'vendeur' => $vendeur,
                'commandes' => $commandes
            ]);
        }
    });
    Route::get('/vendeur/mes-commandes', function(Request $request) {
        $vendeur = Auth::guard('vendeur')->user();
        $client = \App\Models\Client::where('email', $vendeur->email)->first();
        $commandes = $client ? $client->commandes()->with(['Produit' => function($q) {
            $q->with('Vendeur');
        }])->get() : collect();
        if ($request->ajax()) {
            return view('vendeurs.mes-commandes', compact('vendeur', 'commandes'));
        } else {
            return view('PageVendeur', [
                'partial' => 'vendeurs.mes-commandes',
                'vendeur' => $vendeur,
                'commandes' => $commandes
            ]);
        }
    });
    Route::post('/vendeur/commandes/{id}/mark-delivered', [VendeurController::class, 'markDelivered'])->middleware('vendeur.not.blocked');
    Route::delete('/vendeur/commandes/{id}', [VendeurController::class, 'deleteCommande'])->middleware('vendeur.not.blocked');
    Route::delete('/vendeur/mes-commandes/{id}', [VendeurController::class, 'deleteMesCommande'])->name('vendeur.mes-commandes.destroy')->middleware('vendeur.not.blocked');
    Route::post('/passer-commande', [CommandeController::class, 'store']);
    Route::get('/vendeur/clients', function(Request $request) {
        $vendeur = Auth::guard('vendeur')->user();
        $clients = Client::whereHas('commandes', function($q) use ($vendeur) {
            $q->where('Statut', 'Livrée')->whereHas('Produit', function($p) use ($vendeur) {
                $p->where('Vendeur_idVendeur', $vendeur->idVendeur);
            });
        })->with(['commandes' => function($q) use ($vendeur) {
            $q->where('Statut', 'Livrée')->whereHas('Produit', function($p) use ($vendeur) {
                $p->where('Vendeur_idVendeur', $vendeur->idVendeur);
            });
        }])->get();
        if ($request->ajax()) {
            return view('vendeurs.clients', compact('vendeur', 'clients'));
        } else {
            return view('PageVendeur', [
                'partial' => 'vendeurs.clients',
                'vendeur' => $vendeur,
                'clients' => $clients
            ]);
        }
    })->name('vendeur.clients');
    Route::get('/vendeur/clients/{id}', [VendeurController::class, 'showClient'])->name('vendeur.clients.show');
    Route::get('/vendeur/analyses', function(Request $request) {
        $vendeur = Auth::guard('vendeur')->user();
        if ($request->ajax()) {
            return app(\App\Http\Controllers\AnalysesController::class)->index($request);
        } else {
            return view('PageVendeur', [
                'partial' => 'vendeurs.analyses',
                'vendeur' => $vendeur
            ]);
        }
    });
    Route::get('/vendeur/messages', [VendeurController::class, 'messages'])->name('vendeur.messages');
    // Message endpoints for conversation actions
    Route::get('/vendeur/messages/conversation/{type}/{id}', [VendeurController::class, 'getConversation']);
    Route::delete('/vendeur/messages/conversation/{type}/{id}', [VendeurController::class, 'deleteConversation']);
    Route::delete('/vendeur/messages/{id}', [VendeurController::class, 'deleteMessage']);
    Route::post('/vendeur/messages/block/{type}/{id}', [VendeurController::class, 'blockUser']);
    Route::post('/vendeur/messages/unblock/{type}/{id}', [VendeurController::class, 'unblockUser']);
    // Send message (named route used by the view)
    Route::post('/vendeur/messages/send', [VendeurController::class, 'sendMessage'])->name('vendeur.messages.send')->middleware('vendeur.not.blocked');
    Route::get('/vendeur/parametres', function(Request $request) {
        $vendeur = Auth::guard('vendeur')->user();
        if ($request->ajax()) {
            return app(\App\Http\Controllers\VendeurController::class)->parametres($request);
        } else {
            return view('PageVendeur', [
                'partial' => 'vendeurs.parametres',
                'vendeur' => $vendeur
            ]);
        }
    });
    Route::get('/vendeur/ia-alertes', [VendeurController::class, 'iaAlerts'])->name('vendeurs.ia_alertes');

    // Vendeur message routes
    Route::get('/messages/conversation/{type}/{id}', [VendeurController::class, 'getConversation'])->name('vendeur.messages.conversation');
    Route::post('/messages/send', [VendeurController::class, 'sendMessage'])->name('vendeur.messages.send')->middleware('vendeur.not.blocked');
    Route::delete('/messages/{id}', [VendeurController::class, 'deleteMessage'])->name('vendeur.messages.delete');
    Route::delete('/messages/conversation/{type}/{id}', [VendeurController::class, 'deleteConversation'])->name('vendeur.messages.conversation.delete');
    Route::post('/messages/block/{type}/{id}', [VendeurController::class, 'blockUser'])->name('vendeur.messages.block');
    Route::post('/messages/unblock/{type}/{id}', [VendeurController::class, 'unblockUser'])->name('vendeur.messages.unblock');
});

Route::middleware(['auth:vendeur'])->group(function () {
    Route::get ('/produits', [ProduitController::class, 'index']);
    Route::get('/produits/{id}/edit', [ProduitController::class, 'edit'])->name('produits.edit');
    Route::post('/produits', [ProduitController::class, 'AjouterProduit'])->name('produits.AjouterProduit')->middleware('vendeur.not.blocked');
    Route::get('/produits/{id}', [ProduitController::class, 'show'])->name('produits.show');
    Route::match(['post','put'], '/produits/{id}', [ProduitController::class, 'update'])->name('produits.update')->middleware('vendeur.not.blocked');
    Route::post('/produits/{id}/delete', [ProduitController::class, 'destroy'])->name('produits.destroy')->middleware('vendeur.not.blocked');
});
Route::get ('/commandes', [CommandeController::class, 'index']);
Route::post('/passer-commande', [CommandeController::class, 'store'])->name('passer.commande');
Route::get ('/vendeurs', [VendeurController::class, 'index']);
// Clients list (simple controller-less route returning a view)
Route::get('/clients', function () {
    $vendeur = Auth::guard('vendeur')->user();
    $clients = Client::all();
    return view('clients.index', compact('clients', 'vendeur'));
});

Route::post('/formulaireVendeur', [VendeurController::class, 'FormulaireVendeur']);
Route::get('/PageVendeur', [PageVendeurController::class, 'index'])->name('PageVendeur')->middleware('auth:vendeur');
Route::post('/AjouterProduit', [ProduitController::class, 'AjouterProduit']);

Route::get('/', function (Request $request) {
    $query = Produit::query();
    // Filtrer par catégorie si fourni
    if ($request->filled('categorie')) {
        $query->where('Categorie', $request->categorie);
    }
    // Filtrer par recherche rapide (nom, description ou catégorie du produit)
    if ($request->filled('recherche')) {
        $term = trim($request->recherche);
        $query->where(function($q) use ($term) {
            $q->where('Nom', 'like', '%' . $term . '%')
              ->orWhere('Description', 'like', '%' . $term . '%')
              ->orWhere('Categorie', 'like', '%' . $term . '%');
        });
    }

    $produits = $query->orderBy('DateAjout', 'desc')->get();
    return view('PagePrincipale', compact('produits'));
});

// Routes pour les pages statiques
Route::get('/a-propos', function () {
    return view('a-propos');
});

Route::get('/contact', function () {
    return view('contact');
});

// Page listant tous les produits en promotion
Route::get('/promotions', function (Request $request) {
    $query = Produit::where('Promotion', 1)
        ->whereNotNull('Reduction')
        ->where('Reduction', '>', 0);

    // Filtre par recherche (nom, description, catégorie)
    if ($request->filled('recherche')) {
        $term = trim($request->recherche);
        $query->where(function ($q) use ($term) {
            $q->where('Nom', 'like', '%' . $term . '%')
                ->orWhere('Description', 'like', '%' . $term . '%')
                ->orWhere('Categorie', 'like', '%' . $term . '%');
        });
    }

    // Filtre par catégorie
    if ($request->filled('categorie')) {
        $query->where('Categorie', $request->categorie);
    }

    // Tri
    if ($request->filled('tri_prix')) {
        if ($request->tri_prix === 'asc') {
            $query->orderBy('Prix', 'asc');
        } elseif ($request->tri_prix === 'desc') {
            $query->orderBy('Prix', 'desc');
        } elseif ($request->tri_prix === 'recente') {
            $query->orderBy('DateAjout', 'desc');
        } else {
            $query->orderBy('DateAjout', 'desc');
        }
    } elseif ($request->filled('tri_reduction')) {
        if ($request->tri_reduction === 'desc') {
            $query->orderBy('Reduction', 'desc');
        } else {
            $query->orderBy('Reduction', 'asc');
        }
    } else {
        $query->orderBy('DateAjout', 'desc');
    }

    $produits = $query->get();
    return view('promotions', compact('produits'));
})->name('promotions');

// Page listant tous les produits les plus vendus
Route::get('/top-vendus', function(Request $request){
    // Redirect to the recherches fragment (AJAX fragment endpoint)
    return redirect('/top-recherches/fragment');
});

// New route explicitly for top searches
Route::get('/top-recherches', function(Request $request){
    // Redirect to the recherches fragment (AJAX fragment endpoint)
    return redirect('/top-recherches/fragment');
});

// AJAX fragment for top recherches (product grid only)
Route::get('/top-recherches/fragment', function(Request $request){
    $topRecherchesIds = [];
    if(\Illuminate\Support\Facades\Schema::hasTable('recherches')){
        $topRecherchesIds = \Illuminate\Support\Facades\DB::table('recherches')
            ->select('produit_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'))
            ->groupBy('produit_id')
            ->orderByDesc('total')
            ->pluck('produit_id')
            ->toArray();
    }
    $topRecherches = collect([]);
    if(!empty($topRecherchesIds)){
        $prodMap = \App\Models\Produit::whereIn('idProduit', $topRecherchesIds)->get()->keyBy('idProduit');
        $topRecherches = collect($topRecherchesIds)->map(function($id) use($prodMap){ return $prodMap->get($id); })->filter();
    }
    if($topRecherches->isEmpty() && \Illuminate\Support\Facades\Schema::hasTable('Produitcommande')){
        $topVendusIds = \Illuminate\Support\Facades\DB::table('Produitcommande')
            ->select('Produit_idProduit', \Illuminate\Support\Facades\DB::raw('SUM(Quantite) as total'))
            ->groupBy('Produit_idProduit')
            ->orderByDesc('total')
            ->pluck('Produit_idProduit')
            ->toArray();
        if(!empty($topVendusIds)){
            $prodMap = \App\Models\Produit::whereIn('idProduit', $topVendusIds)->get()->keyBy('idProduit');
            $topRecherches = collect($topVendusIds)->map(function($id) use($prodMap){ return $prodMap->get($id); })->filter();
        }
    }
    return view('partials.top_list', ['items' => $topRecherches]);
});

// Keep legacy top-vendus fragment route too
Route::get('/top-vendus/fragment', function(Request $request){
    $topVendusIds = [];
    if(\Illuminate\Support\Facades\Schema::hasTable('Produitcommande')){
        $topVendusIds = \Illuminate\Support\Facades\DB::table('Produitcommande')
            ->select('Produit_idProduit', \Illuminate\Support\Facades\DB::raw('SUM(Quantite) as total'))
            ->groupBy('Produit_idProduit')
            ->orderByDesc('total')
            ->pluck('Produit_idProduit')
            ->toArray();
    }
    $topVendus = collect([]);
    if(!empty($topVendusIds)){
        $prodMap = \App\Models\Produit::whereIn('idProduit', $topVendusIds)->get()->keyBy('idProduit');
        $topVendus = collect($topVendusIds)->map(function($id) use($prodMap){ return $prodMap->get($id); })->filter();
    }
    return view('partials.top_list', ['items' => $topVendus]);
});

Route::get('/formulaireVendeur', function () {
    return view('formulaireVendeur');
});
Route::post('/formulaireVendeur', [VendeurController::class, 'FormulaireVendeur']);

Route::get('/formulaireClient', function () {
    return view('formulaireClient');
});

// Cart routes (session-based)
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/formulaireClient', [ClientController::class, 'FormulaireClient']);


// Unified Connexion page
Route::get('/Connexion', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('connexion');
// alias for Laravel default middleware that redirects to route('login')
Route::get('/login', function(){ return redirect()->route('connexion'); })->name('login');
Route::post('/Connexion', [App\Http\Controllers\AuthController::class, 'login'])->name('connexion.post');

// Backwards compatible routes redirecting to unified Connexion
Route::get('/ConnexionVendeur', function () { return redirect()->route('connexion'); });
Route::post('/ConnexionVendeur', function (Request $request) { return redirect()->route('connexion.post'); });

// Unified logout route
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// Redirect /dashboard to /admin/dashboard for admin users
Route::get('/dashboard', function () { return redirect('/admin/dashboard'); })->middleware('auth');

Route::get('/ConnexionClient', function () { return redirect()->route('connexion'); });
Route::post('/ConnexionClient', function (Request $request) { return redirect()->route('connexion.post'); });

// Client page (protected) — named PageClient
Route::get('/PageClient', function (Request $request) {
    $client = Auth::guard('client')->user();
    $client->load(['commandes.Produit', 'message' => function($q) { $q->orderBy('DateEnvoi', 'desc'); }]);
    // Support AJAX partials via ?view=dashboard
    if ($request->ajax()) {
        $view = $request->query('view', 'dashboard');
        if ($view === 'dashboard') {
            return view('clients.dashboard', compact('client'));
        }
        // default AJAX response for PageClient is dashboard partial
        return view('clients.dashboard', compact('client'));
    }
    return view('PageClient', compact('client'));
})->middleware('auth:client')->name('PageClient');

// Client SPA routes (protected) — return partial for AJAX requests
Route::middleware(['auth:client'])->group(function () {
    Route::get('/commandes', function(Request $request){
        $client = Auth::guard('client')->user();
        $commandes = $client ? $client->commandes()->orderBy('DateCommande','desc')->get() : collect();
        if ($request->ajax()) {
            return view('clients.commandes', compact('client', 'commandes'));
        }
        return view('PageClient', ['partial' => 'clients.commandes', 'client' => $client, 'commandes' => $commandes]);
    });

    Route::get('/commandes/{id}', [ClientController::class, 'showCommande'])->name('client.commande.show');
    Route::delete('/commandes/{id}', [CommandeController::class, 'destroy'])->name('client.commande.destroy')->middleware('client.not.blocked');

    Route::get('/messages', function(Request $request){
        $client = Auth::guard('client')->user();
        // Récupérer tous les messages du client avec les vendeurs et administrateurs
        $messages = Message::with(['vendeur', 'administrateur'])->where('Client_idClient', $client->idClient)->orderBy('DateEnvoi', 'desc')->get();

        // Grouper les messages en conversations par vendeur ou admin
        $conversations = [];
        foreach ($messages as $message) {
            $key = '';
            $sender = null;
            $senderType = '';
            if ($message->vendeur) {
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
                    'isBlocked' => $senderType === 'vendeur' ? ($sender->Bloque ?? false) : false,
                ];
            } elseif ($key) {
                if ($message->DateEnvoi > $conversations[$key]['lastMessageDate']) {
                    $conversations[$key]['lastMessage'] = $message;
                    $conversations[$key]['lastMessageDate'] = $message->DateEnvoi;
                }
                // Count messages considered unread by the model (supports legacy variants)
                if ($message->isUnread()) {
                    $conversations[$key]['unreadCount']++;
                }
            }
        }

        // Trier les conversations par date du dernier message
        usort($conversations, function($a, $b) {
            return $b['lastMessageDate'] <=> $a['lastMessageDate'];
        });

        $conversations = collect($conversations)->values();

        if ($request->ajax()) {
            return view('clients.messages', compact('client', 'conversations'));
        }
        return view('PageClient', ['partial' => 'clients.messages', 'client' => $client, 'conversations' => $conversations]);
    });

    // Client message routes
    Route::get('/messages/conversation/{type}/{id}', [ClientController::class, 'getConversation'])->name('client.messages.conversation');
    Route::post('/messages/send', [ClientController::class, 'sendMessage'])->name('client.messages.send')->middleware('client.not.blocked');
    Route::delete('/messages/{id}', [ClientController::class, 'deleteMessage'])->name('client.messages.delete')->middleware('client.not.blocked');
    Route::post('/messages/block/{type}/{id}', [ClientController::class, 'blockUser'])->name('client.messages.block');
    Route::post('/messages/unblock/{type}/{id}', [ClientController::class, 'unblockUser'])->name('client.messages.unblock');

    Route::get('/parametres', function(Request $request){
        $client = Auth::guard('client')->user();
        if ($request->ajax()) {
            return view('clients.parametres', compact('client'));
        }
        return view('PageClient', ['partial' => 'clients.parametres', 'client' => $client]);
    });

    // Client settings POST (supports AJAX)
    Route::post('/parametres', function(Request $request){
        $client = Auth::guard('client')->user();
        if (!$client) {
            if ($request->ajax()) return response()->json(['success' => false, 'message' => 'Non authentifié'], 401);
            return redirect()->route('connexion');
        }

        $data = $request->only(['email', 'TelClient', 'Nom', 'Prenom', 'Adresse', 'current_password', 'new_password', 'new_password_confirmation']);
        $rules = [
            'email' => 'nullable|email',
            'TelClient' => 'nullable|string|max:30',
            'Nom' => 'nullable|string|max:100',
            'Prenom' => 'nullable|string|max:100',
        ];

        // If user is changing password, require and validate password fields
        if ($request->filled('new_password')) {
            $rules['new_password'] = 'required|string|min:8|confirmed';
        }

        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules);

        if ($validator->fails()) {
            if ($request->ajax()) return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
            return back()->withErrors($validator)->withInput();
        }

        // If changing password, verify current password
        if ($request->filled('new_password')) {
            if (!\Illuminate\Support\Facades\Hash::check($data['current_password'] ?? '', $client->MotDePasse)) {
                if ($request->ajax()) return response()->json(['success' => false, 'message' => 'Mot de passe actuel incorrect.'], 422);
                return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.']);
            }
            $client->MotDePasse = \Illuminate\Support\Facades\Hash::make($data['new_password']);
        }

        // include Adresse if provided
        $client->fill($request->only(['email', 'TelClient', 'Nom', 'Prenom', 'Adresse']));
        $client->save();

        if ($request->ajax()) return response()->json(['success' => true, 'message' => 'Enregistré', 'client' => $client]);
        return redirect()->back()->with('status', 'Paramètres mis à jour');

    })->middleware('auth:client');

    // Verify current password before showing new password fields (AJAX helper)
    Route::post('/parametres/verify-password', function(Request $request){
        $client = Auth::guard('client')->user();
        if (!$client) return response()->json(['success' => false, 'message' => 'Non authentifié'], 401);
        $request->validate(['current_password' => 'required|string']);
        if (\Illuminate\Support\Facades\Hash::check($request->current_password, $client->MotDePasse)) {
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Mot de passe incorrect'], 422);
    })->middleware('auth:client');
});

// Route nommée PageAdmin pour compatibilité avec la page principale
Route::get('/PageAdmin', function () {
    return redirect()->route('admin.dashboard');
})->name('PageAdmin');

// Admin authentication and dashboard
Route::get('/admin/login', [AdministrateurController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdministrateurController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdministrateurController::class, 'logout'])->name('admin.logout');

Route::prefix('admin')->middleware('auth.administrateur')->group(function () {
    Route::get('/', [AdministrateurController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/produits', [AdministrateurController::class, 'produits'])->name('admin.produits');
    // Admin product detail (AJAX-friendly partial)
    Route::get('/produits/{id}', [AdministrateurController::class, 'showProduit'])->name('admin.produits.show');
    Route::post('/produits/{id}/delete', [AdministrateurController::class, 'deleteProduit'])->name('admin.produits.delete');
    Route::get('/clients', [AdministrateurController::class, 'clients'])->name('admin.clients');
    Route::get('/clients/{id}', [AdministrateurController::class, 'showClient'])->name('admin.clients.show');
    Route::post('/clients/{id}/delete', [AdministrateurController::class, 'deleteClient'])->name('admin.clients.delete');
    Route::get('/messages', [AdministrateurController::class, 'messages'])->name('admin.messages');
    Route::post('/messages/block/{type}/{id}', [AdministrateurController::class, 'blockUser'])->name('admin.messages.block');
    Route::post('/messages/unblock/{type}/{id}', [AdministrateurController::class, 'unblockUser'])->name('admin.messages.unblock');
    // fetch a single conversation (AJAX)
    Route::get('/messages/conversation/{type}/{id}', [AdministrateurController::class, 'getConversation'])->name('admin.messages.conversation');
    Route::post('/messages/send', [AdministrateurController::class, 'sendMessage'])->name('admin.messages.send');
    // Accept PATCH as a fallback for older clients/scripts that still send PATCH
    Route::patch('/messages/send', [AdministrateurController::class, 'sendMessage']);
    Route::delete('/messages/{id}', [AdministrateurController::class, 'deleteMessage'])->name('admin.messages.delete');
    Route::delete('/messages/conversation/{type}/{id}', [AdministrateurController::class, 'deleteConversation'])->name('admin.messages.conversation.delete');
    Route::get('/vendeurs', [AdministrateurController::class, 'vendeurs'])->name('admin.vendeurs');
    Route::get('/vendeurs/{id}', [AdministrateurController::class, 'showVendeur'])->name('admin.vendeurs.show');
    Route::post('/vendeurs/{id}/delete', [AdministrateurController::class, 'deleteVendeur'])->name('admin.vendeurs.delete');
    Route::get('/commandes', [AdministrateurController::class, 'commandes'])->name('admin.commandes');
    Route::get('/mes-commandes', [AdministrateurController::class, 'mesCommandes'])->name('admin.mes-commandes');
    Route::get('/commandes/{id}', [AdministrateurController::class, 'showCommande'])->name('admin.commandes.show');
    Route::get('/parametres', [AdministrateurController::class, 'parametres'])->name('admin.parametres');
    Route::post('/parametres', [AdministrateurController::class, 'updateSettings'])->name('admin.parametres.update');
    Route::patch('/admin/update-info', [AdministrateurController::class, 'updateAdminInfo'])->name('admin.update.info');
    Route::get('/ia-alertes', [AdministrateurController::class, 'iaAlerts'])->name('admin.ia_alertes');
    Route::get('/ia-alertes/{id}', [AdministrateurController::class, 'showAlerte'])->name('admin.ia_alertes.show');
    Route::post('/ia-alertes/delete-multiple', [AdministrateurController::class, 'deleteAlerts'])->name('admin.ia_alertes.delete');
    Route::get('/cart', [AdministrateurController::class, 'cart'])->name('admin.cart');
    Route::post('/cart/add', [AdministrateurController::class, 'addToCart'])->name('admin.cart.add');
    Route::post('/cart/remove', [AdministrateurController::class, 'removeFromCart'])->name('admin.cart.remove');
    Route::post('/cart/update', [AdministrateurController::class, 'updateCart'])->name('admin.cart.update');
    Route::post('/cart/place-order', [AdministrateurController::class, 'placeOrder'])->name('admin.cart.place-order');
});


Route::get('/ConnexionAdmin', function () {
    return view('ConnexionAdmin');
});

/* Route principale d'administration (PageAdmin)
Route::get('/admin', function () {
    return view('admin.PageAdmin');
})->name('PageAdmin');*/

Route::post('/deconnexion', function (Request $request) {
    Auth::guard('vendeur')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
});

/*Route::get('/welcome', function () {
    return view('Welcome');
});*/
Route::get('/PagePrincipale', function (Request $request) {
    $query = Produit::query();
    if ($request->filled('categorie')) {
        $query->where('Categorie', $request->categorie);
    }
    if ($request->filled('recherche')) {
        $term = trim($request->recherche);
        $query->where(function($q) use ($term) {
            $q->where('Nom', 'like', '%' . $term . '%')
              ->orWhere('Description', 'like', '%' . $term . '%');
        });
    }
    $produits = $query->orderBy('DateAjout', 'desc')->get();
    return view('PagePrincipale', compact('produits'));
});
// Public product detail route (accessible sans authentification vendeur)
Route::get('/produit/{id}', [ProduitController::class, 'publicShow'])->name('produit.public');
// Additional SPA pages used by PageVendeur sidebar
Route::get('/analyses', [AnalysesController::class, 'index']);

Route::get('/vendeur/parametres', [VendeurController::class, 'parametres'])->middleware('auth:vendeur');
Route::post('/vendeur/parametres', [VendeurController::class, 'updateSettings'])->middleware(['auth:vendeur', 'vendeur.not.blocked']);

// Note: message routes for clients and vendeurs are defined in their respective middleware groups above.

// AJAX helper: mark message as read for authenticated vendeur
Route::post('/vendeur/messages/{id}/lire', [App\Http\Controllers\MessageController::class, 'markAsRead'])->middleware('auth:vendeur');

    Route::post('/vendeur/produits/promotion/remove', function(Request $request) {
        $vendeur = Auth::guard('vendeur')->user();
        $ids = $request->input('produits', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Aucun produit sélectionné.']);
        }
        $produits = \App\Models\Produit::whereIn('idProduit', $ids)
            ->where('Vendeur_idVendeur', $vendeur->idVendeur)
            ->get();
        $updated = 0;
        foreach ($produits as $produit) {
            if ($produit->PrixOriginal) {
                $produit->Prix = $produit->PrixOriginal;
                $produit->PrixOriginal = null;
            }
            $produit->Promotion = 0;
            $produit->Reduction = null;
            $produit->save();
            $updated++;
        }
        if ($updated > 0) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Aucune mise à jour effectuée.']);
        }
    });

