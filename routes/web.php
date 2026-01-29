<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProduitController;
use App\Models\Produit;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\VendeurController;
use App\Http\Controllers\PageVendeurController;
use App\Http\Controllers\AnalysesController;
use App\Http\Controllers\AdministrateurController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Vendeur;
use App\Models\Client;
use App\Models\Administrateur;

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

Route::get('/', function () {
    return view('PagePrincipale');
});

Route::get('/formulaireVendeur', function () {
    return view('formulaireVendeur');
});


Route::get('/AjouterProduit', function () {
    return view('produits.AjouterProduit');
});




Route::get('/formulaireClient', function () {
    return view('formulaireClient');
});
Route::post('/formulaireClient', function (Request $request){
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
    return view('formulaireClient', compact('message'));
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
Route::get('/PageClient', function () {
    $client = Auth::guard('client')->user();
    return view('PageClient', compact('client'));
})->middleware('auth:client')->name('PageClient');

// Admin authentication and dashboard
Route::get('/admin/login', [AdministrateurController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdministrateurController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdministrateurController::class, 'logout'])->name('admin.logout');

Route::prefix('admin')->middleware('auth:administrateur')->group(function () {
    Route::get('/', [AdministrateurController::class, 'dashboard'])->name('admin.dashboard');
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
Route::get('/PagePrincipale', function () {
    return view('PagePrincipale');
});
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