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
        if ($admin && Hash::check($password, $admin->MotDePasse)) {
            Auth::guard('administrateur')->login($admin);
            $request->session()->regenerate();
            return redirect('/PagePrincipale');
        }

        $vendeur = Vendeur::whereRaw('LOWER(email) = ?', [$email])->first();
        if ($vendeur && Hash::check($password, $vendeur->MotDePasse)) {
            Auth::guard('vendeur')->login($vendeur);
            $request->session()->regenerate();
            return redirect('/PagePrincipale');
        }

        $client = Client::whereRaw('LOWER(email) = ?', [$email])->first();
        if ($client && Hash::check($password, $client->MotDePasse)) {
            Auth::guard('client')->login($client);
            $request->session()->regenerate();
            return redirect('/PagePrincipale');
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
