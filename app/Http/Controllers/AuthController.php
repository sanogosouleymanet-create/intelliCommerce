<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Vendeur;
use App\Models\Administrateur;
use App\Models\Client;
use App\Models\SavedCart;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('Connexion');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'motdepasse' => 'required|string',
        ]);

        $email = trim(strtolower($request->email));
        $password = $request->motdepasse;

        // Priority: administrateur -> vendeur -> client
        $admin = Administrateur::whereRaw('LOWER(email) = ?', [$email])->first();
        if ($admin) {
            $stored = $admin->MotDePasse;
            $isHashed = $stored && (preg_match('/^\$2[aby]\$|^\$argon2/', $stored) === 1);
            if (Hash::check($password, $stored) || (!$isHashed && $stored === $password)) {
                if (!$isHashed && $stored === $password) {
                    $admin->MotDePasse = Hash::make($password);
                    $admin->save();
                }
                Auth::guard('administrateur')->login($admin);
                $request->session()->regenerate();
                // Restaurer le panier sauvegardé en base (si présent) en le fusionnant
                $key = 'cart_admin_' . $admin->idAdministrateur;
                $current = session($key, []);
                $saved = SavedCart::where('guard', 'administrateur')->where('user_id', (string)$admin->getAuthIdentifier())->first();
                if ($saved && is_array($saved->cart)) {
                    foreach ($saved->cart as $pid => $qty) {
                        $current[$pid] = (isset($current[$pid]) ? $current[$pid] + $qty : $qty);
                    }
                    session([$key => $current]);
                    SavedCart::updateOrCreate(['guard' => 'administrateur', 'user_id' => (string)$admin->getAuthIdentifier()], ['cart' => $current]);
                }
                return redirect('/PagePrincipale');
            }
        }

        $vendeur = Vendeur::whereRaw('LOWER(email) = ?', [$email])->first();
        if ($vendeur) {
            $stored = $vendeur->MotDePasse;
            $isHashed = $stored && (preg_match('/^\$2[aby]\$|^\$argon2/', $stored) === 1);
            if (Hash::check($password, $stored) || (!$isHashed && $stored === $password)) {
                if (!$isHashed && $stored === $password) {
                    $vendeur->MotDePasse = Hash::make($password);
                    $vendeur->save();
                }
                Auth::guard('vendeur')->login($vendeur);
                $request->session()->regenerate();
                // Restaurer le panier sauvegardé en base (si présent) en le fusionnant
                $key = 'cart_vendeur_' . $vendeur->idVendeur;
                $current = session($key, []);
                $saved = SavedCart::where('guard', 'vendeur')->where('user_id', (string)$vendeur->getAuthIdentifier())->first();
                if ($saved && is_array($saved->cart)) {
                    foreach ($saved->cart as $pid => $qty) {
                        $current[$pid] = (isset($current[$pid]) ? $current[$pid] + $qty : $qty);
                    }
                    session([$key => $current]);
                    SavedCart::updateOrCreate(['guard' => 'vendeur', 'user_id' => (string)$vendeur->getAuthIdentifier()], ['cart' => $current]);
                }
                return redirect('/PagePrincipale');
            }
        }

        $client = Client::whereRaw('LOWER(email) = ?', [$email])->first();
        if ($client) {
            $stored = $client->MotDePasse;
            $isHashed = $stored && (preg_match('/^\$2[aby]\$|^\$argon2/', $stored) === 1);
            if (Hash::check($password, $stored) || (!$isHashed && $stored === $password)) {
                if (!$isHashed && $stored === $password) {
                    $client->MotDePasse = Hash::make($password);
                    $client->save();
                }
                Auth::guard('client')->login($client);
                $request->session()->regenerate();
                // Restaurer le panier sauvegardé en base (si présent) en le fusionnant
                $key = 'cart_client_' . $client->idClient;
                $current = session($key, []);
                $saved = SavedCart::where('guard', 'client')->where('user_id', (string)$client->getAuthIdentifier())->first();
                if ($saved && is_array($saved->cart)) {
                    foreach ($saved->cart as $pid => $qty) {
                        $current[$pid] = (isset($current[$pid]) ? $current[$pid] + $qty : $qty);
                    }
                    session([$key => $current]);
                    SavedCart::updateOrCreate(['guard' => 'client', 'user_id' => (string)$client->getAuthIdentifier()], ['cart' => $current]);
                }
                return redirect('/PagePrincipale');
            }
        }

        return back()->withErrors(['credentials' => 'Email ou mot de passe incorrect'])->withInput();
    }

    public function logout(Request $request)
    {
        // Avant déconnexion, s'assurer que le panier courant est sauvegardé en base
        if (Auth::guard('administrateur')->check()) {
            $id = (string) Auth::guard('administrateur')->id();
            $key = 'cart_admin_' . $id;
            $cart = session($key, []);
            SavedCart::updateOrCreate(['guard' => 'administrateur', 'user_id' => $id], ['cart' => $cart]);
        }
        if (Auth::guard('vendeur')->check()) {
            $id = (string) Auth::guard('vendeur')->id();
            $key = 'cart_vendeur_' . $id;
            $cart = session($key, []);
            SavedCart::updateOrCreate(['guard' => 'vendeur', 'user_id' => $id], ['cart' => $cart]);
        }
        if (Auth::guard('client')->check()) {
            $id = (string) Auth::guard('client')->id();
            $key = 'cart_client_' . $id;
            $cart = session($key, []);
            SavedCart::updateOrCreate(['guard' => 'client', 'user_id' => $id], ['cart' => $cart]);
        }

        Auth::guard('administrateur')->logout();
        Auth::guard('vendeur')->logout();
        Auth::guard('client')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/PagePrincipale');
    }
}
