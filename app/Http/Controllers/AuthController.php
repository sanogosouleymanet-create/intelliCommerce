<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Vendeur;
use App\Models\Administrateur;
use App\Models\Client;

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
                // Restaurer un panier sauvegardé en cookie (si présent) en le fusionnant
                $cookieName = 'saved_cart_admin_' . $admin->idAdministrateur;
                if ($request->cookie($cookieName)) {
                    $saved = json_decode($request->cookie($cookieName), true);
                    if (is_array($saved)) {
                        $key = 'cart_admin_' . $admin->idAdministrateur;
                        $current = session($key, []);
                        foreach ($saved as $pid => $qty) {
                            $current[$pid] = (isset($current[$pid]) ? $current[$pid] + $qty : $qty);
                        }
                        session([$key => $current]);
                    }
                    cookie()->queue(cookie()->forget($cookieName));
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
                // Restaurer un panier sauvegardé en cookie (si présent) en le fusionnant
                $cookieName = 'saved_cart_vendeur_' . $vendeur->idVendeur;
                if ($request->cookie($cookieName)) {
                    $saved = json_decode($request->cookie($cookieName), true);
                    if (is_array($saved)) {
                        $key = 'cart_vendeur_' . $vendeur->idVendeur;
                        $current = session($key, []);
                        foreach ($saved as $pid => $qty) {
                            $current[$pid] = (isset($current[$pid]) ? $current[$pid] + $qty : $qty);
                        }
                        session([$key => $current]);
                    }
                    cookie()->queue(cookie()->forget($cookieName));
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
                // Restaurer un panier sauvegardé en cookie (si présent) en le fusionnant
                $cookieName = 'saved_cart_client_' . $client->idClient;
                if ($request->cookie($cookieName)) {
                    $saved = json_decode($request->cookie($cookieName), true);
                    if (is_array($saved)) {
                        $key = 'cart_client_' . $client->idClient;
                        $current = session($key, []);
                        foreach ($saved as $pid => $qty) {
                            $current[$pid] = (isset($current[$pid]) ? $current[$pid] + $qty : $qty);
                        }
                        session([$key => $current]);
                    }
                    cookie()->queue(cookie()->forget($cookieName));
                }
                return redirect('/PagePrincipale');
            }
        }

        return back()->withErrors(['credentials' => 'Email ou mot de passe incorrect'])->withInput();
    }

    public function logout(Request $request)
    {
        // Avant de déconnecter, sauvegarder le panier courant dans un cookie pour
        // pouvoir le restaurer après une future reconnexion.
        if (Auth::guard('administrateur')->check()) {
            $id = Auth::guard('administrateur')->id();
            $key = 'cart_admin_' . $id;
            $cart = session($key, []);
            cookie()->queue(cookie('saved_cart_admin_' . $id, json_encode($cart), 60 * 24 * 30));
        }
        if (Auth::guard('vendeur')->check()) {
            $id = Auth::guard('vendeur')->id();
            $key = 'cart_vendeur_' . $id;
            $cart = session($key, []);
            cookie()->queue(cookie('saved_cart_vendeur_' . $id, json_encode($cart), 60 * 24 * 30));
        }
        if (Auth::guard('client')->check()) {
            $id = Auth::guard('client')->id();
            $key = 'cart_client_' . $id;
            $cart = session($key, []);
            cookie()->queue(cookie('saved_cart_client_' . $id, json_encode($cart), 60 * 24 * 30));
        }

        Auth::guard('administrateur')->logout();
        Auth::guard('vendeur')->logout();
        Auth::guard('client')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/PagePrincipale');
    }
}
