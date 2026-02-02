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
                return redirect('/PagePrincipale');
            }
        }

        return back()->withErrors(['credentials' => 'Email ou mot de passe incorrect'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('administrateur')->logout();
        Auth::guard('vendeur')->logout();
        Auth::guard('client')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/PagePrincipale');
    }
}
