<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos - Site Intelli-Commerce</title>
    <link rel="stylesheet" href="{{ asset('css/StylePagePrincipale.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ENjdO4Dr2bkBIFxQpeoA6VZ6bQZ6Y9o2e2Z1ZlFZC+0h5Y5n3/tf6Yb6Y1Y3pXx+" crossorigin="anonymous">
</head>
<body>
    <div id="page" class="site">
        <header>
            <div class="header-top mobile-hide">
                <div class="conteiner">
                    <div class="wrapper flexitem">
                        <div class="left">
                        </div>
                        <div class="right">
                            <ul class="flexitem main-links">

                             <li class="main-links">
                                @php
                                        $admin = Auth::guard('administrateur')->user();
                                        $vendeur = Auth::guard('vendeur')->user();
                                        $client = Auth::guard('client')->user();

                                        // compute cart count/total per user or guest (same logic as CartController::cartKey)
                                        $cartCount = 0;
                                        $cartTotal = 0;
                                        if(auth()->guard('client')->check()){
                                            $cartKey = 'cart_client_' . auth()->guard('client')->id();
                                        } elseif(auth()->guard('vendeur')->check()){
                                            $cartKey = 'cart_vendeur_' . auth()->guard('vendeur')->id();
                                        } elseif(auth()->guard('administrateur')->check()){
                                            $cartKey = 'cart_admin_' . auth()->guard('administrateur')->id();
                                        } else {
                                            $cartKey = 'cart_guest_' . session()->getId();
                                        }
                                        $cart = session($cartKey, []);
                                        if(is_array($cart) && !empty($cart)){
                                            $cartCount = array_sum($cart);
                                            $prodIds = array_keys($cart);
                                            $prods = \App\Models\Produit::whereIn('idProduit', $prodIds)->get()->keyBy('idProduit');
                                            foreach($cart as $pid => $q){
                                                $p = $prods->get($pid);
                                                if($p) $cartTotal += ($p->Prix ?? 0) * $q;
                                            }
                                        }
                                    @endphp
                                @if($admin || $vendeur || $client)
                                    @php
                                        $user = $admin ?? $vendeur ?? $client;
                                        $displayName = trim($user->Nom . ' ' . ($user->Prenom ?? ''));
                                        if($admin) {
                                            $profileUrl = route('admin.dashboard');
                                        } elseif($vendeur) {
                                            $profileUrl = route('PageVendeur');
                                        } else {
                                            $profileUrl = route('PageClient');
                                        }
                                    @endphp
                                    <div style="display:flex;align-items:center;gap:12px">
                                        <button type="button" onclick="location.href='{{ $profileUrl }}'" class="login">
                                            <i class="fa-solid fa-user"></i>
                                            <span>{{ $displayName }}</span>
                                    </div>
                                @else
                                     <button onclick="window.location.href='/Connexion'" style="margin-left:10px;padding:6px 10px;border-radius:4px;border:1px solid #ddd;background:#fff;color:#2b7cff;cursor:pointer">S'inscrire/Se Connecter</button>
                                @endif
                            </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-nav">
                <container>
                    <div class="wrapper flexitem">
                        <a href="#" class="trigger desktop-hide"><i class="ri-menu-3-line"></i></a>
                        <div class="left flexitem">
                            <div class="logo"><a href="/"><span class="circle"></span><img src="Logo-site.png" width="250" alt="logo"></a></div>
                            <nav class="mobile-hide">
                                <ul class="flexitem second-links">
                                    <li><a href="/">Accueil</a></li>
                                    <li><a href="/a-propos" class="active">À propos</a></li>
                                    <li><a href="/contact">Contact</a></li>
                                </ul>
                            </nav>
                        </div>
                        <div class="right">
                            <ul class="flexitem second-links">
                                <li><a href="#" class="iscart">
                                    <div class="icon-large"><i class="ri-shopping-cart-line"></i></div>
                                    <div class="fly-item"><span class="item-number">{{ $cartCount }}</span></div>
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </container>
            </div>
        </header>

        <main>
            <div class="container py-5">
                <div class="row">
                    <div class="col-12">
                        <h1 class="text-center mb-4">À propos de Intelli-Commerce</h1>
                        <div class="row">
                            <div class="col-md-8 mx-auto">
                                <p class="lead text-center mb-4">
                                    Bienvenue sur Intelli-Commerce, votre plateforme e-commerce intelligente dédiée à la vente de produits de qualité.
                                </p>
                                <h2>Notre Mission</h2>
                                <p>
                                    Chez Intelli-Commerce, nous nous engageons à offrir une expérience d'achat en ligne exceptionnelle en connectant les vendeurs et les clients de manière transparente et sécurisée. Notre plateforme utilise des technologies avancées pour faciliter les transactions et améliorer l'expérience utilisateur.
                                </p>
                                <h2>Nos Valeurs</h2>
                                <ul>
                                    <li><strong>Qualité :</strong> Nous sélectionnons rigoureusement nos vendeurs et produits pour garantir la meilleure qualité.</li>
                                    <li><strong>Innovation :</strong> Nous intégrons constamment les dernières technologies pour améliorer notre service.</li>
                                    <li><strong>Fiabilité :</strong> La sécurité et la confiance sont au cœur de toutes nos opérations.</li>
                                    <li><strong>Communauté :</strong> Nous construisons une communauté de vendeurs et d'acheteurs satisfaits.</li>
                                </ul>
                                <h2>Notre Équipe</h2>
                                <p>
                                    Notre équipe est composée de professionnels passionnés par le e-commerce et déterminés à révolutionner l'expérience d'achat en ligne. Nous travaillons ensemble pour créer une plateforme qui répond aux besoins de tous nos utilisateurs.
                                </p>
                                <h2>Contactez-nous</h2>
                                <p>
                                    Pour toute question ou suggestion, n'hésitez pas à nous contacter via notre <a href="/contact">page de contact</a>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            <div class="container py-4">
                <div class="row">
                    <div class="col-12 text-center">
                        <p>&copy; 2024 Intelli-Commerce. Tous droits réservés.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
</body>
    <div id="toast-container" style="position:fixed;right:16px;bottom:16px;z-index:2000;display:flex;flex-direction:column;gap:8px"></div>
    <!-- Mini-cart modal -->
    <div id="mini-cart-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:2000;align-items:center;justify-content:flex-end;padding:24px;">
        <div id="mini-cart-modal" style="width:720px;max-width:96%;max-height:92vh;overflow:auto;background:#fff;border-radius:12px;margin-left:8px;box-shadow:0 12px 40px rgba(0,0,0,0.35);border:1px solid rgba(0,0,0,0.05);">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 18px;border-bottom:1px solid #f1f1f1;background:linear-gradient(90deg,#f7fafc,#ffffff);border-top-left-radius:12px;border-top-right-radius:12px">
                <div style="display:flex;align-items:center;gap:10px">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 6H4V4h2v2zM20 6h-2V4h2v2zM6 20H4v-2h2v2zM20 20h-2v-2h2v2z" fill="#0b66d1"/></svg>
                    <strong style="font-size:1.05rem">Mon panier</strong>
                </div>
                <button id="mini-cart-close" aria-label="Fermer le panier" style="border:0;background:transparent;font-size:18px;padding:6px 8px;cursor:pointer">✕</button>
            </div>
            <div id="mini-cart-body" style="padding:14px;display:block;">
                <div style="text-align:center;color:#666;padding:28px 6px">Chargement…</div>
            </div>
            <div style="padding:14px;border-top:1px solid #f7f7f7;display:flex;justify-content:space-between;align-items:center;background:#fafafa;border-bottom-left-radius:12px;border-bottom-right-radius:12px;position:sticky;bottom:0;z-index:10;">
                <div style="display:flex;gap:8px;align-items:center">
                    <a href="/cart" class="shiny-button">Voir le panier</a>
                </div>
                <!-- footer total element (kept hidden so JS can update it safely) -->
                <div id="mini-cart-footer-total" style="display:none;font-weight:700;color:#0b66d1;">0 FCFA</div>
            </div>
        </div>
    </div>
</html>
