<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>window.isClientAuthenticated = @json(auth()->guard('client')->check());</script>
    <script>window.isAuthenticated = @json(auth()->guard('client')->check() || auth()->guard('vendeur')->check() || auth()->guard('administrateur')->check());</script>
    <link rel ="stylesheet" href="{{ asset('css/StylePagePrincipale.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ENjdO4Dr2bkBIFxQpeoA6VZ6bQZ6Y9o2e2Z1ZlFZC+0h5Y5n3/tf6Yb6Y1Y3pXx+" crossorigin="anonymous">
    <title>Produits en Promotion - Intelli-Commerce</title>
    <style>
        /* Page Promotions - style dédié */
        .page-promotions main { padding-bottom: 3rem; }
        .page-promotions .promo-page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1a1a2e;
            letter-spacing: -0.02em;
            margin: 0;
        }
        .page-promotions .promo-back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            color: #495057;
            border: 1px solid #dee2e6;
            background: #fff;
            transition: background .2s, color .2s, border-color .2s;
        }
        .page-promotions .promo-back-link:hover {
            background: #f8f9fa;
            color: #1a1a2e;
            border-color: #ffc107;
        }
        .page-promotions .promo-filters-bar {
            background: linear-gradient(135deg, #fffef8 0%, #fff9e6 100%);
            border: 1px solid rgba(255, 193, 7, 0.4);
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 16px rgba(255, 193, 7, 0.12);
        }
        .page-promotions .promo-filters-bar .form-control,
        .page-promotions .promo-filters-bar .form-select {
            border-radius: 8px;
            border: 1px solid #e9ecef;
            padding: 10px 14px;
            font-size: 0.95rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .page-promotions .promo-filters-bar .form-control:focus,
        .page-promotions .promo-filters-bar .form-select:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.2);
            outline: 0;
        }
        .page-promotions .promo-filters-bar .btn-primary {
            background: linear-gradient(135deg, #e65100 0%, #ff9800 100%);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(230, 81, 0, 0.3);
            transition: transform .15s, box-shadow .15s;
        }
        .page-promotions .promo-filters-bar .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(230, 81, 0, 0.35);
        }
        .page-promotions .promo-filters-bar .btn-outline-secondary {
            border-radius: 8px;
            padding: 10px 18px;
            font-weight: 600;
        }
        .page-promotions .promo-count {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: linear-gradient(135deg, #ffc107 0%, #ffeb3b 100%);
            color: #1a1a2e;
            font-weight: 700;
            font-size: 0.95rem;
            border-radius: 20px;
            margin-bottom: 1.25rem;
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.25);
        }
        .page-promotions .product-grid {
            padding-left: 0;
        }
        .page-promotions .product-card {
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .page-promotions .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }
        .page-promotions .product-card-promo:hover {
            box-shadow: 0 8px 24px rgba(255, 193, 7, 0.35);
        }
        @media (max-width: 768px) {
            .page-promotions .promo-filters-bar form { flex-direction: column; align-items: stretch; }
            .page-promotions .promo-filters-bar .form-control,
            .page-promotions .promo-filters-bar .form-select { max-width: none; min-width: 0; }
        }
    </style>
</head>
<body class="page-promotions">
    <div id="page" class="site">
        <aside class="site-off desktop-hide">
            <div class="off-canvas">
                <div class="canvas-head flexitem">
                    <div class="logo"><a href="/"><span class="circle"></span><img src="Logo-site.png"  alt="logo"></a></div>
                    <a href="#" class="off-close"><i class="ri-close-line ri-xl"></i></a>
                </div>
                
            </div>
        </aside>
        <header>
           <div class="header-top mobile-hide">
            <div class="conteiner">
                <div class="wrapper flexitem">
                    <div class="left">
                        <ul class="flexitem main-links">
                            <li><a href="/">Accueil</a></li>
                            <li><a href="/a-propos">À propos</a></li>
                            <li><a href="/contact">Contact</a></li>
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
                                            $cartCount = count($cart);
                                            $prodIds = array_keys($cart);
                                            $prods = \App\Models\Produit::whereIn('idProduit', $prodIds)->get()->keyBy('idProduit');
                                            foreach($cart as $pid => $q){
                                                $p = $prods->get($pid);
                                                if($p) $cartTotal += ($p->Prix ?? 0) * $q;
                                            }
                                        }

                                        // compute unread messages count
                                        $messageUnreadCount = 0;
                                        $messagesUrl = '#';
                                        if(auth()->guard('client')->check()){
                                            $messageUnreadCount = \App\Models\Message::where('Client_idClient', auth()->guard('client')->id())
                                                ->whereIn('Statut', ['non lu','envoye'])
                                                ->whereIn('sender_type', ['vendeur', 'administrateur'])
                                                ->count();
                                            $messagesUrl = '/messages';
                                        } elseif(auth()->guard('vendeur')->check()){
                                            $messageUnreadCount = \App\Models\Message::where('Vendeur_idVendeur', auth()->guard('vendeur')->id())
                                                ->whereIn('Statut', ['non lu','envoye'])
                                                ->whereIn('sender_type', ['client', 'administrateur'])
                                                ->count();
                                            $messagesUrl = '/vendeur/messages';
                                        } elseif(auth()->guard('administrateur')->check()){
                                            $messageUnreadCount = \App\Models\Message::where('Administrateur_idAdministrateur', auth()->guard('administrateur')->id())
                                                ->whereIn('Statut', ['non lu','envoye'])
                                                ->whereIn('sender_type', ['client', 'vendeur'])
                                                ->count();
                                            $messagesUrl = route('admin.dashboard') . '#messages';
                                        }
                                    @endphp
                                @if($admin || $vendeur || $client)
                                    @php
                                        $user = $admin ?? $vendeur ?? $client;
                                        $displayName = trim($user->Nom . ' ' . ($user->Prenom ?? ''));
                                        if($admin) {
                                            $profileUrl = route('PageAdmin');
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
                       
                    </div>
                    <div class="right">
                        <ul class="flexitem second-links">
                            @if($admin || $vendeur || $client)
                            <li>
                                @if($admin)
                                    <a href="{{ $messagesUrl }}">
                                        <div class="icon-large"><i class="ri-mail-unread-line"></i></div>
                                        <div class="fly-item"><span class="message-number">{{ $messageUnreadCount }}</span></div>
                                    </a>
                                @else
                                    <a href="{{ $messagesUrl }}">
                                        <div class="icon-large"><i class="ri-mail-unread-line"></i></div>
                                        <div class="fly-item"><span class="message-number">{{ $messageUnreadCount }}</span></div>
                                    </a>
                                @endif
                            </li>
                            @endif

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
            <div class="container py-4">
                <div class="row">
                    <section class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                            <br><h1 class="promo-page-title">Produits en Promotion</h1>
                        </div><br>

                        <!-- Barre de filtres -->
                        <div class="promo-filters-bar filters-bar">
                            <form id="promoFilterForm" method="GET" action="{{ route('promotions') }}" class="d-flex flex-wrap align-items-center gap-3" style="width:100%;">
                                <input type="text" name="recherche" value="{{ request('recherche') }}" class="form-control promo-filter-trigger" placeholder="Nom, description..." style="min-width:200px;max-width:320px;">
                                <select name="categorie" class="form-select promo-filter-trigger" style="min-width:160px;max-width:240px;">
                                    <option value="">Toutes les catégories</option>
                                    <option value="Electronique" {{ request('categorie') == 'Electronique' ? 'selected' : '' }}>Électronique</option>
                                    <option value="Vetements" {{ request('categorie') == 'Vetements' ? 'selected' : '' }}>Vêtements</option>
                                    <option value="Chaussures-Femme" {{ request('categorie') == 'Chaussures-Femme' ? 'selected' : '' }}>Chaussures Femme</option>
                                    <option value="Chaussures-Homme" {{ request('categorie') == 'Chaussures-Homme' ? 'selected' : '' }}>Chaussures Homme</option>
                                    <option value="Mode-Homme" {{ request('categorie') == 'Mode-Homme' ? 'selected' : '' }}>Mode Homme</option>
                                    <option value="Mode-Femme" {{ request('categorie') == 'Mode-Femme' ? 'selected' : '' }}>Mode Femme</option>
                                    <option value="Beauté" {{ request('categorie') == 'Beauté' ? 'selected' : '' }}>Beauté</option>
                                    <option value="Mode-Fille" {{ request('categorie') == 'Mode-Fille' ? 'selected' : '' }}>Mode Fille</option>
                                    <option value="Mode-Garçon" {{ request('categorie') == 'Mode-Garçon' ? 'selected' : '' }}>Mode Garçon</option>
                                    <option value="Cuisine&Maison" {{ request('categorie') == 'Cuisine&Maison' ? 'selected' : '' }}>Cuisine & Maison</option>
                                    <option value="Sports" {{ request('categorie') == 'Sports' ? 'selected' : '' }}>Sports</option>
                                    <option value="Aliment" {{ request('categorie') == 'Aliment' ? 'selected' : '' }}>Aliment</option>
                                    <option value="Livres" {{ request('categorie') == 'Livres' ? 'selected' : '' }}>Livres</option>
                                    <option value="Autres" {{ request('categorie') == 'Autres' ? 'selected' : '' }}>Autres</option>
                                </select>
                                <select name="tri_prix" class="form-select promo-filter-trigger" style="min-width:160px;max-width:220px;">
                                    <option value="">Trier par</option>
                                    <option value="recente" {{ request('tri_prix') == 'recente' ? 'selected' : '' }}>Plus récents</option>
                                    <option value="asc" {{ request('tri_prix') == 'asc' ? 'selected' : '' }}>Prix croissant</option>
                                    <option value="desc" {{ request('tri_prix') == 'desc' ? 'selected' : '' }}>Prix décroissant</option>
                                </select>
                                <select name="tri_reduction" class="form-select promo-filter-trigger" style="min-width:180px;max-width:240px;">
                                    <option value="">Réduction</option>
                                    <option value="desc" {{ request('tri_reduction') == 'desc' ? 'selected' : '' }}>Plus forte réduction</option>
                                    <option value="asc" {{ request('tri_reduction') == 'asc' ? 'selected' : '' }}>Plus faible réduction</option>
                                </select>
                                @if(request()->hasAny(['recherche','categorie','tri_prix','tri_reduction']))
                                    <a href="{{ route('promotions') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                                @endif
                            </form>
                        </div>

                        @if(isset($produits) && $produits->count())
                            <p class="promo-count">{{ $produits->count() }} offre(s) en promotion</p>
                            <div class="product-grid">
                                @foreach($produits as $produit)
                                    <div class="product-card card @if($produit->Promotion && isset($produit->Reduction) && $produit->Reduction > 0) product-card-promo @endif">
                                        <div class="position-relative">
                                            @if($produit->Promotion && isset($produit->Reduction) && $produit->Reduction > 0)
                                                <span class="badge-promo" style="border-radius:4px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.2);">
                                                    <span style="background:#ffc107;color:#000;padding:3px 6px;font-size:0.7rem;font-weight:600;">En promotion</span>
                                                    <span style="background:#e65100;color:#fff;padding:3px 5px;font-size:0.7rem;font-weight:700;">-{{ $produit->Reduction }}%</span>
                                                </span>
                                            @endif
                                            @php
                                                $imgUrl = 'https://via.placeholder.com/400x300?text=No+Image';
                                                $img = trim((string)($produit->Image ?? ''));
                                                if($img !== ''){
                                                    if(preg_match('/^https?:\/\//i', $img)){
                                                        $imgUrl = $img;
                                                    }
                                                    elseif(\Illuminate\Support\Facades\Storage::exists('public/'.$img)){
                                                        $imgUrl = asset('storage/'.$img);
                                                    }
                                                    elseif(file_exists(public_path($img))){
                                                        $imgUrl = asset($img);
                                                    }
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
                                                @if($produit->Promotion && isset($produit->Reduction) && $produit->Reduction > 0 && isset($produit->PrixOriginal) && $produit->PrixOriginal > $produit->Prix)
                                                    <div class="product-price" style="display:flex;flex-direction:column;align-items:flex-start;">
                                                        <span style="color:#e53935;text-decoration:line-through;font-size:0.9em;">{{ number_format($produit->PrixOriginal, 0, ',', ' ') }} FCFA</span>
                                                        <span style="color:#1e88e5;font-weight:700;font-size:1.1em;">{{ number_format($produit->Prix, 0, ',', ' ') }} FCFA</span>
                                                    </div>
                                                @else
                                                    <div class="product-price">{{ number_format($produit->Prix ?? 0, 0, ',', ' ') }} FCFA</div>
                                                @endif
                                                <button type="button" class="btn btn-sm btn-outline-secondary product-open" data-id="{{ $produit->idProduit }}" data-name="{{ $dataName }}" data-desc="{{ $dataDesc }}" data-price="{{ $dataPrice }}" data-img="{{ $dataImg }}" data-vendor-name="{{ $vendorName }}" data-vendor-address="{{ $vendorAddress }}" data-stock="{{ $produit->Stock ?? 0 }}" data-category="{{ $produit->Categorie ?? '' }}" data-similar='@json($similar)'>Voir</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <h4>Aucun produit en promotion actuellement</h4>
                                <p>Revenez bientôt pour découvrir nos prochaines offres promotionnelles !</p>
                                <a href="/" class="btn btn-primary">Retour à l'accueil</a>
                            </div>
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
        (function(){
            var form = document.getElementById('promoFilterForm');
            if(!form) return;
            var searchInput = form.querySelector('input[name="recherche"]');
            var selects = form.querySelectorAll('.promo-filter-trigger');
            var debounceTimer;
            function submitForm(){ form.submit(); }
            selects.forEach(function(el){
                el.addEventListener('change', submitForm);
            });
            if(searchInput){
                searchInput.addEventListener('input', function(){
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(submitForm, 400);
                });
            }
        })();
    </script>
    <script>
        // Reuse product detail script from PagePrincipale for product-open behavior
        (function(){
            let savedStack = [];
            function renderDetail(data){
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
                const container = document.querySelector('main');
                if(!container) return;
                savedStack.push({ html: container.innerHTML, scroll: window.scrollY || window.pageYOffset || 0 });
                container.innerHTML = html;
                try{ history.pushState({ produitId: data.id || null }, '', data.id ? ('?produit=' + encodeURIComponent(data.id)) : window.location.pathname); }catch(e){}
            }
            function restoreMain(){
                const container = document.querySelector('main');
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
                    const needsAjax = !(data.vendorName || data.vendorAddress) || data.stock === '' || !data.similar;
                    if(needsAjax && data.id){
                        const url = '/produit/' + encodeURIComponent(data.id);
                        const container = document.querySelector('main');
                        if(!container){ renderDetail(data); return; }
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
                    if(history.state && history.state.produitId) history.back(); else restoreMain();
                    return;
                }
            });
            window.addEventListener('popstate', function(e){
                if(e.state && e.state.produitId){
                    restoreMain();
                }
            });
        })();
    </script>

    <div id="toast-container" style="position:fixed;right:16px;bottom:16px;z-index:2000;display:flex;flex-direction:column;gap:8px"></div>
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
                <div id="mini-cart-footer-total" style="display:none;font-weight:700;color:#0b66d1;">0 FCFA</div>
            </div>
        </div>
    </div>
</body>
</html>
