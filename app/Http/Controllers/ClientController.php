<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->filled('recherche')) {
            $term = $request->recherche;
            $query->where('Nom', 'like', "%{$term}%")->orWhere('Prenom', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%");
        }

        $clients = $query->get();

        if ($request->ajax()) {
            return view('clients._main', compact('clients'))->render();
        }

        return view('clients.index', compact('clients'));
    }

    public function show($id)
    {
        $client = Client::where('idClient', $id)->firstOrFail();
        return view('clients.show', compact('client'));
    }

    public function FormulaireClient(Request $request)
    {
        // Validation basique
    $message = "Client enregistré avec succès.";
    $validated = $request->validate([
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'mail' => 'required|email|max:255',
        'motdepasse' => 'required|string|min:4|max:8',
         

    ]);
        $client = Client::create([
                    'Nom' => $request->nom,
                    'Prenom' => $request->prenom,
                    'DateDeNaissance' => $request->datenaissance ?? null,
                    'Adresse' => $request->adresse,
                    'TelClient' => $request->tel,
                    'email' => $request->mail,
                    'MotDePasse' => Hash::make($request->motdepasse),
                    'DateCreation' => now(),
        ]);

            // Connecte automatiquement le client créé et redirige vers la page principale
            Auth::guard('client')->login($client);
            $request->session()->regenerate();

            return redirect('/PagePrincipale');
    }
}
