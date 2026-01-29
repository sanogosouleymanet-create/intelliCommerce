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

    public function parametres()
    {
        $vendeur = Auth::guard('vendeur')->user();
        return view('parametres.index', compact('vendeur'));
    }

    public function updateSettings(Request $request)
    {
        $vendeur = Auth::guard('vendeur')->user();
        if (!$vendeur) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 401);
        }

        $validated = $request->validate([
            'NomBoutique' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:vendeurs,email,' . $vendeur->idVendeur . ',idVendeur',
            'Nom' => 'nullable|string|max:255',
            'Prenom' => 'nullable|string|max:255',
            'Adresse' => 'nullable|string|max:500',
            'TelVendeur' => 'nullable|string|max:50',
        ]);

        $vendeur->fill([
            'NomBoutique' => $validated['NomBoutique'] ?? $vendeur->NomBoutique,
            'email' => $validated['email'] ?? $vendeur->email,
            'Nom' => $validated['Nom'] ?? $vendeur->Nom,
            'Prenom' => $validated['Prenom'] ?? $vendeur->Prenom,
            'Adresse' => $validated['Adresse'] ?? $vendeur->Adresse,
            'TelVendeur' => $validated['TelVendeur'] ?? $vendeur->TelVendeur,
        ]);

        $vendeur->save();

        return response()->json(['success' => true, 'message' => 'Paramètres mis à jour', 'vendeur' => $vendeur]);
    }
}
