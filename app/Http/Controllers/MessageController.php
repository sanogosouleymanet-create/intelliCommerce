<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Message;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $vendeur = Auth::guard('vendeur')->user();
        $query = Message::query();

        if ($vendeur) {
            $query->where('Vendeur_idVendeur', $vendeur->idVendeur);
        }

        $query->orderBy('DateEnvoi', 'desc');

        $messages = $query->get();

        if ($request->query('partial') == '1') {
            $html = view('messages._list', compact('messages'))->render();
            return response($html);
        }

        return view('messages.index', compact('messages', 'vendeur'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Contenu' => 'required|string',
            'Client_idClient' => 'nullable|integer',
            'Vendeur_idVendeur' => 'nullable|integer',
        ]);

        $message = Message::create([
            'Contenu' => $validated['Contenu'],
            'DateEnvoi' => now(),
            'Statut' => 0,
            'Client_idClient' => $validated['Client_idClient'] ?? null,
            'Vendeur_idVendeur' => $validated['Vendeur_idVendeur'] ?? Auth::guard('vendeur')->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Message envoyé', 'id' => $message->idMessage]);
    }

    public function markAsRead(Request $request, $id)
    {
        $message = Message::where('idMessage', $id)->firstOrFail();

        if (Schema::hasColumn('messages', 'Lu')) {
            $message->Lu = true;
        } elseif (Schema::hasColumn('messages', 'Statut')) {
            $message->Statut = 1;
        }

        $message->save();

        return response()->json(['success' => true]);
    }
}
