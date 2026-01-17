<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\VendeurController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Vendeur;
use App\Models\Client;
use App\Models\Administrateur;


Route::get ('/produits', [ProduitController::class, 'index']);
Route::get ('/commandes', [CommandeController::class, 'index']);
Route::get ('/vendeurs', [VendeurController::class, 'index']);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/formulaireVendeur', function () {
    return view('formulaireVendeur');
});
Route::post('/formulaireVendeur', function (Request $request){
    $message = "Vendeur enregidtré";
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
    return view('formulaireVendeur', compact('message'));
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
});
Route::post('/ConnexionVendeur', function (Request $request){
    $email = $request->email;
    $motdepasse = $request->motdepasse;

    $vendeur = Vendeur::where('email', $email)->first();

    if ($vendeur && Hash::check($motdepasse, $vendeur->MotDePasse)) {
        $message = "Connexion réussie";
        return view('PageVendeur');
    } else {
        $message = "Email ou mot de passe incorrect.";
        return view('ConnexionVendeur', compact('message'));
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
        $message = "Connexion réussie";
        return view('ConnexionClient', compact('message'));
    } else {
        $message = "Email ou mot de passe incorrect.";
        return view('ConnexionClient', compact('message'));
    }
});

Route::get('/ConnexionAdmin', function () {
    return view('ConnexionAdmin');
});

Route::get('/welcome', function () {
    return view('Welcome');
});
