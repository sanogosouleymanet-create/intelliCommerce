
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProduitController;
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
        $produits = $vendeur->produits;
        if ($request->ajax()) {
            return view('vendeurs.produits.index', compact('vendeur', 'produits'));
        } else {
            return view('PageVendeur', [
                'partial' => 'vendeurs.produits.index',
                'vendeur' => $vendeur,
                'produits' => $produits
            ]);
        }
    });
    Route::get('/vendeur/commandes', function(Request $request) {
        $vendeur = Auth::guard('vendeur')->user();
        $commandes = $vendeur->commandes ?? [];
        if ($request->ajax()) {
            return view('vendeurs.commandes.index', compact('vendeur', 'commandes'));
        } else {
            return view('PageVendeur', [
                'partial' => 'vendeurs.commandes.index',
                'vendeur' => $vendeur,
                'commandes' => $commandes
            ]);
        }
    });
    Route::get('/vendeur/clients', function(Request $request) {
        $vendeur = Auth::guard('vendeur')->user();
        $clients = $vendeur->clients ?? [];
        if ($request->ajax()) {
            return view('vendeurs.clients.index', compact('vendeur', 'clients'));
        } else {
            return view('PageVendeur', [
                'partial' => 'vendeurs.clients.index',
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
                'partial' => 'vendeurs.analyses.index',
                'vendeur' => $vendeur
            ]);
        }
    });
    Route::get('/vendeur/messages', function(Request $request) {
        $vendeur = Auth::guard('vendeur')->user();
        $messages = $vendeur->messages ?? [];
        if ($request->ajax()) {
            return view('vendeurs.messages.index', compact('vendeur', 'messages'));
        } else {
            return view('PageVendeur', [
                'partial' => 'vendeurs.messages.index',
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
                'partial' => 'vendeurs.parametres.index',
                'vendeur' => $vendeur
            ]);
        }
    });
});

Route::middleware(['auth:vendeur'])->group(function () {
    Route::get ('/produits', [ProduitController::class, 'index']);
    Route::post('/produits', [ProduitController::class, 'AjouterProduit'])->name('produits.AjouterProduit');
    Route::get('/produits/{id}', [ProduitController::class, 'show'])->name('produits.show');
    // Accept both POST (legacy) and PUT for updates
    Route::match(['post','put'], '/produits/{id}', [ProduitController::class, 'update'])->name('produits.update');
    Route::post('/produits/{id}/delete', [ProduitController::class, 'destroy'])->name('produits.destroy');
});
Route::get ('/commandes', [CommandeController::class, 'index']);
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
        $term = $request->recherche;
        $query->where(function($q) use ($term) {
            $q->where('Nom', 'like', '%' . $term . '%')
              ->orWhere('Description', 'like', '%' . $term . '%')
              ->orWhere('Categorie', 'like', '%' . $term . '%');
        });
    }

    $produits = $query->orderBy('DateAjout', 'desc')->get();
    return view('PagePrincipale', compact('produits'));
});

Route::get('/formulaireVendeur', function () {
    return view('formulaireVendeur');
});
Route::post('/formulaireVendeur', [VendeurController::class, 'FormulaireVendeur']);
Route::get('/AjouterProduit', function () {
    return view('produits.AjouterProduit');
});
Route::get('/PageVendeur', [PageVendeurController::class, 'index'])->name('PageVendeur')->middleware('auth:vendeur');
Route::post('/AjouterProduit', [ProduitController::class, 'AjouterProduit']);

Route::get('/formulaireClient', function () {
    return view('formulaireClient');
});
Route::post('/formulaireClient', [ClientController::class, 'FormulaireClient']);


Route::get('/', function () {
    return view('PagePrincipale');
});

// Unified Connexion page
Route::get('/Connexion', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('connexion');
Route::post('/Connexion', [App\Http\Controllers\AuthController::class, 'login'])->name('connexion.post');

// Backwards compatible routes redirecting to unified Connexion
Route::get('/ConnexionVendeur', function () { return redirect()->route('connexion'); });
Route::post('/ConnexionVendeur', function (Request $request) { return redirect()->route('connexion.post'); });

// Unified logout route
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

Route::get('/ConnexionClient', function () { return redirect()->route('connexion'); });
Route::post('/ConnexionClient', function (Request $request) { return redirect()->route('connexion.post'); });

// Client page (protected) — named PageClient
Route::get('/PageClient', function (Request $request) {
    $client = Auth::guard('client')->user();
    if ($request->ajax()) {
        return view('clients.profile', compact('client'));
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
});

// Admin authentication and dashboard
Route::get('/admin/login', [AdministrateurController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdministrateurController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdministrateurController::class, 'logout'])->name('admin.logout');

Route::prefix('admin')->middleware('auth:administrateur')->group(function () {
    Route::get('/', [AdministrateurController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/produits', [AdministrateurController::class, 'produits'])->name('admin.produits');
    Route::get('/clients', [AdministrateurController::class, 'clients'])->name('admin.clients');
    Route::get('/vendeurs', [AdministrateurController::class, 'vendeurs'])->name('admin.vendeurs');
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
        $term = $request->recherche;
        $query->where(function($q) use ($term) {
            $q->where('Nom', 'like', '%' . $term . '%')
              ->orWhere('Description', 'like', '%' . $term . '%')
              ->orWhere('Categorie', 'like', '%' . $term . '%');
        });
    }
    $produits = $query->orderBy('DateAjout', 'desc')->get();
    return view('PagePrincipale', compact('produits'));
});
// Public product detail route (accessible sans authentification vendeur)
Route::get('/produit/{id}', [ProduitController::class, 'publicShow'])->name('produit.public');
// Additional SPA pages used by PageVendeur sidebar
Route::get('/analyses', [AnalysesController::class, 'index']);

Route::get('/parametres', [VendeurController::class, 'parametres'])->middleware('auth:vendeur');
Route::post('/parametres', [VendeurController::class, 'updateSettings'])->middleware('auth:vendeur');

Route::get('/messages', function () {
    $vendeur = Auth::guard('vendeur')->user();
    // The messages table uses `DateEnvoi` (no timestamps), so order by that column
    $messages = $vendeur ? $vendeur->messages()->latest('DateEnvoi')->get() : collect();
    return view('messages.index', compact('messages', 'vendeur'));
});