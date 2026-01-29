<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommandeController extends Controller
{
    public function index()
    {
        $vendeur = Auth::guard('vendeur')->user();
        $commandes = Commande::all();
        return view('commandes.index', compact('commandes', 'vendeur'));
    }
    public function ListeCommandes(Request $request)
    {
        $query = Commande::query();

        if ($request->filled('recherche')) {
            $term = $request->recherche;
            $query->whereHas('Client', function($q) use ($term) {
                $q->where('Nom', 'like', "%{$term}%")->orWhere('Prenom', 'like', "%{$term}%");
            });
        }

        if ($request->filled('statut')) {
            $query->where('Statut', $request->statut);
        }

        if ($request->filled('date_from')) {
            $query->where('DateCommande', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('DateCommande', '<=', $request->date_to);
        }

        $commandes = $query->get();

        if ($request->query('partial') == '1') {
            $html = view('commandes._list', compact('commandes'))->render();
            return response($html);
        }

        return view('commandes.index', compact('commandes'));
    }

    public function show($id)
    {
        $commande = Commande::with('Produit')->where('idCommande', $id)->firstOrFail();
        return view('commandes.show', compact('commande'));
    }
}
