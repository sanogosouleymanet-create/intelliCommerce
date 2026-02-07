<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Produit;
use App\Models\Produitcommande;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    /**
     * Store a new order from the session cart (AJAX POST).
     * Expects `selected_products[]` optional array of product ids to include; otherwise uses whole cart.
     */
    public function store(Request $request)
    {
        $client = Auth::guard('client')->user();
        if (!$client) {
            return response()->json(['success' => false, 'message' => 'Veuillez vous connecter pour passer commande'], 401);
        }

        // determine cart key (same logic as CartController)
        if (Auth::guard('client')->check()) {
            $cartKey = 'cart_client_' . Auth::guard('client')->id();
        } elseif (Auth::guard('vendeur')->check()) {
            $cartKey = 'cart_vendeur_' . Auth::guard('vendeur')->id();
        } elseif (Auth::guard('administrateur')->check()) {
            $cartKey = 'cart_admin_' . Auth::guard('administrateur')->id();
        } else {
            $cartKey = 'cart_guest_' . $request->session()->getId();
        }

        $originalCart = session($cartKey, []);
        $selected = $request->input('selected_products', null);
        // Determine which items we'll order: either the selected subset or the whole cart
        if (is_array($selected) && !empty($selected)) {
            $cart = array_filter($originalCart, function($qty, $pid) use ($selected) { return in_array($pid, $selected); }, ARRAY_FILTER_USE_BOTH);
        } else {
            $cart = $originalCart;
        }

        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Votre panier est vide'], 400);
        }

        // build lines, check availability, compute total
        $prodIds = array_keys($cart);
        $produits = Produit::whereIn('idProduit', $prodIds)->get()->keyBy('idProduit');
        $total = 0;
        foreach ($cart as $pid => $qty) {
            $p = $produits->get($pid);
            if (!$p) return response()->json(['success' => false, 'message' => "Produit introuvable ({$pid})"], 404);
            if (($p->Stock ?? 0) < $qty) return response()->json(['success' => false, 'message' => "Stock insuffisant pour {$p->Nom}"], 400);
            $total += ($p->Prix ?? 0) * $qty;
        }

        DB::beginTransaction();
        try {
            $commande = Commande::create([
                'DateCommande' => now(),
                'Statut' => 'en cours',
                'MontanTotal' => $total,
                'Client_idClient' => $client->idClient,
            ]);

            foreach ($cart as $pid => $qty) {
                $p = $produits->get($pid);
                Produitcommande::create([
                    'Produit_idProduit' => $p->idProduit,
                    'Commande_idCommande' => $commande->idCommande,
                    'Quantite' => $qty,
                    'PrixUnitaire' => $p->Prix,
                    'DateAjout' => now(),
                ]);
                // optional: decrement stock
                if (isset($p->Stock)) {
                    $p->Stock = max(0, $p->Stock - $qty);
                    $p->save();
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Commande enregistrée', 'commande_id' => $commande->idCommande]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Commande store error: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
            $msg = env('APP_DEBUG') ? $e->getMessage() : 'Erreur lors de l enregistrement de la commande';
            return response()->json(['success' => false, 'message' => $msg], 500);
        }
    }
}
