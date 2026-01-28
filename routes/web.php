<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProduitController;
use App\Models\Produit;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\VendeurController;
use App\Http\Controllers\PageVendeurController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Vendeur;
use App\Models\Client;
use App\Models\Administrateur;

Route::middleware(['auth:vendeur'])->group(function () {
    Route::get ('/produits', [ProduitController::class, 'index']);
    Route::post('/produits', [ProduitController::class, 'AjouterProduit'])->name('produits.AjouterProduit');
});
Route::get ('/commandes', [CommandeController::class, 'index']);
Route::get ('/vendeurs', [VendeurController::class, 'index']);

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

Route::get('/ConnexionVendeur', function () {
    return view('ConnexionVendeur');
})->name('login');

Route::post('/ConnexionVendeur', function (Request $request){
    $email = $request->email;
    $motdepasse = $request->motdepasse;

    $vendeur = Vendeur::where('email', $email)->first();

    if ($vendeur && Hash::check($motdepasse, $vendeur->MotDePasse)) {
        Auth::guard('vendeur')->login($vendeur); // Connexion du vendeur via le guard 'vendeur'
        $request->session()->regenerate(); // Régénérer la session pour éviter les attaques de fixation de session
        return redirect()->route('PageVendeur');
    } else {
        $message = "Email ou mot de passe incorrect.";
        return back()->withErrors(['credentials' => $message])->withInput();
    }
});


Route::get('/ConnexionClient', function () {
    return view('ConnexionClient');
});
Route::post('/ConnexionClient', function (Request $request){
    $validated = $request->validate([
        'email' => 'required|email',
        'motdepasse' => 'required|string',
    ]);

    $email = trim(strtolower($request->email));
    $motdepasse = $request->motdepasse;

    $client = Client::whereRaw('LOWER(email) = ?', [$email])->first();

    if ($client && Hash::check($motdepasse, $client->MotDePasse)) {
        return view('PagePrincipale');
    } else {
        $message = "Email ou mot de passe incorrect.";
        return view('ConnexionClient', compact('message'));
    }
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
