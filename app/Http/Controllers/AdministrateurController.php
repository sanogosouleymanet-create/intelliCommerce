<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Administrateur;
use App\Models\Produit;
use App\Models\Vendeur;
use App\Models\Client;
use App\Models\Ia_alerte;

class AdministrateurController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'motdepasse' => 'required|string',
        ]);

        $email = trim(strtolower($request->email));
        $pwd = trim($request->motdepasse);
        $admin = Administrateur::whereRaw('LOWER(email) = ?', [$email])->first();

        if ($admin) {
            $stored = $admin->MotDePasse;
            // Detect common hash prefixes (bcrypt/argon2). If not present, assume plain text.
            $isHashed = $stored && (preg_match('/^\$2[aby]\$|^\$argon2/', $stored) === 1);

            if (Hash::check($pwd, $stored) || (!$isHashed && $stored === $pwd)) {
                // If stored password was plain text, re-hash it for security
                if (!$isHashed && $stored === $pwd) {
                    $admin->MotDePasse = Hash::make($pwd);
                    $admin->save();
                }

                Auth::guard('administrateur')->login($admin);
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }
        }

        return back()->withErrors(['credentials' => 'Identifiants invalides'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('administrateur')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // After logout, redirect to the main page
        return redirect('/PagePrincipale');
    }

    public function dashboard(Request $request)
    {
        $admin = Auth::guard('administrateur')->user();
        $counts = [
            'produits' => Produit::count(),
            'vendeurs' => Vendeur::count(),
            'clients' => Client::count(),
            'administrateurs' => Administrateur::count(),
            'ia_alertes' => Ia_alerte::count(),
        ];
        return view('admin.dashboard', compact('counts', 'admin'));
    }

    public function iaAlerts()
    {
        $alerts = Ia_alerte::orderBy('DateCreation', 'desc')->get();
        return view('admin.ia_alertes', compact('alerts'));
    }

    public function produits()
    {
        $produits = \App\Models\Produit::with('vendeur')->get();
        return view('admin.produits', compact('produits'));
    }

    public function clients()
    {
        return view('admin.clients');
    }

    public function vendeurs()
    {
        return view('admin.vendeurs');
    }
}
