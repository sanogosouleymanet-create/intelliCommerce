<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\VendeurController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Vendeur;


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

    $vend = Vendeur::create(
        [	
        'Nom' => $request->Nom,
        'Prenom' => $request->Prenom,
        'Adresse' => $request->Adresse,
        'TelVendeur' => $request->TelVendeur,
        'email' => $request->email,
        'NomBoutique' => $request->NomBoutique,
        'MotDePasse' => Hash::make($request->MotDePasse),
        ]
    );
    return view('/formulaireVendeur', compact('message'));
});


Route::get('/formulaireClient', function () {
    return view('formulaireClient');
});

Route::get('/ConnexionVendeur', function () {
    return view('ConnexionVendeur');
});

Route::get('/ConnexionClient', function () {
    return view('ConnexionClient');
});

Route::get('/P1Client', function () {
    return view('P1Client');
});

Route::get('/ConnexionAdmin', function () {
    return view('ConnexionAdmin');
});

Route::get('/welcome', function () {
    return view('Welcome');
});
