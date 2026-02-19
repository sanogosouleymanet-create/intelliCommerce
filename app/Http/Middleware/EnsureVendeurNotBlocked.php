<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureVendeurNotBlocked
{
    /**
     * Block access to write actions when the vendor account is blocked by admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $vendeur = Auth::guard('vendeur')->user();
        if (!$vendeur) {
            return $next($request);
        }

        if (!empty($vendeur->Bloque)) {
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Votre compte est limité. Cette action n\'est pas autorisée.',
                    'error' => 'Compte bloqué',
                ], 403);
            }

            return redirect()->route('PageVendeur')
                ->with('error', 'Votre compte est limité par l\'administrateur. Certaines actions sont désactivées.');
        }

        return $next($request);
    }
}
