<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel ="stylesheet" href="{{ asset('css/StylePagePrincipale.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ENjdO4Dr2bkBIFxQpeoA6VZ6bQZ6Y9o2e2Z1ZlFZC+0h5Y5n3/tf6Yb6Y1Y3pXx+" crossorigin="anonymous">
    <style>
        /* Product grid styling to match the provided screenshot */
        .product-grid { display:flex; flex-wrap:wrap; gap:16px; justify-content:center; padding-left:18px; }
        .product-card { width:220px; box-shadow:0 2px 6px rgba(0,0,0,0.08); border-radius:6px; overflow:hidden; background:#fff; }
        .product-card .card-img-top { width:100%; height:180px; object-fit:cover; display:block; }
        .product-card .card-body { padding:8px 10px; height:140px; display:flex; flex-direction:column; }
        .product-title { font-size:0.9rem; margin:0; line-height:1.1; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .product-price { font-weight:600; color:#1e88e5; }
        .cart-btn { position:absolute; right:8px; bottom:8px; border-radius:50%; width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; background:#fff; border:1px solid #eee; }
        .product-meta { font-size:0.82rem; color:#666; }
        @media (max-width:767px) { .product-card { width:100% !important; } .product-grid { gap:12px; } }
        /* Note: left-side departments panel removed; header panel handled below */
        /* Ensure header departments inner menu is hidden when collapsed */
        .header-dpt.collapsed .dpt-menu { display: none !important; }
        /* Search/category badges */
        .search-info { display:flex; gap:12px; align-items:center; margin:12px 0 6px 6px; flex-wrap:wrap; }
        .search-info .badge { background:#e8f4ff; color:#0b66d1; padding:6px 10px; border-radius:16px; font-weight:600; box-shadow:0 1px 0 rgba(0,0,0,0.03); }
        .search-info .count { color:#333; font-weight:600; margin-left:6px; }
        /* Megamenu active highlight */
        a.menu-active, a.menu-active:hover { background:#0b66d1; color:#fff !important; padding:4px 8px; border-radius:4px; }
    </style>
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
                            <li><a href="/">Accueil</a></li>
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
                                        <button type="button" onclick="location.href='{{ $profileUrl }}'" style="display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border-radius:4px;border:1px solid #ddd;background:#fff;color:#2b7cff;cursor:pointer">
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
                                                         <li><a href="{{ url('/') }}?categorie={{ urlencode('Robes') }}">Robes</a></li>
                                                         <li><a href="{{ url('/') }}?categorie={{ urlencode('Hauts & T-shirts') }}">Hauts & T-shirts</a></li>
                                                         <li><a href="{{ url('/') }}?categorie={{ urlencode('Vestes et manteaux') }}">Vestes et manteaux</a></li>
                                                         <li><a href="{{ url('/') }}?categorie={{ urlencode('Pantalons & capris') }}">Pantalons & capris</a></li>
                                                         <li><a href="{{ url('/') }}?categorie={{ urlencode('Pulls') }}">Pulls</a></li>
                                                         <li><a href="{{ url('/') }}?categorie={{ urlencode('Costumes') }}">Costumes</a></li>
                                                         <li><a href="{{ url('/') }}?categorie={{ urlencode('Sweats à capuche & sweatshirts') }}">Sweats à capuche & sweatshirts</a></li>
                                                         <li><a href="{{ url('/') }}?categorie={{ urlencode('Pyjamas & peignoirs') }}">Pyjamas & peignoirs</a></li>
                                                         <li><a href="{{ url('/') }}?categorie={{ urlencode('Shorts') }}">Shorts</a></li>
                                                         <li><a href="{{ url('/') }}?categorie={{ urlencode('Maillots de bain') }}">Maillots de bain</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="flexcol">
                                                <div class="row">
                                                    <h4>Bijoux</h4>
                                                    <ul>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Accessoires') }}">Accessoires</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Sacs & pochettes') }}">Sacs & pochettes</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Colliers') }}">Colliers</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Bagues') }}">Bagues</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Boucles d\'oreilles') }}">Boucles d'oreilles</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Bracelets') }}">Bracelets</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Bijoux de corps') }}">Bijoux de corps</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="flexcol">
                                                <div class="row">
                                                    <h4>Beauté</h4>
                                                    <ul>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Accessoires de bain') }}">Accessoires de bain</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Soins de la peau') }}">Soins de la peau</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Kits spa & cadeaux') }}">Kits spa & cadeaux</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Maquillage & cosmétiques') }}">Maquillage & cosmétiques</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Huiles essentielles') }}">Huiles essentielles</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Savons & bombes de bain') }}">Savons & bombes de bain</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Soins capillaires') }}">Soins capillaires</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Masques pour le visage') }}">Masques pour le visage</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Parfums') }}">Parfums</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="flexcol">
                                                <div class="row">
                                                    <h4>Meilleures marques</h4>
                                                    <ul class="Women-brands">
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Nike') }}">Nike</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Louis Vuitton') }}">Louis Vuitton</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Chanel') }}">Chanel</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Dior') }}">Dior</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Gucci') }}">Gucci</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Prada') }}">Prada</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Hermès') }}">Hermès</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Rolex') }}">Rolex</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Cartier') }}">Cartier</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Givenchy') }}">Givenchy</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('Sara') }}">Sara</a></li>
                                                        <li><a href="{{ url('/') }}?categorie={{ urlencode('H&M') }}">H&M</a></li>
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
                                <div class="fly-item"><span class="item-number">0</span></div>
                            </a></li>
                            <li><a href="#" class="iscart">
                                <div class="icon-large"><i class="ri-shopping-cart-line"></i></div>
                                    <div class="fly-item"><span class="item-number">0</span></div>
                                
                            </a></li>
                            <li><a href="#">
                                <div class="icon-text">
                                    <div class="mini-text">Total</div>
                                    <div class="cart-total">0 FCFA</div>
                                </div>
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
                                <div class="mini-text mobile-hide">Total 5000 Produits</div>
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
                        <div class="d-flex justify-content-between align-items-center mb-3">
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
                        </div>

                        @if(isset($produits) && $produits->count())
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
                                            <h6 class="product-title">{{ $produit->Nom }}</h6>
                                            <p class="product-meta mb-2">{{ \Illuminate\Support\Str::limit($produit->Description, 60) }}</p>
                                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                                <div class="product-price">{{ number_format($produit->Prix, 0, ',', ' ') }} FCFA</div>
                                                <a href="/produit/{{ $produit->idProduit }}" class="btn btn-sm btn-outline-secondary">Voir</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination removed: affichage continu des produits -->
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
</body>
</html>