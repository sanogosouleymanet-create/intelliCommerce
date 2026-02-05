<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // show cart
    public function index(Request $request)
    {
        $key = $this->cartKey($request);
        $cart = session($key, []);
        $items = [];
        $total = 0;
        foreach ($cart as $id => $qty) {
            $p = Produit::where('idProduit', $id)->first();
            if (!$p) continue;
            $subtotal = ($p->Prix ?? 0) * $qty;
            $items[] = ['produit' => $p, 'qty' => $qty, 'subtotal' => $subtotal];
            $total += $subtotal;
        }
        return view('cart.index', compact('items', 'total'));
    }

    // add item (AJAX)
    public function add(Request $request)
    {
        $id = $request->input('id');
        $qty = max(1, (int) $request->input('qty', 1));
        $produit = Produit::where('idProduit', $id)->first();
        if (!$produit) return response()->json(['success' => false, 'message' => 'Produit introuvable'], 404);
        if (($produit->Stock ?? 0) < $qty) return response()->json(['success' => false, 'message' => 'Stock insuffisant'], 400);

        $key = $this->cartKey($request);
        $cart = session($key, []);
        if (isset($cart[$id])) $cart[$id] += $qty; else $cart[$id] = $qty;
        session([$key => $cart]);

        // compute summary
        $count = array_sum($cart);
        $total = 0;
        foreach ($cart as $pid => $q) {
            $p = Produit::where('idProduit', $pid)->first();
            if (!$p) continue;
            $total += ($p->Prix ?? 0) * $q;
        }

        return response()->json(['success' => true, 'count' => $count, 'total' => $total]);
    }

    public function remove(Request $request)
    {
        $id = $request->input('id');
        $key = $this->cartKey($request);
        $cart = session($key, []);
        if (isset($cart[$id])) unset($cart[$id]);
        session([$key => $cart]);
        return response()->json(['success' => true, 'count' => array_sum($cart), 'total' => $this->computeTotal($cart)]);
    }

    public function update(Request $request)
    {
        $id = $request->input('id');
        $qty = max(0, (int) $request->input('qty', 0));
        $key = $this->cartKey($request);
        $cart = session($key, []);
        if ($qty <= 0) {
            if (isset($cart[$id])) unset($cart[$id]);
        } else {
            $p = Produit::where('idProduit', $id)->first();
            if (!$p) return response()->json(['success' => false, 'message' => 'Produit introuvable'], 404);
            $cart[$id] = $qty;
        }
        session([$key => $cart]);
        return response()->json(['success' => true, 'count' => array_sum($cart), 'total' => $this->computeTotal($cart)]);
    }

    protected function computeTotal($cart)
    {
        $total = 0;
        foreach ($cart as $pid => $q) {
            $p = Produit::where('idProduit', $pid)->first();
            if (!$p) continue;
            $total += ($p->Prix ?? 0) * $q;
        }
        return $total;
    }

    /**
     * Determine session key to store the cart for the current requester.
     */
    protected function cartKey(Request $request)
    {
        if (Auth::guard('client')->check()) {
            $u = Auth::guard('client')->user();
            return 'cart_client_' . $u->getAuthIdentifier();
        }
        if (Auth::guard('vendeur')->check()) {
            $u = Auth::guard('vendeur')->user();
            return 'cart_vendeur_' . $u->getAuthIdentifier();
        }
        if (Auth::guard('administrateur')->check()) {
            $u = Auth::guard('administrateur')->user();
            return 'cart_admin_' . $u->getAuthIdentifier();
        }
        return 'cart_guest_' . $request->session()->getId();
    }
}
