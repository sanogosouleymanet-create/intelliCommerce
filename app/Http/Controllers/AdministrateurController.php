<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Administrateur;
use App\Models\Produit;
use App\Models\Vendeur;
use App\Models\Client;

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
        $admin = Administrateur::whereRaw('LOWER(email) = ?', [$email])->first();

        if ($admin && Hash::check($request->motdepasse, $admin->MotDePasse)) {
            Auth::guard('administrateur')->login($admin);
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['credentials' => 'Identifiants invalides'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('administrateur')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    public function dashboard()
    {
        $counts = [
            'produits' => Produit::count(),
            'vendeurs' => Vendeur::count(),
            'clients' => Client::count(),
            'administrateurs' => Administrateur::count(),
        ];

        return view('admin.dashboard', compact('counts'));
    }
}
