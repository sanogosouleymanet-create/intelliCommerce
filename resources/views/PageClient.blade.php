<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel ="stylesheet" href="{{ asset('css/StylePagePrincipale.css') }}">
    <link rel="stylesheet" href="{{ asset('css/StylePageClient.css') }}">
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
        /* Décaler l'aside un peu vers la gauche sur écrans medium et plus */
        @media (min-width: 768px) {
            .client-dashboard .row > aside { transform: translateX(-50px); }
        }
        /* Augmenter la largeur du conteneur principal pour donner plus d'espace 
        .client-dashboard > .container {
            max-width: 1800px !important;
            width: 100% !important;
        }*/
    </style>
</head>
<body>
    <div id="page" class="site client-shop">
        <!-- Header copied from PagePrincipale for consistency -->
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
                                    $client = $client ?? Auth::guard('client')->user();

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
                                        <button type="button" onclick="location.href='{{ $profileUrl }}'" style="display:inline-flex;align-items:center;gap:8px;padding:0px 15px;border-radius:4px;border:1px solid #ddd;background:#fff;color:#2b7cff;cursor:pointer">
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
                        <div class="logo"><a href="/"><span class="circle"></span><img src="{{ asset('Logo-site.png') }}" width="250" alt="logo"></a></div>
                        <nav class="mobile-hide">
                            <ul class="flexitem second-links">
                                
                            <li><a href="/">Accueil</a></li>
                            <li><a href="#">À propos</a></li>
                            <li><a href="#">Contact</a></li>
                       
                            </ul>
                        </nav>
                    </div>
                    <div class="right">
                        <ul class="flexitem second-links">
                            <!--<li class="mobile-hide"><a href="#">
                                <div class="icon-large"><i class="ri-heart-line"></i></div>
                                <div class="fly-item"><span class="item-number">0</span></div>
                            </a></li>-->
                            <li><a href="#" class="iscart">
                                <div class="icon-large"><i class="ri-shopping-cart-line"></i></div>
                                    <div class="fly-item"><span class="item-number">{{ $cartCount }}</span></div>
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
                            <h5 class="mb-1" style="text-align: center;">{{ $client ? ($client->Nom . ' ' . ($client->Prenom ?? '')) : 'Client' }}</h5>
                            <div class="text-muted small" style="text-align: center;">Espace Client</div>
                            
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
                        @yield('content')
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
                                                                @php
                                                                    $total = $commande->Produit->sum(function($produit) {
                                                                        $prix = $produit->pivot->PrixUnitaire ?? $produit->Prix ?? 0;
                                                                        $qty = $produit->pivot->Quantite ?? 0;
                                                                        return (float)$prix * (float)$qty;
                                                                    });
                                                                @endphp
                                                                <div class="text-end">Total: <strong>{{ number_format($total ?? 0, 0, ',', ' ') }} FCFA</strong></div>
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
                                        @php
                                            $allMessages = $client->message()->orderBy('DateEnvoi', 'desc')->get();
                                            $recentMessages = $allMessages->take(3);
                                            $unreadInRecent = $recentMessages->where('Statut', 'non lu')->count();
                                            $totalUnread = $allMessages->where('Statut', 'non lu')->count();
                                            $hasMoreUnread = $totalUnread > $unreadInRecent;
                                        @endphp
                                        <h5>Messages @if($hasMoreUnread) <i class="fas fa-envelope text-warning"></i> @endif</h5>
                                        @if($recentMessages->count())
                                            <ul>
                                                @foreach($recentMessages as $msg)
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
                        let html = await res.text();
                        // Remplacer les imports vers le style vendeur par le style client
                        html = html.replace(/StyleVendeurProduits\.css/g, 'StylePageClient.css');
                        const tmp = document.createElement('div'); tmp.innerHTML = html;
                        const target = document.querySelector('main .container .row section.col-md-9');
                        if(target){
                            // Insert the full response so any <style> or <script> in the partial is preserved
                            // but ensure inline scripts in the partial are executed: parse them from html and run
                            const tmpDiv = document.createElement('div'); tmpDiv.innerHTML = html;
                            // move styles and html into target
                            target.innerHTML = html;
                            // execute inline scripts from the response
                            tmpDiv.querySelectorAll('script').forEach(s => {
                                const ns = document.createElement('script');
                                if(s.src) ns.src = s.src;
                                else ns.textContent = s.textContent;
                                document.body.appendChild(ns);
                                ns.parentNode.removeChild(ns);
                            });
                            history.pushState(null,'',href);
                            // update active tab
                            document.querySelectorAll('a[data-client-nav]').forEach(a => a.classList.toggle('active', a.getAttribute('href') === href));
                            // run helpers
                            applyClientsParamStyles();
                            ensureClientInputsStyled();
                            initClientParamForm();
                            // call messages initializer if present
                            try{ if(window.initClientMessages) window.initClientMessages(); }catch(e){}
                        } else {
                            console.warn('Target container for client partial not found');
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
                        let html = await res.text();
                        // Remplacer les imports vers le style vendeur par le style client
                        html = html.replace(/StyleVendeurProduits\.css/g, 'StylePageClient.css');
                        const tmp = document.createElement('div'); tmp.innerHTML = html;
                        const target = document.querySelector('main .container .row section.col-md-9');
                        if(target){
                            const tmpDiv = document.createElement('div'); tmpDiv.innerHTML = html;
                            target.innerHTML = html;
                            tmpDiv.querySelectorAll('script').forEach(s => {
                                const ns = document.createElement('script');
                                if(s.src) ns.src = s.src;
                                else ns.textContent = s.textContent;
                                document.body.appendChild(ns);
                                ns.parentNode.removeChild(ns);
                            });
                            document.querySelectorAll('a[data-client-nav]').forEach(a => a.classList.toggle('active', a.getAttribute('href') === (location.pathname + location.search)));
                            applyClientsParamStyles();
                            ensureClientInputsStyled();
                            initClientParamForm();
                            try{ if(window.initClientMessages) window.initClientMessages(); }catch(e){}
                        }
                    }catch(e){ console.error('popstate fetch failed', e); }
                })();
            });

            // ensure active client nav reflects current URL on initial load
            (function(){
                const cur = (location.pathname + location.search);
                document.querySelectorAll('a[data-client-nav]').forEach(a => a.classList.toggle('active', a.getAttribute('href') === cur));
            })();
            // If the partial is already present on initial load, initialize its behaviours
            applyClientsParamStyles();
            ensureClientInputsStyled();
            initClientParamForm();
            
            // Inject CSS for client parameters when partials are loaded via AJAX
            function applyClientsParamStyles(){
                if(document.getElementById('clients-parametres-style')) return;
                const css = `
                .clients-parametres .form-control, .clients-parametres input.form-control, .clients-parametres textarea.form-control {
                    border: 1px solid #000 !important;
                    box-shadow: none !important;
                    background-color: #fff !important;
                    color: #000 !important;
                }
                .clients-parametres .form-control-plaintext, .clients-parametres input.form-control-plaintext, .clients-parametres textarea.form-control-plaintext {
                    border: 1px solid #000 !important;
                    padding: .375rem .75rem !important;
                    border-radius: .25rem !important;
                    background-color: #fff !important;
                    color: #000 !important;
                    box-shadow: none !important;
                    display: block;
                    width: 100%;
                }
                .clients-parametres .form-control:focus { box-shadow: 0 0 0 .2rem rgba(43,124,255,.15) !important; }
                `;
                const s = document.createElement('style'); s.id = 'clients-parametres-style'; s.appendChild(document.createTextNode(css));
                document.head.appendChild(s);
            }
            
            // Also apply inline styles to inputs in the injected partial to ensure immediate rendering
            function ensureClientInputsStyled(){
                const container = document.querySelector('.clients-parametres');
                if(!container) return;
                const inputs = container.querySelectorAll('input.form-control-plaintext, textarea.form-control-plaintext, input.form-control, textarea.form-control');
                inputs.forEach(i => {
                    i.style.border = '1px solid #000';
                    i.style.backgroundColor = '#fff';
                    i.style.boxShadow = 'none';
                    i.style.color = '#000';
                    i.style.padding = i.style.padding || '.375rem .75rem';
                    i.classList.remove('form-control-plaintext');
                    i.classList.add('form-control');
                });
            }

            // Initialize client parameters form behaviour (readonly -> edit -> save via AJAX)
            function initClientParamForm(){
                const form = document.getElementById('formParametres');
                if(!form || form.dataset.inited) return; form.dataset.inited = '1';

                const inputs = Array.from(form.querySelectorAll('input, textarea')).filter(i => i.type !== 'hidden' && i.type !== 'submit' && i.type !== 'button');
                const btnEdit = document.getElementById('btnEditParam');
                const btnSave = document.getElementById('btnSaveParam');
                const status = document.getElementById('paramStatus');

                const original = {};
                inputs.forEach(i => original[i.name||i.id] = i.value);

                function setReadOnly(state){
                    inputs.forEach(i => { i.readOnly = state; i.classList.toggle('form-control-plaintext', state); i.classList.toggle('form-control', !state); });
                    if(btnSave) btnSave.disabled = state;
                    if(btnEdit) { btnEdit.textContent = state ? 'Modifier' : 'Annuler'; btnEdit.classList.toggle('btn-outline-danger', !state); }
                }

                setReadOnly(true);

                btnEdit?.addEventListener('click', function(){
                    const editing = btnSave ? btnSave.disabled : true;
                    if(editing){ setReadOnly(false); inputs[0]?.focus(); }
                    else { inputs.forEach(i => i.value = original[i.name||i.id] || ''); setReadOnly(true); }
                });

                form.addEventListener('submit', async function(e){
                    e.preventDefault();
                    if(btnSave && btnSave.disabled) return;

                    // basic client-side validation
                    const emailField = form.querySelector('[name="email"]');
                    if(emailField && emailField.value.trim()){ const re = /^\S+@\S+\.\S+$/; if(!re.test(emailField.value.trim())){ if(status) status.innerHTML = '<div class="alert alert-danger">Email invalide</div>'; return; } }

                    // if changing password, verify current password first via AJAX
                    const currentPwd = form.querySelector('[name="current_password"]')?.value || '';
                    const newPwd = form.querySelector('[name="new_password"]')?.value || '';
                    const confirmPwd = form.querySelector('[name="new_password_confirmation"]')?.value || '';
                    if(newPwd){
                        if(newPwd.length < 8){ if(status) status.innerHTML = '<div class="alert alert-danger">Le nouveau mot de passe doit contenir au moins 8 caractères.</div>'; return; }
                        if(newPwd !== confirmPwd){ if(status) status.innerHTML = '<div class="alert alert-danger">La confirmation du mot de passe ne correspond pas.</div>'; return; }
                        if(!currentPwd){ if(status) status.innerHTML = '<div class="alert alert-danger">Veuillez saisir le mot de passe actuel pour le modifier.</div>'; return; }

                        try{
                            const verifyRes = await fetch('/parametres/verify-password', {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': (form.querySelector('input[name="_token"]')||{}).value || '' },
                                body: JSON.stringify({ current_password: currentPwd })
                            });
                            const verifyJson = await verifyRes.json().catch(()=>({}));
                            if(!verifyRes.ok || !verifyJson.success){
                                if(status) status.innerHTML = '<div class="alert alert-danger">Mot de passe actuel incorrect.</div>';
                                return;
                            }
                        }catch(err){ if(status) status.innerHTML = '<div class="alert alert-danger">Erreur vérification mot de passe.</div>'; return; }
                    }

                    const fd = new FormData(form);
                    btnSave.disabled = true;
                    try{
                        const res = await fetch(form.action || '/parametres', { method: 'POST', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': (form.querySelector('input[name="_token"]')||{}).value || '' }, body: fd });
                        const json = await res.json().catch(()=>({}));
                        btnSave.disabled = false;
                        if(res.ok && json.success !== false){
                            // update originals
                            inputs.forEach(i => original[i.name||i.id] = i.value);
                            setReadOnly(true);
                            if(status) status.innerHTML = '<div class="alert alert-success">'+(json.message||'Enregistré')+'</div>';
                            setTimeout(()=>{ if(status) status.innerHTML = ''; }, 2500);
                        } else {
                            if(status) status.innerHTML = '<div class="alert alert-danger">'+(json.message||'Erreur')+'</div>';
                        }
                    }catch(err){ btnSave.disabled = false; if(status) status.innerHTML = '<div class="alert alert-danger">Erreur réseau</div>'; }
                });
            }
        })();
    </script>
    <script>
        // Mini-cart modal handling
        (function(){
            const cartIcon = document.querySelector('.iscart');
            const overlay = document.getElementById('mini-cart-overlay');
            const modal = document.getElementById('mini-cart-modal');
            const closeBtn = document.getElementById('mini-cart-close');
            const body = document.getElementById('mini-cart-body');
            const footerTotal = document.getElementById('mini-cart-footer-total');

            // Function to load cart content
            async function loadCart(){
                try{
                    const res = await fetch('/cart/fragment', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if(res.ok){
                        const html = await res.text();
                        body.innerHTML = html;
                        // Update total if present
                        const totalEl = body.querySelector('.cart-total');
                        if(totalEl && footerTotal){
                            footerTotal.textContent = totalEl.textContent;
                            footerTotal.style.display = 'block';
                        } else {
                            footerTotal.style.display = 'none';
                        }
                    } else {
                        body.innerHTML = '<div style="text-align:center;color:#666;padding:28px 6px">Erreur lors du chargement du panier.</div>';
                    }
                } catch(err){
                    body.innerHTML = '<div style="text-align:center;color:#666;padding:28px 6px">Erreur réseau.</div>';
                }
            }

            // Open modal
            if(cartIcon){
                cartIcon.addEventListener('click', function(e){
                    e.preventDefault();
                    overlay.style.display = 'flex';
                    loadCart();
                });
            }

            // Close modal
            if(closeBtn){
                closeBtn.addEventListener('click', function(){
                    overlay.style.display = 'none';
                });
            }

            if(overlay){
                overlay.addEventListener('click', function(e){
                    if(e.target === overlay){
                        overlay.style.display = 'none';
                    }
                });
            }
        })();
    </script>
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
</body>
</html>
