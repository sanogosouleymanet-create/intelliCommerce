<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Produit;
use Illuminate\Support\Facades\Storage;

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
    }elseif($request->tri_prix == 'recente'){
        $query->orderBy('DateAjout', 'desc');
    }

    $produits = $query->get();
    if ($request->ajax()) {
        return view('vendeurs.produits._list', compact('produits', 'vendeur'))->render();
    }
    return view('vendeurs.produits', compact('produits', 'vendeur'));
}


    public function AjouterProduit(Request $request)
    {
        // Validation basique
        $validated = $request->validate([
            'Nom' => 'required|string|max:255',
            'Description' => 'required|string',
            'Prix' => 'required|numeric',
            'Stock' => 'required|integer|min:0',
            'Categorie' => 'nullable|string|max:255',
            'Image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $vendeur = Auth::guard('vendeur')->user();

        $path = $request->file('Image')->store('Images', 'public');

        // Création d'un produit: utiliser le stock initial fourni par le vendeur
        try {
            $produit = Produit::create([
                'Nom' => $request->Nom,
                'Description' => $request->Description,
                'Prix' => $request->Prix,
                'Stock' => (int) ($request->Stock ?? 0),
                'Categorie' => $request->Categorie ?? null,
                'Image' => $path,
                'DateAjout' => now(),
                'Vendeur_idVendeur' => $vendeur?->idVendeur,
            ]);
        } catch (\Throwable $e) {
            \Log::error('AjouterProduit failed: ' . $e->getMessage());
            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => false, 'message' => 'Impossible d\'ajouter le produit'], 500);
            }
            return redirect()->back()->with('error', 'Impossible d\'ajouter le produit.');
        }

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Produit ajouté avec succès',
            ]);
        }

        return redirect('/vendeur/produits');

    }

    // Show product details
    public function show(Request $request, $id)
    {
        $vendeur = Auth::guard('vendeur')->user();
        $produit = Produit::where('idProduit', $id)->where('Vendeur_idVendeur', $vendeur->idVendeur)->firstOrFail();
        if ($request->ajax()) {
            // return the partial used by the PageVendeur SPA
            return view('vendeurs.produits.show', compact('produit', 'vendeur'))->render();
        }
        // If this is a top-level (non-AJAX) request, redirect into the SPA so the fragment
        // is displayed inside PageVendeur and not as a bare HTML fragment.
        return redirect()->to(route('PageVendeur') . '?product=' . $produit->idProduit);
    }

    // Public-facing product detail (no vendeur auth required)
    public function publicShow($id)
    {
        $produit = Produit::where('idProduit', $id)->firstOrFail();
        $vendeur = null;
        try {
            $vendeurModel = '\\App\\Models\\Vendeur';
            $vendeur = $vendeurModel::find($produit->Vendeur_idVendeur);
        } catch (\Throwable $e) {
            $vendeur = null;
        }
        // If this is an AJAX request, return only the fragment so it can be injected in-page
        if (request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('produits._public_fragment', compact('produit', 'vendeur'));
        }

        // For non-AJAX public requests, redirect to the homepage with a query parameter
        // so the main page can load the fragment via AJAX and display the detail inline.
        return redirect()->to(url('/') . '?produit=' . $produit->idProduit);
    }

    // Update product (POST form with _method=PUT or direct POST)
    public function update(Request $request, $id)
    {
        $vendeur = Auth::guard('vendeur')->user();
        $produit = Produit::where('idProduit', $id)->where('Vendeur_idVendeur', $vendeur->idVendeur)->firstOrFail();

        $validated = $request->validate([
            'Nom' => 'required|string|max:255',
            'Description' => 'required|string',
            'Prix' => 'required|numeric',
            'Stock' => 'required|integer|min:0',
            'Categorie' => 'nullable|string|max:255',
            'Image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('Image')) {
            // remove old image if exists
            if ($produit->Image) {
                Storage::disk('public')->delete($produit->Image);
            }
            $path = $request->file('Image')->store('Images', 'public');
            $produit->Image = $path;
        }

        $produit->Nom = $request->Nom;
        $produit->Description = $request->Description;
        $produit->Prix = $request->Prix;
        $produit->Stock = (int) $request->Stock;
        $produit->Categorie = $request->Categorie ?? null;
        $produit->save();

        return response()->json(['success' => true, 'message' => 'Produit mis à jour', 'produit' => $produit]);
    }

    // Delete product
    public function destroy(Request $request, $id)
    {
        $vendeur = Auth::guard('vendeur')->user();
        $produit = Produit::where('idProduit', $id)->where('Vendeur_idVendeur', $vendeur->idVendeur)->firstOrFail();
        if ($produit->Image) {
            Storage::disk('public')->delete($produit->Image);
        }
        $produit->delete();
        return response()->json(['success' => true, 'message' => 'Produit supprimé']);
    }


}