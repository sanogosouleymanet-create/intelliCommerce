<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>window.isClientAuthenticated = @json(auth()->guard('client')->check());</script>
    <script>window.isAuthenticated = @json(auth()->guard('client')->check() || auth()->guard('vendeur')->check() || auth()->guard('administrateur')->check());</script>
    <link rel="stylesheet" href="{{ asset('css/StylePagePrincipale.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ENjdO4Dr2bkBIFxQpeoA6VZ6bQZ6Y9o2e2Z1ZlFZC+0h5Y5n3/tf6Yb6Y1Y3pXx+" crossorigin="anonymous">
    <title>Les plus recherchés - Intelli-Commerce</title>
    <style>
        .page-top-recherches main { padding-bottom: 3rem; }
        .page-top-recherches .page-title { font-size: 1.75rem; font-weight: 700; color: #1a1a2e; margin: 0 0 1rem 0; }
        .page-top-recherches .filters-bar {
            background: linear-gradient(135deg, #f0f7ff 0%, #e8f4fc 100%);
            border: 1px solid rgba(11, 102, 209, 0.25);
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 16px rgba(11, 102, 209, 0.08);
        }
        .page-top-recherches .filters-bar .form-control,
        .page-top-recherches .filters-bar .form-select {
            border-radius: 8px; border: 1px solid #e9ecef;
            padding: 10px 14px; font-size: 0.95rem;
        }
        .page-top-recherches .filters-bar .form-control:focus,
        .page-top-recherches .filters-bar .form-select:focus {
            border-color: #0b66d1; box-shadow: 0 0 0 3px rgba(11, 102, 209, 0.2); outline: 0;
        }
        .page-top-recherches .result-count {
            display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px;
            background: linear-gradient(135deg, #0b66d1 0%, #1e88e5 100%); color: #fff;
            font-weight: 700; font-size: 0.95rem; border-radius: 20px; margin-bottom: 1.25rem;
        }
        .page-top-recherches .product-grid { padding-left: 0; }
        .page-top-recherches .product-card { transition: transform .2s ease, box-shadow .2s ease; }
        .page-top-recherches .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
        @media (max-width: 768px) {
            .page-top-recherches .filters-bar form { flex-direction: column; align-items: stretch; }
            .page-top-recherches .filters-bar .form-control,
            .page-top-recherches .filters-bar .form-select { max-width: none; min-width: 0; }
        }
    </style>
</head>
<body class="page-top-recherches">
    <div id="page" class="site">
        <aside class="site-off desktop-hide">
            <div class="off-canvas">
                <div class="canvas-head flexitem">
                    <div class="logo"><a href="/"><span class="circle"></span><img src="Logo-site.png" alt="logo"></a></div>
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
                                    $cartCount = 0;
                                    $cartKey = auth()->guard('client')->check() ? 'cart_client_' . auth()->guard('client')->id()
                                        : (auth()->guard('vendeur')->check() ? 'cart_vendeur_' . auth()->guard('vendeur')->id()
                                        : (auth()->guard('administrateur')->check() ? 'cart_admin_' . auth()->guard('administrateur')->id()
                                        : 'cart_guest_' . session()->getId()));
                                    $cart = session($cartKey, []);
                                    if(is_array($cart) && !empty($cart)){
                                        $cartCount = count($cart);
                                    }
                                    $messageUnreadCount = 0;
                                    $messagesUrl = '#';
                                    if(auth()->guard('client')->check()){
                                        $messageUnreadCount = \App\Models\Message::where('Client_idClient', auth()->guard('client')->id())
                                            ->whereIn('Statut', ['non lu','envoye'])->whereIn('sender_type', ['vendeur', 'administrateur'])->count();
                                        $messagesUrl = '/messages';
                                    } elseif(auth()->guard('vendeur')->check()){
                                        $messageUnreadCount = \App\Models\Message::where('Vendeur_idVendeur', auth()->guard('vendeur')->id())
                                            ->whereIn('Statut', ['non lu','envoye'])->whereIn('sender_type', ['client', 'administrateur'])->count();
                                        $messagesUrl = '/vendeur/messages';
                                    } elseif(auth()->guard('administrateur')->check()){
                                        $messageUnreadCount = \App\Models\Message::where('Administrateur_idAdministrateur', auth()->guard('administrateur')->id())
                                            ->whereIn('Statut', ['non lu','envoye'])->whereIn('sender_type', ['client', 'vendeur'])->count();
                                        $messagesUrl = route('admin.dashboard') . '#messages';
                                    }
                                @endphp
                                @if($admin || $vendeur || $client)
                                    @php
                                        $user = $admin ?? $vendeur ?? $client;
                                        $displayName = trim($user->Nom . ' ' . ($user->Prenom ?? ''));
                                        $profileUrl = $admin ? route('PageAdmin') : ($vendeur ? route('PageVendeur') : route('PageClient'));
                                    @endphp
                                    <div style="display:flex;align-items:center;gap:12px">
                                        <button type="button" onclick="location.href='{{ $profileUrl }}'" class="login">
                                            <i class="fa-solid fa-user"></i><span>{{ $displayName }}</span>
                                        </button>
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
                                <a href="{{ $messagesUrl }}">
                                    <div class="icon-large"><i class="ri-mail-unread-line"></i></div>
                                    <div class="fly-item"><span class="message-number">{{ $messageUnreadCount }}</span></div>
                                </a>
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
                            <h1 class="page-title">Les plus recherchés</h1>
                        </div>

                        <div class="filters-bar">
                            <form id="topFilterForm" method="GET" action="{{ url('/top-recherches') }}" class="d-flex flex-wrap align-items-center gap-3" style="width:100%;">
                                <input type="text" name="recherche" value="{{ request('recherche') }}" class="form-control top-filter-trigger" placeholder="Nom, description..." style="min-width:200px;max-width:320px;">
                                <select name="categorie" class="form-select top-filter-trigger" style="min-width:160px;max-width:240px;">
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
                                <select name="tri_prix" class="form-select top-filter-trigger" style="min-width:160px;max-width:220px;">
                                    <option value="">Trier par</option>
                                    <option value="recente" {{ request('tri_prix') == 'recente' ? 'selected' : '' }}>Plus récents</option>
                                    <option value="asc" {{ request('tri_prix') == 'asc' ? 'selected' : '' }}>Prix croissant</option>
                                    <option value="desc" {{ request('tri_prix') == 'desc' ? 'selected' : '' }}>Prix décroissant</option>
                                </select>
                                @if(request()->hasAny(['recherche','categorie','tri_prix']))
                                    <a href="{{ url('/top-recherches') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                                @endif
                            </form>
                        </div>

                        @if(isset($produits) && $produits->count())
                            <p class="result-count">{{ $produits->count() }} produit(s)</p>
                            <div class="product-grid" id="top-list-grid">
                                @foreach($produits as $produit)
                                    @include('partials.top_list_card', ['produit' => $produit])
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <h4>Aucun produit trouvé</h4>
                                <p>Modifiez les filtres ou revenez à l'accueil.</p>
                                <a href="/" class="btn btn-primary">Retour à l'accueil</a>
                            </div>
                        @endif
                    </section>
                </div>
            </div>
        </main>
        <footer></footer>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
    <script>
        (function(){
            var form = document.getElementById('topFilterForm');
            if(!form) return;
            var searchInput = form.querySelector('input[name="recherche"]');
            var selects = form.querySelectorAll('.top-filter-trigger');
            var debounceTimer;
            function submitForm(){ form.submit(); }
            selects.forEach(function(el){ el.addEventListener('change', submitForm); });
            if(searchInput){
                searchInput.addEventListener('input', function(){
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(submitForm, 400);
                });
            }
        })();
    </script>
    <script>
        (function(){
            let savedStack = [];
            function restoreMain(){
                var container = document.querySelector('main');
                if(!container) return;
                if(savedStack.length){
                    var entry = savedStack.pop();
                    container.innerHTML = entry.html;
                    if(typeof entry.scroll === 'number') window.scrollTo({ top: entry.scroll, left: 0, behavior: 'auto' });
                }
            }
            window.restoreMainTop = restoreMain;
            document.addEventListener('click', function(e){
                var btn = e.target.closest('.product-open');
                if(btn){
                    e.preventDefault();
                    var data = { id: btn.dataset.id, name: btn.dataset.name || '', desc: btn.dataset.desc || '', price: btn.dataset.price || '', img: btn.dataset.img || '', vendorName: btn.dataset.vendorName || '', vendorAddress: btn.dataset.vendorAddress || '', stock: btn.dataset.stock || '', category: btn.dataset.category || '', similar: btn.dataset.similar || '' };
                    var container = document.querySelector('main');
                    if(!container) return;
                    if(data.id){
                        savedStack.push({ html: container.innerHTML, scroll: window.scrollY || 0 });
                        fetch('/produit/' + encodeURIComponent(data.id), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(function(r){ return r.text(); })
                            .then(function(html){ container.innerHTML = html; try{ history.pushState({ produitId: data.id }, '', '?produit=' + encodeURIComponent(data.id)); }catch(e){} })
                            .catch(function(err){ console.error(err); savedStack.pop(); });
                    }
                    return;
                }
                if(e.target.closest('.js-back')){
                    e.preventDefault();
                    if(history.state && history.state.produitId) history.back(); else restoreMain();
                }
            });
            window.addEventListener('popstate', function(e){
                if(e.state && e.state.produitId){
                    restoreMain();
                }
            });
        })();
    </script>
    <div id="toast-container" style="position:fixed;right:16px;bottom:16px;z-index:2000;"></div>
    <div id="mini-cart-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:2000;align-items:center;justify-content:flex-end;padding:24px;">
        <div id="mini-cart-modal" style="width:720px;max-width:96%;max-height:92vh;overflow:auto;background:#fff;border-radius:12px;box-shadow:0 12px 40px rgba(0,0,0,0.35);">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 18px;border-bottom:1px solid #f1f1f1;">
                <strong>Mon panier</strong>
                <button id="mini-cart-close" type="button" aria-label="Fermer" style="border:0;background:transparent;font-size:18px;cursor:pointer">✕</button>
            </div>
            <div id="mini-cart-body" style="padding:14px;">Chargement…</div>
            <div style="padding:14px;border-top:1px solid #f7f7f7;">
                <a href="/cart" class="shiny-button">Voir le panier</a>
            </div>
        </div>
    </div>
</body>
</html>
