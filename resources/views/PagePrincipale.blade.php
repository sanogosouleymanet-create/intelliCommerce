<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>window.isClientAuthenticated = @json(auth()->guard('client')->check());</script>
    <link rel ="stylesheet" href="{{ asset('css/StylePagePrincipale.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ENjdO4Dr2bkBIFxQpeoA6VZ6bQZ6Y9o2e2Z1ZlFZC+0h5Y5n3/tf6Yb6Y1Y3pXx+" crossorigin="anonymous">
    <!-- Styles moved to public/css/StylePagePrincipale.css -->
    <title>Site Intelli-Commerce</title>
</head>
<body>
    <div id="page" class="site">
        <aside class="site-off desktop-hide">
            <div class="off-canvas">
                <div class="canvas-head flexitem">
                    <div class="logo"><a href="/"><span class="circle"></span><img src="Logo-site.png"  alt="logo"></a></div>
                    <a href="#" class="off-close"><i class="ri-close-line ri-xl"></i></a>
                </div>
                <div class="department"></div>
                <div class="nav"></div>
                <div class="thetop-nav"></div>
            </div>
        </aside>
        <header>
           <div class="header-top mobile-hide">
            <div class="conteiner">
                <div class="wrapper flexitem">
                    <div class="left">
                        <ul class="flexitem main-links">
                            
                            <li><a href="#">À propos</a></li>
                            <li><a href="#">Contact</a></li>
                        </ul>
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
                                <li><a href="{{('/welcome')}}">Accueil</a></li>
                                <li><a href="#">Boutique</a></li>
                                <li class="has-child">
                                    <a href="#">Femme 
                                    <div class="icon-small"><i class="ri-arrow-down-s-line"></i></div>
                                    </a>
                                  <div class="mega">
                                     <div class="container">
                                          <div class="wrapper">
                                             <div class="flexcol">
                                                 <div class="row">
                                                     <h4>Vêtements femme</h4>
                                                     <ul>
                                                         <li><a href="{{ url('/') }}?recherche={{ urlencode('Robes') }}">Robes</a></li>
                                                         <li><a href="{{ url('/') }}?recherche={{ urlencode('Hauts & T-shirts') }}">Hauts & T-shirts</a></li>
                                                         <li><a href="{{ url('/') }}?recherche={{ urlencode('Vestes et manteaux') }}">Vestes et manteaux</a></li>
                                                         <li><a href="{{ url('/') }}?recherche={{ urlencode('Pantalons & capris') }}">Pantalons & capris</a></li>
                                                         <li><a href="{{ url('/') }}?recherche={{ urlencode('Pulls') }}">Pulls</a></li>
                                                         <li><a href="{{ url('/') }}?recherche={{ urlencode('Costumes') }}">Costumes</a></li>
                                                         <li><a href="{{ url('/') }}?recherche={{ urlencode('Sweats à capuche & sweatshirts') }}">Sweats à capuche & sweatshirts</a></li>
                                                         <li><a href="{{ url('/') }}?recherche={{ urlencode('Pyjamas & peignoirs') }}">Pyjamas & peignoirs</a></li>
                                                         <li><a href="{{ url('/') }}?recherche={{ urlencode('Shorts') }}">Shorts</a></li>
                                                         <li><a href="{{ url('/') }}?recherche={{ urlencode('Maillots de bain') }}">Maillots de bain</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="flexcol">
                                                <div class="row">
                                                    <h4>Bijoux</h4>
                                                    <ul>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Accessoires') }}">Accessoires</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Sacs & pochettes') }}">Sacs & pochettes</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Colliers') }}">Colliers</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Bagues') }}">Bagues</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Boucles d\'oreilles') }}">Boucles d'oreilles</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Bracelets') }}">Bracelets</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Bijoux de corps') }}">Bijoux de corps</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="flexcol">
                                                <div class="row">
                                                    <h4>Beauté</h4>
                                                    <ul>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Accessoires de bain') }}">Accessoires de bain</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Soins de la peau') }}">Soins de la peau</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Kits spa & cadeaux') }}">Kits spa & cadeaux</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Maquillage & cosmétiques') }}">Maquillage & cosmétiques</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Huiles essentielles') }}">Huiles essentielles</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Savons & bombes de bain') }}">Savons & bombes de bain</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Soins capillaires') }}">Soins capillaires</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Masques pour le visage') }}">Masques pour le visage</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Parfums') }}">Parfums</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="flexcol">
                                                <div class="row">
                                                    <h4>Meilleures marques</h4>
                                                    <ul class="Women-brands">
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Nike') }}">Nike</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Louis Vuitton') }}">Louis Vuitton</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Chanel') }}">Chanel</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Dior') }}">Dior</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Gucci') }}">Gucci</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Prada') }}">Prada</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Hermès') }}">Hermès</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Rolex') }}">Rolex</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Cartier') }}">Cartier</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Givenchy') }}">Givenchy</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('Sara') }}">Sara</a></li>
                                                        <li><a href="{{ url('/') }}?recherche={{ urlencode('H&M') }}">H&M</a></li>
                                                    </ul>
                                                    <a href="#" class="view-all">Voir toutes les marques <i class="ri-arrow-right-line"></i></a>
                                                </div>
                                            </div>
                                            <div class="flexcol products">
                                                <div class="row">
                                                    <div class="media">
                                                        
                                                        <div class="thumbnail object-cover">
                                                            <a href="#"><img src="Image1.jpg" alt=""></a>
                                                        </div>
                                                </div>
                                                 <div class="text-content">
                                                    <h4>Les plus recherchés</h4>
                                                    <a href="" class="primary-button">Commander maintenant</a>
                                                 </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                 </div>
                            </li>
                                <li><a href="#">Homme
                                    <div class="icon-small"><i class="ri-arrow-down-s-line"></i></div>
                                </a></li>
                                <li><a href="#">Enfant
                                    <div class="icon-small"><i class="ri-arrow-down-s-line"></i></div>
                                </a></li>
                                <li><a href="#">Sports
                                    <div class="fly-item"><span>Nouveau!</span></div>
                                </a></li>
                            </ul>
                        </nav>
                    </div>
                    <div class="right">
                        <ul class="flexitem second-links">
                            <li class="mobile-hide"><a href="#">
                                <div class="icon-large"><i class="ri-heart-line"></i></div>
                                <div class="fly-item"><span class="item-number">{{ $cartCount }}</span></div>
                            </a></li>
                            <li><a href="#" class="iscart">
                                <div class="icon-large"><i class="ri-shopping-cart-line"></i></div>
                                    <div class="fly-item"><span class="item-number">{{ $cartCount }}</span></div>
                                
                            </a></li>  
                        </ul>
                    </div>
                </div>
            </container>
           </div>

           <div class="header-main mobile-hide">
             <div class="conteiner">
             <div class="wrapper flexitem">
                    <div class="left">
                        <div id="headerDepartments" class="dpt-cat header-dpt collapsed">
                            <div class="dpt-head">
                                <div class="main-text">Tous les Departements</div>
                                <div class="mini-text mobile-hide">Total {{ isset($produits) ? $produits->count() : \App\Models\Produit::count() }} Produits</div>
                                <a href="#" class="dpt-trigger mobile-hide" aria-expanded="false">
                                    <i class="ri-menu-3-line ri-xl"></i>
                                </a>
                            </div>
                            <div class="dpt-menu">
                                <ul class="second-links">
                                    <li class="has-child beauty">
                                        <a href="#">
                                            <div class="icon-large"><i class="ri-bear-smile-line"></i></div>
                                            Beauté
                                            <div class="icon-small"><i class="ri-arrow-right-s-line"></i></div>
                                        </a>
                                        <ul>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Maquillage') }}">Maquillage</a></li>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Soins de la peau') }}">Soins de la peau</a></li>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Soins capillaires') }}">Soins capillaires</a></li>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Parfums') }}">Parfums</a></li>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Soins des pieds & mains') }}">Soins des pieds & mains</a></li>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Outils & accessoires') }}">Outils & accessoires</a></li>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Rasage & épilation') }}">Rasage & épilation</a></li>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Soins personnels') }}">Soins personnels</a></li>
                                        </ul>
                                    </li>
                                    <li class="has-child electronic">
                                        <a href="#">
                                            <div class="icon-large"><i class="ri-bluetooth-connect-line"></i></div>
                                            Électronique
                                            <div class="icon-small"><i class="ri-arrow-right-s-line"></i></div>
                                        </a>
                                        <ul>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Camera') }}">Camera</a></li>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Téléphone') }}">Téléphone</a></li>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Airpods') }}">Airpods</a></li>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Ordinateur') }}">Ordinateur</a></li>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Télévision') }}">Télévision</a></li>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Vidéo Projecteurs') }}">Vidéo Projecteurs</a></li>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Casque') }}">Casque</a></li>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Enceinte bluetooth') }}">Enceinte bluetooth</a></li>
                                        </ul>
                                    </li>
                                    <li class="has-child fashion">
                                        <a href="#">
                                            <div class="icon-large"><i class="ri-t-shirt-air-line"></i></div>
                                            Mode Femme
                                            <div class="icon-small"><i class="ri-arrow-right-s-line"></i></div>
                                        </a>
                                        <ul>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Vêtements') }}">Vêtements</a></li>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Chaussures') }}">Chaussures</a></li>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Bijoux') }}">Bijoux</a></li>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Montres') }}">Montres</a></li>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Accessoires') }}">Accessoires</a></li>
                                            <li><a href="{{ url('/') }}?categorie={{ urlencode('Sacs à main') }}">Sacs à main</a></li>
                                        </ul>
                                    </li>
                                    <li class="has-child fashion">
                                        <a href="#">
                                            <div class="icon-large"><i class="ri-shirt-line"></i></div>
                                            Mode Homme
                                            
                                        </a>
                                    </li>
                                    <li class="has-child fashion">
                                        <a href="#">
                                            <div class="icon-large"><i class="ri-user-5-line"></i></div>
                                            Mode Fille

                                        </a>
                                    </li>
                                    <li class="has-child fashion">
                                        <a href="#">
                                            <div class="icon-large"><i class="ri-user-6-line"></i></div>
                                            Mode Garçon
                                        </a>
                                    </li>
                                    <li class="has-child homekit">
                                        <a href="#">
                                            <div class="icon-large"><i class="ri-heart-pulse-line"></i></div>
                                            Cuisine & Maison
                                             <div class="icon-small"><i class="ri-arrow-right-s-line"></i></div>
                                        </a>
                                        <div class="mega">
                                            <div class="container">
                                                <div class="wrapper">
                                                    <div class="flexcol">
                                                        <div class="row">
                                                            <h4><a href="{{ url('/') }}?categorie={{ urlencode('Cuisine & Salle à manger') }}">Cuisine & Salle à manger</a></h4>
                                                            <ul>
                                                                <li><a href="{{ url('/') }}?categorie={{ urlencode('Cuisine') }}">Cuisine</a></li>
                                                                <li><a href="{{ url('/') }}?categorie={{ urlencode('Salle à manger') }}">Salle à manger</a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="flexcol">
                                                        <div class="row">
                                                            <h4><a href="{{ url('/') }}?categorie={{ urlencode('Salon') }}">Salon</a></h4>
                                                            <ul>
                                                                <li><a href="{{ url('/') }}?categorie={{ urlencode('Meubles de salon') }}">Meubles de salon</a></li>
                                                                <li><a href="{{ url('/') }}?categorie={{ urlencode('Véranda') }}">Véranda</a></li>
                                                                <li><a href="{{ url('/') }}?categorie={{ urlencode('Salle familiale') }}">Salle familiale</a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="flexcol">
                                                        <div class="row">
                                                            <h4><a href="{{ url('/') }}?categorie={{ urlencode('Lit & Bain') }}">Lit & Bain</a></h4>
                                                            <ul>
                                                                <li><a href="{{ url('/') }}?categorie={{ urlencode('Toilettes') }}">Toilettes</a></li>
                                                                <li><a href="{{ url('/') }}?categorie={{ urlencode('Rangement & Placard') }}">Rangement & Placard</a></li>
                                                                <li><a href="{{ url('/') }}?categorie={{ urlencode('Chambre à coucher') }}">Chambre à coucher</a></li>
                                                                <li><a href="{{ url('/') }}?categorie={{ urlencode('Bébé et Enfant') }}">Bébé et Enfant</a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="flexcol">
                                                        <div class="row">
                                                            <h4><a href="{{ url('/') }}?categorie={{ urlencode('Utilitaire') }}">Utilitaire</a></h4>
                                                            <ul>
                                                                <li><a href="{{ url('/') }}?categorie={{ urlencode('Lessive') }}">Lessive</a></li>
                                                                <li><a href="{{ url('/') }}?categorie={{ urlencode('Garage') }}">Garage</a></li>
                                                                <li><a href="{{ url('/') }}?categorie={{ urlencode('Vestiaire') }}">Vestiaire</a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="has-child fashion">
                                        <a href="{{ url('/') }}?categorie={{ urlencode('Sports') }}">
                                            <div class="icon-large"><i class="ri-basketball-line"></i></div>
                                            Sports
                                        </a>
                                    </li>
                                    <li class="has-child fashion">
                                        <a href="{{ url('/') }}?categorie={{ urlencode('Meilleures ventes') }}">
                                            <div class="icon-large"><i class="ri-shield-star-line"></i></div>
                                            Meilleures ventes
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="right">
                        <div class="search-box">
                            <form action="/" method="GET" class="search">
                                <span class="icon-large"><i class="ri-search-line"></i></span>
                                <input type="search" name="recherche" value="{{ request('recherche') }}" placeholder="Rechercher produits..." />
                                <button type="submit">Rechercher</button>
                            </form>
                        </div>
                    </div>
                </div>
           </div> 
           </div>
        </header>

        <main>
            <div class="container py-4">
                <div class="row">
                    <section class="col-12">
                        <!--<div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="m-0">Nos produits</h2>
                            </div>
                        </div>
                        <div class="search-info">
                            @if(request('categorie'))
                                <span class="badge">Catégorie: {{ request('categorie') }}</span>
                            @endif
                            @if(request('recherche'))
                                <span class="badge">Recherche: « {{ request('recherche') }} »</span>
                            @endif
                            <span class="count">{{ isset($produits) ? $produits->count() : 0 }} résultat(s)</span>
                        </div>-->

                        @if(isset($produits) && $produits->count())
                            {{-- Section avant la liste des produits : bannière + listes "Les plus vendus" / "Les plus recherchés" --}}
                            @php
                                // Top 5 produits les plus vendus via la table pivot Produitcommande
                                $topVendusIds = [];
                                if(\Illuminate\Support\Facades\Schema::hasTable('Produitcommande')){
                                    $topVendusIds = \Illuminate\Support\Facades\DB::table('Produitcommande')
                                        ->select('Produit_idProduit', \Illuminate\Support\Facades\DB::raw('SUM(Quantite) as total'))
                                        ->groupBy('Produit_idProduit')
                                        ->orderByDesc('total')
                                        ->limit(5)
                                        ->pluck('Produit_idProduit')
                                        ->toArray();
                                }
                                $topVendus = collect([]);
                                if(!empty($topVendusIds)){
                                    $prodMap = \App\Models\Produit::whereIn('idProduit', $topVendusIds)->get()->keyBy('idProduit');
                                    $topVendus = collect($topVendusIds)->map(function($id) use($prodMap){ return $prodMap->get($id); })->filter();
                                }

                                // Top recherchés : si une table de logs de recherche existe, l'utiliser
                                $topRecherches = collect([]);
                                $searchFallback = false;
                                if(\Illuminate\Support\Facades\Schema::hasTable('recherches')){
                                    $topSearchIds = \Illuminate\Support\Facades\DB::table('recherches')
                                        ->select('produit_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as cnt'))
                                        ->groupBy('produit_id')
                                        ->orderByDesc('cnt')
                                        ->limit(5)
                                        ->pluck('produit_id')
                                        ->toArray();
                                    if(!empty($topSearchIds)){
                                        $map = \App\Models\Produit::whereIn('idProduit', $topSearchIds)->get()->keyBy('idProduit');
                                        $topRecherches = collect($topSearchIds)->map(fn($id) => $map->get($id))->filter();
                                    }
                                }
                                if($topRecherches->isEmpty()){
                                    $topRecherches = $topVendus; // fallback
                                    $searchFallback = true;
                                }
                            @endphp

                            <div id="mainContent">
                            <div class="pre-products" style="display:flex;gap:18px;align-items:flex-start;margin:12px 6px;">
                                <div class="hero" style="flex:1;background:#0b66d1;color:#fff;border-radius:8px;padding:20px;min-height:140px;display:flex;flex-direction:column;justify-content:center;">
                                    <h2 style="margin:0 0 8px 0">Offres du jour</h2>
                                    <p style="margin:0 0 12px 0;opacity:0.95">Profitez des meilleures offres et réductions sur une sélection de produits populaires.</p>
                                    <a href="{{ url('/') }}?categorie={{ urlencode('Meilleures ventes') }}" class="btn btn-light" style="width:170px">Voir les offres</a>
                                </div>

                                <div style="width:340px;display:flex;flex-direction:column;gap:12px;">
                                    <div class="top-list" style="background:#fff;padding:12px;border-radius:8px;">
                                        <h5 style="margin:0 0 8px 0">Les plus vendus</h5>
                                        @if($topVendus->isEmpty())
                                            <div class="text-muted">Aucun produit vendu récemment.</div>
                                        @else
                                            @foreach($topVendus as $p)
                                                <div class="item" style="display:flex;gap:10px;align-items:center;margin-bottom:10px;">
                                                    @php
                                                        $img = trim((string)($p->Image ?? ''));
                                                        $imgUrl = 'https://via.placeholder.com/80x60?text=No';
                                                        if($img !== ''){
                                                            if(preg_match('/^https?:\/\//i', $img)){
                                                                $imgUrl = $img;
                                                            } elseif(\Illuminate\Support\Facades\Storage::exists('public/'.$img)){
                                                                $imgUrl = asset('storage/'.$img);
                                                            } elseif(file_exists(public_path($img))){
                                                                $imgUrl = asset($img);
                                                            } elseif(file_exists(public_path('images/'.basename($img)))){
                                                                $imgUrl = asset('images/'.basename($img));
                                                            }
                                                        }
                                                    @endphp
                                                    @php
                                                        $dataName = e($p->Nom);
                                                        $dataDesc = e($p->Description ?? '');
                                                        $dataPrice = number_format($p->Prix,0,',',' ') . ' FCFA';
                                                        $dataImg = $imgUrl;
                                                    @endphp
                                                    <a href="#" class="product-open" data-id="{{ $p->idProduit }}" data-name="{{ $dataName }}" data-desc="{{ $dataDesc }}" data-price="{{ $dataPrice }}" data-img="{{ $dataImg }}"><img src="{{ $imgUrl }}" alt="{{ $p->Nom }}" style="width:64px;height:48px;object-fit:cover;border-radius:4px;"></a>
                                                    <div style="flex:1;font-size:0.92rem">
                                                        <a href="#" class="product-open" data-id="{{ $p->idProduit }}" data-name="{{ $dataName }}" data-desc="{{ $dataDesc }}" data-price="{{ $dataPrice }}" data-img="{{ $dataImg }}" style="color:#222;font-weight:700">{{ $p->Nom }}</a>
                                                        <div style="color:#1e88e5;font-weight:700">{{ number_format($p->Prix,0,',',' ') }} FCFA</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <div class="top-list" style="background:#fff;padding:12px;border-radius:8px;">
                                        <h5 style="margin:0 0 8px 0">Les plus recherchés</h5>
                                        @if($topRecherches->isEmpty())
                                            <div class="text-muted">Aucune donnée de recherche disponible.</div>
                                        @else
                                            @foreach($topRecherches as $p)
                                                <div class="item" style="display:flex;gap:10px;align-items:center;margin-bottom:10px;">
                                                    @php
                                                        $img = trim((string)($p->Image ?? ''));
                                                        $imgUrl = 'https://via.placeholder.com/80x60?text=No';
                                                        if($img !== ''){
                                                            if(preg_match('/^https?:\/\//i', $img)){
                                                                $imgUrl = $img;
                                                            } elseif(\Illuminate\Support\Facades\Storage::exists('public/'.$img)){
                                                                $imgUrl = asset('storage/'.$img);
                                                            } elseif(file_exists(public_path($img))){
                                                                $imgUrl = asset($img);
                                                            } elseif(file_exists(public_path('images/'.basename($img)))){
                                                                $imgUrl = asset('images/'.basename($img));
                                                            }
                                                        }
                                                    @endphp
                                                    @php
                                                        $dataName = e($p->Nom);
                                                        $dataDesc = e($p->Description ?? '');
                                                        $dataPrice = number_format($p->Prix,0,',',' ') . ' FCFA';
                                                        $dataImg = $imgUrl;
                                                    @endphp
                                                    <a href="#" class="product-open" data-id="{{ $p->idProduit }}" data-name="{{ $dataName }}" data-desc="{{ $dataDesc }}" data-price="{{ $dataPrice }}" data-img="{{ $dataImg }}"><img src="{{ $imgUrl }}" alt="{{ $p->Nom }}" style="width:64px;height:48px;object-fit:cover;border-radius:4px;"></a>
                                                    <div style="flex:1;font-size:0.92rem">
                                                        <a href="#" class="product-open" data-id="{{ $p->idProduit }}" data-name="{{ $dataName }}" data-desc="{{ $dataDesc }}" data-price="{{ $dataPrice }}" data-img="{{ $dataImg }}" style="color:#222;font-weight:700">{{ $p->Nom }}</a>
                                                        <div style="color:#1e88e5;font-weight:700">{{ number_format($p->Prix,0,',',' ') }} FCFA</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                        @if(!empty($searchFallback) && $searchFallback)
                                            <div class="text-muted" style="font-size:0.82rem">(Données de recherche indisponibles — affichage des plus vendus)</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                                <div class="product-grid">
                                @foreach($produits as $produit)
                                    <div class="product-card card">
                                        <div class="position-relative">
                                            @php
                                                $imgUrl = 'https://via.placeholder.com/400x300?text=No+Image';
                                                $img = trim((string)($produit->Image ?? ''));
                                                if($img !== ''){
                                                    // absolute URL
                                                    if(preg_match('/^https?:\/\//i', $img)){
                                                        $imgUrl = $img;
                                                    }
                                                    // storage/app/public/...
                                                    elseif(\Illuminate\Support\Facades\Storage::exists('public/'.$img)){
                                                        $imgUrl = asset('storage/'.$img);
                                                    }
                                                    // public/ path
                                                    elseif(file_exists(public_path($img))){
                                                        $imgUrl = asset($img);
                                                    }
                                                    // public/images/ fallback
                                                    elseif(file_exists(public_path('images/'.basename($img)))){
                                                        $imgUrl = asset('images/'.basename($img));
                                                    }
                                                }
                                            @endphp
                                            <img src="{{ $imgUrl }}" class="card-img-top" alt="{{ $produit->Nom }}">
                                            <button class="add-to-cart" title="Ajouter au panier" data-id="{{ $produit->idProduit }}" aria-label="Ajouter {{ $produit->Nom }} au panier">
                                                <i class="fa fa-cart-plus"></i>
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $dataName = e($produit->Nom);
                                                $dataDesc = e($produit->Description ?? '');
                                                $dataPrice = number_format($produit->Prix, 0, ',', ' ') . ' FCFA';
                                                $dataImg = $imgUrl;
                                                $vendeur = $produit->vendeur ?? null;
                                                $vendorName = e($vendeur->NomBoutique ?? ($vendeur->Nom . ' ' . ($vendeur->Prenom ?? '')));
                                                $vendorAddress = e($vendeur->Adresse ?? '');
                                                // produits similaires (même catégorie)
                                                $similar = \App\Models\Produit::where('Categorie', $produit->Categorie)
                                                    ->where('idProduit', '!=', $produit->idProduit)
                                                    ->limit(4)
                                                    ->get(['idProduit','Nom','Prix','Image'])
                                                    ->map(function($s){
                                                        $img = trim((string)($s->Image ?? ''));
                                                        $imgUrl = 'https://via.placeholder.com/120x90?text=No';
                                                        if($img !== ''){
                                                            if(preg_match('/^https?:\/\//i', $img)){
                                                                $imgUrl = $img;
                                                            } elseif(\Illuminate\Support\Facades\Storage::exists('public/'.$img)){
                                                                $imgUrl = asset('storage/'.$img);
                                                            } elseif(file_exists(public_path($img))){
                                                                $imgUrl = asset($img);
                                                            } elseif(file_exists(public_path('images/'.basename($img)))){
                                                                $imgUrl = asset('images/'.basename($img));
                                                            }
                                                        }
                                                        return ['id' => $s->idProduit, 'name' => $s->Nom, 'price' => number_format($s->Prix,0,',',' ') . ' FCFA', 'img' => $imgUrl];
                                                    })->toArray();
                                                $dataSimilar = e(json_encode($similar));
                                            @endphp
                                            <h6 class="product-title"><a href="#" class="product-open" data-id="{{ $produit->idProduit }}" data-name="{{ $dataName }}" data-desc="{{ $dataDesc }}" data-price="{{ $dataPrice }}" data-img="{{ $dataImg }}" data-vendor-name="{{ $vendorName }}" data-vendor-address="{{ $vendorAddress }}" data-stock="{{ $produit->Stock ?? 0 }}" data-category="{{ $produit->Categorie ?? '' }}" data-similar='@json($similar)'>{{ $produit->Nom }}</a></h6>
                                            <p class="product-meta mb-2">{{ \Illuminate\Support\Str::limit($produit->Description, 60) }}</p>
                                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                                <div class="product-price">{{ number_format($produit->Prix, 0, ',', ' ') }} FCFA</div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary product-open" data-id="{{ $produit->idProduit }}" data-name="{{ $dataName }}" data-desc="{{ $dataDesc }}" data-price="{{ $dataPrice }}" data-img="{{ $dataImg }}" data-vendor-name="{{ $vendorName }}" data-vendor-address="{{ $vendorAddress }}" data-stock="{{ $produit->Stock ?? 0 }}" data-category="{{ $produit->Categorie ?? '' }}" data-similar='@json($similar)'>Voir</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            </div> 
                        @else
                            <div class="alert alert-info">Aucun produit trouvé.</div>
                        @endif
                    </section>
                </div>
            </div>
        </main>
        <footer>
        
        </footer>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
    <script>
        // Toggle header departments panel using the original header trigger (.dpt-trigger)
        (function(){
            const headerTrigger = document.querySelector('#headerDepartments .dpt-head .dpt-trigger');
            const headerPanel = document.getElementById('headerDepartments');
            if(headerTrigger && headerPanel){
                headerTrigger.addEventListener('click', function(e){
                    e.preventDefault();
                    const isCollapsed = headerPanel.classList.toggle('collapsed');
                    headerTrigger.setAttribute('aria-expanded', (!isCollapsed).toString());
                });
                // reflect initial state
                headerTrigger.setAttribute('aria-expanded', (!headerPanel.classList.contains('collapsed')).toString());
            }
        })();
    </script>
    <script>
        // Remplacer #mainContent par une vue détail produit (sans navigation)
        (function(){
            // history stack: each entry { html, scroll }
            let savedStack = [];
            function renderDetail(data){
                // parse similar products if provided as JSON string
                let similar = [];
                try{ if(data.similar) similar = JSON.parse(data.similar); }catch(e){ similar = []; }
                const similarHtml = similar.length ? `<div style="margin-top:12px">
                        <h5 style="margin:8px 0">Produits similaires</h5>
                        <div style="display:flex;gap:10px;flex-wrap:wrap">${similar.map(s => `
                            <div style="width:140px;background:#fff;border-radius:6px;padding:6px;box-shadow:0 1px 2px rgba(0,0,0,0.04);">
                                <a href="#" class="product-open" data-id="${s.id}" data-name="${s.name.replace(/\"/g,'') }" data-desc="" data-price="${s.price}" data-img="${s.img}">
                                    <img src="${s.img}" alt="${s.name}" style="width:100%;height:78px;object-fit:cover;border-radius:4px;">
                                    <div style="font-size:0.85rem;color:#222;font-weight:700;margin-top:6px">${s.name}</div>
                                    <div style="color:#1e88e5;font-weight:700">${s.price}</div>
                                </a>
                                <div style="margin-top:6px;text-align:center">
                                    <button class="btn btn-sm btn-outline-primary add-to-cart-similar" data-id="${s.id}" style="padding:6px 8px;border-radius:6px">Ajouter</button>
                                </div>
                            </div>`).join('')}</div></div>` : '';

                // details block: prix, stock, catégorie, boutique
                const detailsHtml = `<div style="margin-top:8px;padding:12px;border-radius:6px;background:#86d0df;color:#000">
                        <div style="font-weight:700">Prix: <span style="font-weight:400">${data.price||''}</span></div>
                        <div style="font-weight:700;margin-top:6px">Stock: <span style="font-weight:400">${data.stock||''}</span></div>
                        <div style="font-weight:700;margin-top:6px">Catégorie: <span style="font-weight:400">${data.category||''}</span></div>
                        <div style="font-weight:700;margin-top:6px">Boutique: <span style="font-weight:400">${data.vendorName||''}</span></div>
                    </div>`;

                const html = `
                    <div class="product-detail" style="display:flex;gap:18px;align-items:flex-start;padding:12px;background:#fff;border-radius:8px;">
                        <div style="flex:1;max-width:520px;min-width:0">
                            <img src="${data.img || ''}" alt="${data.name||''}" style="width:100%;height:auto;max-height:520px;object-fit:contain;border-radius:8px;display:block;" />
                        </div>
                        <div style="width:360px;display:flex;flex-direction:column;gap:12px;">
                            <h2 style="margin:0">${data.name||''}</h2>
                            <div style="color:#1e88e5;font-weight:700;font-size:1.1rem">${data.price||''}</div>
                            ${detailsHtml}
                            <p style="color:#444;flex:1;white-space:pre-wrap">${data.desc||''}</p>
                            <div style="display:flex;gap:8px;align-items:center">
                                <button class="btn btn-sm btn-outline-secondary js-back" style="padding:10px 14px;border-radius:8px">← Retour à la liste</button>
                                <button class="btn btn-primary" style="padding:10px 14px;border-radius:8px"><i class="fa fa-cart-plus" aria-hidden="true"></i>&nbsp;Ajouter au panier</button>
                            </div>
                        </div>
                    </div>
                    ${similarHtml ? `<div class="similar-full" style="margin-top:18px;padding:12px;background:transparent;border-radius:6px">${similarHtml}</div>` : ''}
                `;
                const container = document.getElementById('mainContent');
                if(!container) return;
                // push current view onto stack so we can return to it
                savedStack.push({ html: container.innerHTML, scroll: window.scrollY || window.pageYOffset || 0 });
                container.innerHTML = html;
                // push history state so refresh/back behavior is preserved
                try{ history.pushState({ produitId: data.id || null }, '', data.id ? ('?produit=' + encodeURIComponent(data.id)) : window.location.pathname); }catch(e){}
            }
            function restoreMain(){
                const container = document.getElementById('mainContent');
                if(!container) return;
                if(savedStack.length){
                    const entry = savedStack.pop();
                    container.innerHTML = entry.html;
                    if(typeof entry.scroll === 'number'){
                        window.scrollTo({ top: entry.scroll, left: 0, behavior: 'auto' });
                    }
                }
            }
            document.addEventListener('click', function(e){
                const btn = e.target.closest('.product-open');
                if(btn){
                    e.preventDefault();
                    const data = {
                        id: btn.dataset.id,
                        name: btn.dataset.name || '',
                        desc: btn.dataset.desc || '',
                        price: btn.dataset.price || '',
                        img: btn.dataset.img || '',
                        vendorName: btn.dataset.vendorName || '',
                        vendorAddress: btn.dataset.vendorAddress || '',
                        stock: btn.dataset.stock || '',
                        category: btn.dataset.category || '',
                        similar: btn.dataset.similar || ''
                    };

                    // If important details are missing (vendor, stock or similar), fetch server fragment
                    const needsAjax = !(data.vendorName || data.vendorAddress) || data.stock === '' || !data.similar;
                    if(needsAjax && data.id){
                        const url = '/produit/' + encodeURIComponent(data.id);
                        const container = document.getElementById('mainContent');
                        if(!container){ renderDetail(data); return; }
                        // push current view so closing the fragment returns here
                        savedStack.push({ html: container.innerHTML, scroll: window.scrollY || window.pageYOffset || 0 });
                        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(resp => resp.text())
                            .then(html => {
                                container.innerHTML = html;
                                try{ history.pushState({ produitId: data.id || null }, '', '?produit=' + encodeURIComponent(data.id)); }catch(e){}
                            })
                            .catch(err => { console.error('Fetch produit fragment failed', err); renderDetail(data); });
                        return;
                    }

                    renderDetail(data);
                    return;
                }
                if(e.target.closest('.js-back')){
                    e.preventDefault();
                    // navigate back in history; popstate handler will restore the view
                    if(history.state && history.state.produitId) history.back(); else restoreMain();
                    return;
                }

                // Add to cart on similar product or fragment
                const addBtn = e.target.closest('.add-to-cart-similar, .add-to-cart-fragment');
                if(addBtn){
                    e.preventDefault();
                    const id = addBtn.dataset.id;
                    // dispatch event to add product to cart; do not change button state here
                    document.dispatchEvent(new CustomEvent('product-added-to-cart', { detail: { id } }));
                    return;
                }
            });
        })();
    </script>
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
            <div style="padding:14px;border-top:1px solid #f7f7f7;display:flex;justify-content:space-between;align-items:center;background:#fafafa;border-bottom-left-radius:12px;border-bottom-right-radius:12px">
                <div style="display:flex;gap:8px;align-items:center">
                    <a href="/cart" class="shiny-button">Voir le panier</a>
                </div>
                <!-- footer total element (kept hidden so JS can update it safely) -->
                <div id="mini-cart-footer-total" style="display:none;font-weight:700;color:#0b66d1;">0 FCFA</div>
            </div>
        </div>
    </div>
</html>