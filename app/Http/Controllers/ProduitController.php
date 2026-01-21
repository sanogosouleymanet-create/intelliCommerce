<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Produit;

class ProduitController extends Controller
{
    public function index(Request $request)
{
    $vendeur = Auth::guard('vendeur')->user();

    $query = Produit::where('Vendeur_idVendeur', $vendeur->idVendeur);

    //Recherche par nom de produit
    if($request->filled('recherche')){
        $query->where('Nom', 'like', '%' . $request->recherche . '%');
    }

    // Recherche par catégorie
    if($request->filled('categorie')){
        $query->where('Categorie', $request->categorie);
    }

    //Tri par prix
    if($request->tri_prix == 'asc'){
        $query->orderBy('Prix', 'asc');
    } elseif($request->tri_prix == 'desc'){
        $query->orderBy('Prix', 'desc');
    }elseif($request->tri_prix == 'recent'){
        $query->orderBy('DateAjout', 'desc');
    }

    $produits = $query->get();

    return view('produits.index', compact('produits'));
}


    public function AjouterProduit(Request $request)
    {
        // Validation basique
        $validated = $request->validate([
            'Nom' => 'required|string|max:255',
            'Description' => 'required|string',
            'Prix' => 'required|numeric',
            'Categorie' => 'nullable|string|max:255',
            'Image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $vendeur = Auth::guard('vendeur')->user();

        $path = $request->file('Image')->store('images', 'public');

        // Création d'un produit: le stock est calculé automatiquement (initialisé à 0)
        $produit = Produit::create([
            'Nom' => $request->Nom,
            'Description' => $request->Description,
            'Prix' => $request->Prix,
            'Stock' => 0,
            'Categorie' => $request->Categorie ?? null,
            'Image' => $path,
            'DateAjout' => now(),
            'Vendeur_idVendeur' => $vendeur?->idVendeur,
        ]);

        return 
            response()->json([
                'success' => true,
                'message' => 'Produit ajouté avec succès',
            ]);

    }


}
