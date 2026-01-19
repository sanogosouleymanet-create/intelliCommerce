<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendeur;

class PageVendeurController extends Controller
{
    public function index()
    {
        $vendeur = Auth::user();
        if(!$vendeur){
            return redirect()->route('login');
        }
       
        return view('PageVendeur', [
            'vendeur' => $vendeur,
            'produitsCount' => $vendeur->produits()->count(),
            'commandesCount' => $vendeur->commandes()->count(),
            'messagesNonLus' => $vendeur->messages()->where('Lu', false)->count(),
            'alertes' => AlerteIA::where('Vendeur_idVendeur', $vendeur->idVendeur)->latest()->take(5)->get(),
            'commandesRecentes' => $vendeur->commandes()->latest()->take(5)->get(),
            'messagesRecents' => $vendeur->messages()->latest()->take(5)->get(),
        ]);
    }
}