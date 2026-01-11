<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendeur;

class VendeurController extends Controller
{
    public function index()
    {
        $vendeurs = Vendeur::all();
        return view('vendeurs.index', compact('vendeurs'));
    }
}
