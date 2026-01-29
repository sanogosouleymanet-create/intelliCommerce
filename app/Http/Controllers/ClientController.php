<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

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

        if ($request->query('partial') == '1') {
            $html = view('clients._list', compact('clients'))->render();
            return response($html);
        }

        return view('clients.index', compact('clients'));
    }

    public function show($id)
    {
        $client = Client::where('idClient', $id)->firstOrFail();
        return view('clients.show', compact('client'));
    }
}
