<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendeur;

class VendeurController extends Controller
{
    public function index()
    {
        $vendeurs = Vendeur::all();
        return view('vendeurs.index', compact('vendeurs'));
    }


    public function FormulaireVendeur(Request $request)
    {
       // Validation basique
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
            // Connecte automatiquement le vendeur créé et redirige vers son tableau de bord
            Auth::guard('vendeur')->login($vend);
            $request->session()->regenerate();

            return redirect()->route('PageVendeur');
    }
}
