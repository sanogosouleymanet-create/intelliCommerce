<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalysesController extends Controller
{
    public function index(Request $request)
    {
        $vendeur = Auth::guard('vendeur')->user();
        $produitsCount = $vendeur ? $vendeur->produits()->count() : 0;

        $commandesQuery = \App\Models\Commande::whereHas('Produit', function($q) use ($vendeur) {
            $q->where('Vendeur_idVendeur', $vendeur?->idVendeur);
        });
        $commandesCount = $commandesQuery->count();

        $ventes30 = $commandesQuery->where('DateCommande', '>=', now()->subDays(30))->sum('MontanTotal');

        // Top products (by quantity) for this vendeur in the last 30 days
        $topPivot = \App\Models\Produitcommande::whereHas('produit', function($q) use ($vendeur) {
            $q->where('Vendeur_idVendeur', $vendeur?->idVendeur);
        })->where('DateAjout', '>=', now()->subDays(30))
          ->selectRaw('Produit_idProduit, SUM(Quantite) as total')
          ->groupBy('Produit_idProduit')
          ->orderByDesc('total')
          ->take(5)
          ->get();

        $topProducts = $topPivot->map(function($p){
            $prod = \App\Models\Produit::where('idProduit', $p->Produit_idProduit)->first();
            return ['nom' => $prod?->Nom ?? 'Produit #'.$p->Produit_idProduit, 'ventes' => (int)$p->total];
        });

        return view('analyses.index', compact('vendeur','produitsCount','commandesCount','ventes30','topProducts'));
    }
}
