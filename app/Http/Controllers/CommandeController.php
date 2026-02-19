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
        $vendeur = Auth::guard('vendeur')->user();
        $admin = Auth::guard('administrateur')->user();

        if (!$client && !$vendeur && !$admin) {
            return response()->json(['success' => false, 'message' => 'Veuillez vous connecter pour passer commande'], 401);
        }

        // Vérifier si le client est bloqué
        if ($client && !empty($client->Bloque)) {
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Votre compte est limité. Cette action n\'est pas autorisée.',
                    'error' => 'Compte bloqué',
                ], 403);
            }
            return redirect()->route('PageClient')
                ->with('error', 'Votre compte est limité par l\'administrateur. Certaines actions sont désactivées.');
        }

        // If it's a seller or admin, find or create their corresponding client account
        if (($vendeur || $admin) && !$client) {
            $user = $vendeur ?: $admin;
            $client = \App\Models\Client::where('email', $user->email)->first();
            if (!$client) {
                // ensure TelClient is an integer and unique (DB requires integer + unique)
                $tel = $user->TelVendeur ?? $user->TelAdmin ?? null;
                if (empty($tel) || !preg_match('/^\d+$/', (string)$tel)) {
                    // generate a large random unique integer for TelClient
                    do {
                        $telCandidate = random_int(1000000000, 2147483647);
                    } while (\App\Models\Client::where('TelClient', $telCandidate)->exists());
                    $tel = $telCandidate;
                }

                $client = \App\Models\Client::create([
                    'Nom' => $user->Nom,
                    'Prenom' => $user->Prenom ?? '',
                    'DateDeNaissance' => now()->subYears(25)->toDateString(), // Default birthdate
                    'Adresse' => $user->Adresse ?? '',
                    'TelClient' => $tel,
                    'email' => $user->email,
                    'MotDePasse' => $user->MotDePasse,
                    'DateCreation' => now(),
                    'Bloque' => false,
                ]);
            }
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

        // Check if seller is trying to order their own products
        if ($vendeur) {
            foreach ($produits as $p) {
                if ($p->Vendeur_idVendeur == $vendeur->idVendeur) {
                    return response()->json(['success' => false, 'message' => 'Vous ne pouvez pas commander vos propres produits.'], 400);
                }
            }
        }

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
                'MontantTotal' => $total,
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

    public function destroy($id)
    {
        $client = Auth::guard('client')->user();
        if (!$client) {
            return redirect()->route('connexion')->withErrors('Veuillez vous connecter.');
        }

        $commande = Commande::where('idCommande', $id)->where('Client_idClient', $client->idClient)->first();
        if (!$commande) {
            return redirect()->back()->withErrors('Commande introuvable ou non autorisée.');
        }

        // Delete related Produitcommande records
        Produitcommande::where('Commande_idCommande', $commande->idCommande)->delete();

        // Delete the commande
        $commande->delete();

        return redirect('/commandes')->with('success', 'Commande supprimée avec succès.');
    }
}
