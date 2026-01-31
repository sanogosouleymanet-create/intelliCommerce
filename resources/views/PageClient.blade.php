<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel ="stylesheet" href="{{ asset('css/StylePageClient.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Mon Espace - Client</title>
    <style>
        /* small overrides to adapt PagePrincipale style to client dashboard */
        .client-dashboard { padding: 24px 0; }
        .profile-card { border:1px solid #eee; padding:16px; border-radius:6px; background:#fff; }
        .profile-actions { margin-top:12px; }
        .orders-list .order { border-bottom:1px dashed #efefef; padding:12px 0; }
        .recommended { margin-top:18px; }
    </style>
</head>
<body>
    <div id="page" class="site">
        <!-- Header copied from PagePrincipale for consistency -->
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
                                    $client = $client ?? Auth::guard('client')->user();
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
                                </li>
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
                        </ul>
                    </div>
                </div>
           </div> 
           </div>
        </header>

        <main>
            <div class="container py-4 client-dashboard">
                <div class="row">
                    <aside class="col-md-3">
                        <div class="profile-card">
                            <h4>Bonjour, {{ $client ? ($client->Nom . ' ' . ($client->Prenom ?? '')) : 'Client' }}</h4>
                            <div class="profile-actions d-flex flex-column">
                                <a href="/PageClient?view=dashboard" class="btn btn-sm btn-outline-primary mb-2" data-client-nav>Tableau de bord</a>
                                <a href="/commandes" class="btn btn-sm btn-outline-secondary mb-2" data-client-nav>Mes commandes</a>
                                <a href="/messages" class="btn btn-sm btn-outline-secondary mb-2" data-client-nav>Messages</a>
                                <a href="/parametres" class="btn btn-sm btn-outline-secondary" data-client-nav>Paramètres</a>
                            </div>
                            <div class="mt-3 d-flex justify-content-center">
                                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Déconnexion</button>
                                </form>
                            </div>
                        </div>
                    </aside>

                    <section class="col-md-9">
                        @if(isset($partial))
                            @include($partial)
                        @else
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <h2 class="m-0">Mon tableau de bord</h2>
                                <div class="small text-muted">Dernières activités</div>
                            </div>

                            

                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="card p-3">
                                        <h5>Dernières commandes</h5>
                                        <div class="orders-list">
                                            @if(isset($client) && $client->commandes && $client->commandes->count())
                                                @foreach($client->commandes->sortByDesc('DateCommande')->take(5) as $commande)
                                                    <div class="order">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <strong>Commande #{{ $commande->idCommande }}</strong>
                                                                <div class="text-muted">Le {{ \Carbon\Carbon::parse($commande->DateCommande)->format('d/m/Y H:i') }}</div>
                                                            </div>
                                                            <div>
                                                                <div class="text-end">Total: <strong>{{ number_format($commande->Montant,0,',',' ') }} FCFA</strong></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-muted">Aucune commande récente.</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <div class="card p-3">
                                        <h5>Messages</h5>
                                        @if(isset($client) && $client->message && $client->message->count())
                                            <ul>
                                                @foreach($client->message->sortByDesc('DateEnvoi')->take(5) as $msg)
                                                    <li>{{ \Illuminate\Support\Str::limit($msg->Contenu, 120) }} <small class="text-muted">— {{ \Carbon\Carbon::parse($msg->DateEnvoi)->diffForHumans() }}</small></li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="text-muted">Aucun message pour le moment.</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12">
                                    <h5>Produits recommandés</h5>
                                    @php
                                        $reco = App\Models\Produit::orderBy('DateAjout','desc')->take(8)->get();
                                    @endphp
                                    @if($reco->count())
                                        <div class="product-grid">
                                            @foreach($reco as $produit)
                                                <div class="product-card card">
                                                    <div class="position-relative">
                                                        @php
                                                            $imgUrl = 'https://via.placeholder.com/400x300?text=No+Image';
                                                            $img = trim((string)($produit->Image ?? ''));
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
                                                        <img src="{{ $imgUrl }}" class="card-img-top" alt="{{ $produit->Nom }}">
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
                                    @else
                                        <div class="text-muted">Aucun produit à recommander pour le moment.</div>
                                    @endif
                                </div>
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
        // keep SPA behaviour for client area (links like /commandes, /messages, /parametres)
        (function(){
            document.addEventListener('click', function(e){
                const anchor = e.target.closest && e.target.closest('a');
                if(!anchor) return;
                // only handle SPA nav links that opt-in with data-client-nav
                if(!anchor.hasAttribute || !anchor.hasAttribute('data-client-nav')) return;
                const href = anchor.getAttribute('href');
                if(!href) return;
                e.preventDefault();
                (async function(){
                    try{
                        const res = await fetch(href, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
                        const html = await res.text();
                        const tmp = document.createElement('div'); tmp.innerHTML = html;
                        const partial = tmp.querySelector('section') || tmp;
                        const target = document.querySelector('main .container .row section.col-md-9');
                        if(partial && target){
                            target.innerHTML = partial.innerHTML;
                            history.pushState(null,'',href);
                            // update active tab
                            document.querySelectorAll('a[data-client-nav]').forEach(a => a.classList.toggle('active', a.getAttribute('href') === href));
                        } else if(target){
                            target.innerHTML = '<div class="alert alert-warning">Impossible de charger le contenu.</div>';
                        }
                    }catch(err){
                        console.error('Error fetching partial', href, err);
                        const target = document.querySelector('main .container .row section.col-md-9');
                        if(target) target.innerHTML = '<div class="alert alert-danger">Erreur réseau lors du chargement.</div>';
                    }
                })();
            });

            window.addEventListener('popstate', function(){
                (async function(){
                    try{
                        const res = await fetch(location.pathname + location.search, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
                        const html = await res.text();
                        const tmp = document.createElement('div'); tmp.innerHTML = html;
                        const partial = tmp.querySelector('section') || tmp;
                        const target = document.querySelector('main .container .row section.col-md-9');
                        if(partial && target){
                            target.innerHTML = partial.innerHTML;
                            document.querySelectorAll('a[data-client-nav]').forEach(a => a.classList.toggle('active', a.getAttribute('href') === (location.pathname + location.search)));
                        }
                    }catch(e){ console.error('popstate fetch failed', e); }
                })();
            });

            // ensure active client nav reflects current URL on initial load
            (function(){
                const cur = (location.pathname + location.search);
                document.querySelectorAll('a[data-client-nav]').forEach(a => a.classList.toggle('active', a.getAttribute('href') === cur));
            })();
        })();
    </script>
</body>
</html>