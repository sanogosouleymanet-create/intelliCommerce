<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Vendeur;
use App\Models\Ia_alerte;

class PageVendeurController extends Controller
{
    public function index()
    {
        $vendeur = Auth::guard('vendeur')->user();
        if(!$vendeur){
            return redirect()->route('login');
        }
       
        // commandes are linked to produits via the Produitcommande pivot; the commandes table
        // doesn't have a direct Vendeur_idVendeur column, so query via whereHas on Produit.
        $produitsCount = $vendeur->produits()->count();
        $commandesQuery = \App\Models\Commande::whereHas('Produit', function($q) use ($vendeur) {
            $q->where('Vendeur_idVendeur', $vendeur->idVendeur);
        });

        // Determine how to count unread messages depending on the actual message columns
        if (Schema::hasColumn('messages', 'Lu')) {
            $messagesNonLus = $vendeur->messages()->where('Lu', false)->count();
            $messagesRecents = $vendeur->messages()->orderBy('DateEnvoi', 'desc')->take(5)->get();
        } elseif (Schema::hasColumn('messages', 'Statut')) {
            // assume Statut==0 means unread when 'Lu' doesn't exist
            $messagesNonLus = $vendeur->messages()->where('Statut', 0)->count();
            $messagesRecents = $vendeur->messages()->orderBy('DateEnvoi', 'desc')->take(5)->get();
        } else {
            $messagesNonLus = $vendeur->messages()->count();
            $messagesRecents = $vendeur->messages()->orderBy('DateEnvoi', 'desc')->take(5)->get();
        }

        return view('PageVendeur', [
            'vendeur' => $vendeur,
            'produitsCount' => $produitsCount,
            'commandesCount' => $commandesQuery->count(),
            'messagesNonLus' => $messagesNonLus,
            'commandesRecentes' => $commandesQuery->orderBy('DateCommande', 'desc')->take(5)->get(),
            'messagesRecents' => $messagesRecents,
        ]);
    }
}