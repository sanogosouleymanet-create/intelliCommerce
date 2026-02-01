
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


// Routes SPA pour PageVendeur (injection partielle)
Route::middleware(['auth:vendeur'])->group(function () {
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
        $commandes = $vendeur->commandes ?? [];
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
    Route::get('/vendeur/clients', function(Request $request) {
        $vendeur = Auth::guard('vendeur')->user();
        $clients = $vendeur->clients ?? [];
        if ($request->ajax()) {
            return view('vendeurs.clients', compact('vendeur', 'clients'));
        } else {
            return view('PageVendeur', [
                'partial' => 'vendeurs.clients',
                'vendeur' => $vendeur,
                'clients' => $clients
            ]);
        }
    });
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
    Route::get('/vendeur/messages', function(Request $request) {
        $vendeur = Auth::guard('vendeur')->user();
        $messages = $vendeur->messages ?? [];
        if ($request->ajax()) {
            return view('vendeurs.messages', compact('vendeur', 'messages'));
        } else {
            return view('PageVendeur', [
                'partial' => 'vendeurs.messages',
                'vendeur' => $vendeur,
                'messages' => $messages
            ]);
        }
    });
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
});

Route::middleware(['auth:vendeur'])->group(function () {
    Route::get ('/produits', [ProduitController::class, 'index']);
    Route::get('/produits/{id}/edit', [ProduitController::class, 'edit'])->name('produits.edit');
    Route::post('/produits', [ProduitController::class, 'AjouterProduit'])->name('produits.AjouterProduit');
    Route::get('/produits/{id}', [ProduitController::class, 'show'])->name('produits.show');
    // Accept both POST (legacy) and PUT for updates
    Route::match(['post','put'], '/produits/{id}', [ProduitController::class, 'update'])->name('produits.update');
    Route::post('/produits/{id}/delete', [ProduitController::class, 'destroy'])->name('produits.destroy');
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
    // Filtrer par recherche rapide (nom ou description)
    if ($request->filled('recherche')) {
        $term = trim($request->recherche);
        // search in product name or description only (not category)
        $query->where(function($q) use ($term) {
            $q->where('Nom', 'like', '%' . $term . '%')
              ->orWhere('Description', 'like', '%' . $term . '%');
        });
    }

    $produits = $query->orderBy('DateAjout', 'desc')->get();
    return view('PagePrincipale', compact('produits'));
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

    Route::get('/messages', function(Request $request){
        $client = Auth::guard('client')->user();
        $messages = $client ? $client->message()->orderBy('DateEnvoi','desc')->get() : collect();
        if ($request->ajax()) {
            return view('clients.messages', compact('client', 'messages'));
        }
        return view('PageClient', ['partial' => 'clients.messages', 'client' => $client, 'messages' => $messages]);
    });

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

// Admin authentication and dashboard
Route::get('/admin/login', [AdministrateurController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdministrateurController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdministrateurController::class, 'logout'])->name('admin.logout');

Route::prefix('admin')->middleware('auth:administrateur')->group(function () {
    Route::get('/', [AdministrateurController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/produits', [AdministrateurController::class, 'produits'])->name('admin.produits');
    // Admin product detail (AJAX-friendly partial)
    Route::get('/produits/{id}', [AdministrateurController::class, 'showProduit'])->name('admin.produits.show');
    Route::post('/produits/{id}/delete', [AdministrateurController::class, 'deleteProduit'])->name('admin.produits.delete');
    Route::get('/clients', [AdministrateurController::class, 'clients'])->name('admin.clients');
    Route::get('/clients/{id}', [AdministrateurController::class, 'showClient'])->name('admin.clients.show');
    Route::post('/clients/{id}/delete', [AdministrateurController::class, 'deleteClient'])->name('admin.clients.delete');
    Route::get('/messages', [AdministrateurController::class, 'messages'])->name('admin.messages');
    // fetch a single conversation (AJAX)
    Route::get('/messages/conversation/{type}/{id}', [AdministrateurController::class, 'getConversation'])->name('admin.messages.conversation');
    Route::post('/messages/send', [AdministrateurController::class, 'sendMessage'])->name('admin.messages.send');
    Route::get('/vendeurs', [AdministrateurController::class, 'vendeurs'])->name('admin.vendeurs');
    Route::get('/vendeurs/{id}', [AdministrateurController::class, 'showVendeur'])->name('admin.vendeurs.show');
    Route::post('/vendeurs/{id}/delete', [AdministrateurController::class, 'deleteVendeur'])->name('admin.vendeurs.delete');
    Route::get('/ia-alertes', [AdministrateurController::class, 'iaAlerts'])->name('admin.ia_alertes');
});

Route::get('/ConnexionAdmin', function () {
    return view('ConnexionAdmin');
});

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
Route::post('/vendeur/parametres', [VendeurController::class, 'updateSettings'])->middleware('auth:vendeur');

// Note: message routes for clients and vendeurs are defined in their respective middleware groups above.

// AJAX helper: mark message as read for authenticated vendeur
Route::post('/vendeur/messages/{id}/lire', [App\Http\Controllers\MessageController::class, 'markAsRead'])->middleware('auth:vendeur');