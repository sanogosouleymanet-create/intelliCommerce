<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>window.isClientAuthenticated = @json(auth()->guard('client')->check());</script>
    <script>window.isAuthenticated = @json(auth()->guard('client')->check() || auth()->guard('vendeur')->check() || auth()->guard('administrateur')->check());</script>
    <link rel ="stylesheet" href="{{ asset('css/StylePagePrincipale.css') }}">
    <link rel="stylesheet" href="{{ asset('css/StyleVendeurProduits.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Mon Espace - Vendeur</title>
    <style>
        /* small overrides to adapt PagePrincipale style to vendeur dashboard */
        .vendeur-dashboard { padding: 24px 0; }
        .profile-card {
            border:1px solid #eee; padding:16px; border-radius:6px; background:#fff; }
        .profile-actions { margin-top:12px; }
        /* Active state for sidebar buttons */
        .profile-actions a.active { background:#2b7cff; color:#fff; border-color:#2b7cff; }
        .profile-actions a.active i { color:#fff; }
        .orders-list .order { border-bottom:1px dashed #efefef; padding:12px 0; }
        .recommended { margin-top:18px; }

        .main-content::-webkit-scrollbar { display: none; } /* Chrome, Safari, Opera */

        /* Responsiveness for small screens */
        @media (max-width: 767px) {
            .logo img { width: 150px; }
            aside { display: none; }
            .main-content { width: 100%; margin-left: 0; }
            aside.show {
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                width: 250px;
                height: 100%;
                z-index: 1000;
                background: #fff;
                box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            }
        }
        /* Ensure header right-side icons aren't flush against the viewport edge */
        .header-nav .wrapper .right { padding-right: 18px; }
        /* small extra spacing for the IA notification icon */
        .header-nav .second-links .ia-notif { margin-right: 6px; }
        /* Badge styles used by cart and notification icons */
        .header-nav .second-links{display:flex;gap:12px;align-items:center}
        .header-nav .second-links a{position:relative;display:inline-flex;align-items:center;padding:6px 8px}
        .header-nav .second-links .icon-large{display:inline-flex; width: 22px; height: 22px; align-items:center;justify-content:center;position:relative}
        .header-nav .second-links .icon-large .fly-item{position:absolute;top:0;right:0;pointer-events:none;transform: translate(-10%, -50%);
    pointer-events: none;}
        /* Per-icon tweaks to align badges precisely */
        .header-nav .second-links .iscart .icon-large .fly-item{top:0px;right:0}
        /* Slightly reduce badge size on very small screens */
        @media (max-width:420px){ .header-nav .second-links .item-number{font-size:11px;padding:1px 5px;min-width:18px} }
    </style>
</head>
<body>
    <div id="page" class="site">
        <!-- Header (same style as client pages for consistency) -->
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
                                    $vendeur = $vendeur ?? Auth::guard('vendeur')->user();
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
                                            $profileUrl = route('admin.PageAdmin');
                                        } elseif($vendeur) {
                                            $profileUrl = route('PageVendeur');
                                        } else {
                                            $profileUrl = route('PageClient');
                                        }
                                    @endphp
                                    <div style="display:flex;align-items:center;gap:12px">
                                        <button type="button" onclick="location.href='{{ $profileUrl }}'" style="display:inline-flex;align-items:center;gap:8px;padding:0px 15px;border-radius:4px;border:1px solid #ddd;background:#fff;color:#2b7cff;cursor:pointer" >
                                            <i class="fa-solid fa-user"></i>
                                            <span>{{ $displayName }}</span>
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
                        <div class="logo"><a href="/"><span class="circle"></span><img src="{{ asset('Logo-site.png') }}" width="250" alt="logo"></a></div>
                        <nav class="mobile-hide">
                            <ul class="flexitem second-links">
                                <li><a href="/">Accueil</a></li>
                                <li><a href="#">À propos</a></li>
                                <li><a href="#">Contact</a></li>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div class="right">
                        <ul class="flexitem second-links">
                            <!--<li class="mobile-hide"><a href="#">
                                <div class="icon-large"><i class="ri-heart-line"></i></div>
                                <div class="fly-item"><span class="item-number">0</span></div>
                            </a></li>-->
                            <li><a href="/cart" class="iscart" style="text-decoration:none;display:inline-flex;align-items:center" title="Panier">
                                <div class="icon-large"><i class="ri-shopping-cart-line"></i>
                                    <div class="fly-item"><span class="item-number">{{ $cartCount }}</span></div>
                                </div>
                            </a></li>
                            <li>
                                <a href="{{ route('vendeurs.ia_alertes') }}" class="ia-notif" title="Alerte" style="display:inline-flex;align-items:center; text-decoration:none">
                                    <div class="icon-large"><i class="ri-notification-2-line"></i>
                                        <div class="fly-item"><span class="item-number">{{ $counts['ia_alertes'] ?? 0 }}</span></div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="/vendeur/messages" class="mail-unread-icon" style="text-decoration:none;display:inline-flex;align-items:center" title="Messages" data-vendeur-nav>
                                    <div class="icon-large" style="position:relative;">
                                        <i class="ri-mail-unread-line"></i>
                                        @if(isset($counts['messages_unread']) && $counts['messages_unread'] > 0)
                                            <div class="fly-item"><span class="item-number">{{ $counts['messages_unread'] }}</span></div>
                                        @endif
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
           </div> 
           </div>
        </header>

        <main>
            <div class="container py-4 vendeur-dashboard">
                @if(session('error') || !empty($vendeur->Bloque))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        @if(session('error'))
                            {{ session('error') }}
                        @elseif(!empty($vendeur->Bloque))
                            <i class="fas fa-lock me-1"></i> Votre compte est limité. Certaines actions sont désactivées.
                        @endif
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                @endif
                <div class="row">
                    <aside class="col-md-3">
                        <div class="profile-card text-center">
                            <h5 class="mb-1">{{ $vendeur ? ($vendeur->Nom . ' ' . ($vendeur->Prenom ?? '')) : 'Vendeur' }}</h5>
                            <div class="text-muted small">Espace Vendeur</div>
                            <div class="profile-actions d-flex flex-column">
                                <a href="{{ route('PageVendeur') }}?view=dashboard" class="btn btn-sm btn-outline-secondary mb-2" data-vendeur-nav><i class="fa-solid fa-chart-line me-2" ></i> Tableau de Bord</a>
                                <a href="/vendeur/produits" class="btn btn-sm btn-outline-secondary mb-2" data-vendeur-nav><i class="fa-solid fa-box me-2"></i> Produits</a>
                                <a href="/vendeur/commandes" class="btn btn-sm btn-outline-secondary mb-2" data-vendeur-nav><i class="fa-solid fa-cart-shopping me-2"></i> Commandes</a>
                                <a href="/vendeur/clients" class="btn btn-sm btn-outline-secondary mb-2" data-vendeur-nav><i class="fa-solid fa-users me-2"></i> Clients</a>
                                <a href="/vendeur/analyses" class="btn btn-sm btn-outline-secondary mb-2" data-vendeur-nav><i class="fa-solid fa-chart-pie me-2"></i> Analyses</a>
                                <a href="/vendeur/messages" class="btn btn-sm btn-outline-secondary mb-2" data-vendeur-nav><i class="fa-solid fa-envelope me-2"></i> Messages</a>
                                <a href="/vendeur/parametres" class="btn btn-sm btn-outline-secondary" data-vendeur-nav><i class="fa-solid fa-gear me-2"></i> Paramètres</a>
                            </div>    
                            <div class="mt-3 d-flex justify-content-center">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Déconnexion</button>
                                </form>
                            </div>
                        </div>
                    </aside>

                    <section class="col-md-9 main-content" id="main-content">
                        <div id="partial-header"></div>
                        <div id="partial-body">
                            @if(isset($partial))
                                @include($partial)
                            @else
                                @include('vendeurs.dashboard')
                            @endif
                        </div>
                    </section>
                </div>
            </div>
        </main>

        <footer></footer>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
    <script>
        (function(){
            // Mobile sidebar toggle
            const trigger = document.querySelector('.trigger');
            const aside = document.querySelector('aside');
            if(trigger && aside){
                trigger.addEventListener('click', function(e){
                    e.preventDefault();
                    aside.classList.toggle('show');
                });
            }

            const navLinks = document.querySelectorAll('[data-vendeur-nav]');
            const contentEl = document.getElementById('partial-body');

            function setActive(link){
                navLinks.forEach(l => l.classList.remove('active'));
                if(link) link.classList.add('active');
            }

            async function loadUrl(url, link, addHistory = true){
                try{
                    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'include' });
                    if(!res.ok){
                        contentEl.innerHTML = '<div class="alert alert-danger">Erreur de chargement</div>';
                        return;
                    }
                    const html = await res.text();
                    contentEl.innerHTML = html;
                    // Execute scripts in the loaded content
                    const scripts = contentEl.querySelectorAll('script');
                    scripts.forEach(script => {
                        if (!script.src) {
                            // Inline script
                            try {
                                eval(script.textContent);
                            } catch (e) {
                                console.error('Error executing script:', e);
                            }
                        } else {
                            // External script, load it
                            const newScript = document.createElement('script');
                            newScript.src = script.src;
                            document.head.appendChild(newScript);
                        }
                    });
                    setActive(link);
                    if(addHistory) history.pushState({ url: url }, '', url);
                    contentEl.scrollIntoView({ behavior: 'smooth' });
                    // Initialize any widgets inside the loaded partial
                    initPartials();
                }catch(err){
                    console.error(err);
                    contentEl.innerHTML = '<div class="alert alert-danger">Erreur de chargement</div>';
                }
            }

            document.addEventListener('click', function(e){
                const link = e.target.closest('[data-vendeur-nav]');
                if(link){
                    const href = link.getAttribute('href');
                    const sameOrigin = href && (href.startsWith('/') || href.startsWith(window.location.origin));
                    if(sameOrigin){
                        e.preventDefault();
                        loadUrl(href, link);
                    }
                }
            });

            window.addEventListener('popstate', function(e){
                const url = window.location.href;
                const match = Array.from(navLinks).find(l => l.href === url || l.getAttribute('href') === (new URL(url)).pathname + (new URL(url)).search);
                loadUrl(url, match || null, false);
            });

            // set initial active link based on current URL
            const current = window.location.pathname + window.location.search;
            const initial = Array.from(navLinks).find(l => l.getAttribute('href') === current || l.href === window.location.href);
            if(initial) setActive(initial);

            // Set initial history state for back button support
            history.replaceState({ url: window.location.href }, '', window.location.href);

            // Expose loadUrl as global function for partials
            window.vendeurFetchAndInject = function(url, addHistory = true) {
                // when called from partials (e.g. "Voir"), default to adding a history entry
                loadUrl(url, null, addHistory);
            };

            // Initialize partial-specific widgets (e.g., parametres form)
            function initPartials(){
                // Paramètres form: read-only by default with Modifier/Annuler toggle
                const form = document.getElementById('formParametres');
                if(!form || form.dataset.inited) return; form.dataset.inited = '1';

                // include all input fields except hidden inputs
                const fields = Array.from(form.querySelectorAll('input, textarea')).filter(i => i.type !== 'hidden');
                const btnEdit = document.getElementById('btnEditParam');
                const btnSave = document.getElementById('btnSaveParam');
                const statusEl = document.getElementById('paramStatus');

                // Store initial values to allow Cancel
                const initial = fields.map(f => f.value);

                function setReadOnly(state){
                    fields.forEach((f, i) => {
                        f.readOnly = state;
                        f.classList.toggle('form-control-plaintext', state);
                        f.classList.toggle('form-control', !state);
                    });
                    if(btnSave) btnSave.disabled = state;
                    if(btnEdit) { btnEdit.textContent = state ? 'Modifier' : 'Annuler'; btnEdit.classList.toggle('btn-outline-danger', !state); }
                }

                // Initialize read-only state
                setReadOnly(true);

                btnEdit?.addEventListener('click', function(){
                    const editing = btnSave ? btnSave.disabled : true; // if save disabled -> not editing
                    if(editing){
                        // enter edit mode
                        setReadOnly(false);
                    } else {
                        // cancel: restore values
                        fields.forEach((f, i) => f.value = initial[i]);
                        setReadOnly(true);
                    }
                });

                // On successful save, update initial values and return to read-only
                form.addEventListener('saved', function(ev){
                    const newVals = ev.detail || {};
                    fields.forEach((f, i) => { initial[i] = f.value; });
                    if(statusEl){ statusEl.innerHTML = '<div class="alert alert-success">Paramètres enregistrés</div>'; }
                    setReadOnly(true);
                    setTimeout(()=>{ if(statusEl) statusEl.innerHTML = ''; }, 2500);
                });

                // Wire submit to trigger 'saved' event on success (existing fetch handler will still run)
                form.addEventListener('submit', async function(e){
                    e.preventDefault();
                    const data = new FormData(form);
                    try{
                        const res = await fetch(form.action, { method: 'POST', headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN }, body: data, credentials: 'same-origin' });
                        if(res.ok){
                            const detail = await res.json().catch(()=>({}));
                            form.dispatchEvent(new CustomEvent('saved', { detail }));
                        } else {
                            const j = await res.json().catch(()=>({}));
                            alert(j.message || 'Erreur');
                        }
                    }catch(e){ alert('Erreur de requête'); }
                });
            }

            // Run initializers for the content already on the page
            initPartials();
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
    <!-- Scripts déplacés depuis vendeurs/produits.blade.php -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Use event delegation for promotion buttons to work with dynamically loaded content
        document.addEventListener('click', function(e) {
            // Handle "Mettre en Promotion" button click
            const promotionBtn = e.target.closest('#promotionBtn');
            if (promotionBtn) {
                e.preventDefault();
                const checked = Array.from(document.querySelectorAll('.promotion-checkbox:checked')).map(cb => cb.value);
                if(checked.length === 0){
                    alert('Veuillez sélectionner au moins un produit à mettre en promotion.');
                    return;
                }
                // Afficher la modal personnalisée
                const modal = document.getElementById('promotionModal');
                const input = document.getElementById('promotionReductionInput');
                const dureeInput = document.getElementById('promotionDureeInput');
                const form = document.getElementById('promotionForm');
                const cancelBtn = document.getElementById('promotionModalCancel');
                modal.style.display = 'flex';
                modal.style.position = 'fixed';
                modal.style.top = '0';
                modal.style.left = '0';
                modal.style.width = '100vw';
                modal.style.height = '100vh';
                modal.style.background = 'rgba(0,0,0,0.35)';
                modal.style.zIndex = '3000';
                modal.setAttribute('aria-hidden','false');
                input.value = 10;
                dureeInput.value = 7;
                setTimeout(()=>{ input.focus(); }, 150);
                // Gestion propre de la fermeture et soumission du formulaire
                const closeModal = () => {
                    modal.style.display = 'none';
                    modal.setAttribute('aria-hidden','true');
                    form.removeEventListener('submit', submitHandler);
                    cancelBtn.removeEventListener('click', cancelHandler);
                };
                function submitHandler(event){
                    event.preventDefault();
                    let reduction = parseInt(input.value, 10);
                    let duree = parseInt(dureeInput.value, 10);
                    if(isNaN(reduction) || reduction < 1 || reduction > 100){
                        alert('Veuillez entrer une valeur entre 1 et 100 pour la réduction.');
                        return;
                    }
                    if(isNaN(duree) || duree < 1 || duree > 365){
                        alert('Veuillez entrer une durée valide (1 à 365 jours).');
                        return;
                    }
                    fetch('/vendeur/produits/promotion', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ produits: checked, reduction: reduction, duree: duree })
                    }).then(r => r.json()).then(data => {
                        closeModal();
                        if(data.success){
                            alert('Produits mis en promotion avec succès !');
                            window.location.reload();
                        }else{
                            alert(data.message || 'Erreur lors de la mise en promotion.');
                        }
                    });
                }
                function cancelHandler(){ closeModal(); }
                form.addEventListener('submit', submitHandler);
                cancelBtn.addEventListener('click', cancelHandler);
                return;
            }

            // Handle "Retirer la Promotion" button click
            const removePromotionBtn = e.target.closest('#removePromotionBtn');
            if (removePromotionBtn) {
                e.preventDefault();
                const checked = Array.from(document.querySelectorAll('.promotion-checkbox:checked')).map(cb => cb.value);
                if(checked.length === 0){
                    alert('Veuillez sélectionner au moins un produit à retirer de la promotion.');
                    return;
                }
                fetch('/vendeur/produits/promotion/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ produits: checked })
                }).then(r => r.json()).then(data => {
                    if(data.success){
                        alert('Promotion retirée avec succès !');
                        window.location.reload();
                    }else{
                        alert(data.message || 'Erreur lors du retrait de la promotion.');
                    }
                });
                return;
            }
        });

        // Filtrage automatique : déclenche la recherche à chaque changement de champ
        const filter = document.getElementById('filterForm');
        if(!filter) return;
        const autoFilter = async function(){
            const params = new URLSearchParams(new FormData(filter));
            const fetchUrl = filter.action + (params.toString() ? ('?' + params.toString() + '&partial=1') : '?partial=1');
            try{
                const res = await fetch(fetchUrl, { headers: {'X-Requested-With': 'XMLHttpRequest'}, credentials: 'same-origin' });
                if(!res.ok){ filter.submit(); return; }
                const html = await res.text();
                const tmp = document.createElement('div'); tmp.innerHTML = html;
                const inner = tmp.querySelector('#product-list') || tmp;
                const container = document.getElementById('product-list');
                if(container && inner){
                    container.innerHTML = inner.innerHTML;
                } else {
                    filter.submit();
                    return;
                }
                // update visible URL without partial flag
                const publicUrl = filter.action + (params.toString() ? ('?' + params.toString()) : '');
                history.replaceState(null,'', publicUrl);
            }catch(err){
                filter.submit();
            }
        };
        filter.querySelectorAll('input,select').forEach(el => {
            el.addEventListener('change', autoFilter);
            el.addEventListener('input', autoFilter);
        });
    });
    </script>
    <script>
    // Filtrage automatique : déclenche la recherche à chaque changement de champ
    (function(){
        const filter = document.getElementById('filterForm');
        if(!filter) return;
        const autoFilter = async function(){
            const params = new URLSearchParams(new FormData(filter));
            const fetchUrl = filter.action + (params.toString() ? ('?' + params.toString() + '&partial=1') : '?partial=1');
            try{
                const res = await fetch(fetchUrl, { headers: {'X-Requested-With': 'XMLHttpRequest'}, credentials: 'same-origin' });
                if(!res.ok){ filter.submit(); return; }
                const html = await res.text();
                const tmp = document.createElement('div'); tmp.innerHTML = html;
                const inner = tmp.querySelector('#product-list') || tmp;
                const container = document.getElementById('product-list');
                if(container && inner){
                    container.innerHTML = inner.innerHTML;
                } else {
                    filter.submit();
                    return;
                }
                // update visible URL without partial flag
                const publicUrl = filter.action + (params.toString() ? ('?' + params.toString()) : '');
                history.replaceState(null,'', publicUrl);
            }catch(err){
                filter.submit();
            }
        };
        // Sur chaque changement d'un champ du formulaire, lancer le filtrage
        filter.querySelectorAll('input,select').forEach(el => {
            el.addEventListener('change', autoFilter);
            el.addEventListener('input', autoFilter);
        });
    })();

    // product link interception to fetch details via AJAX when available
    document.getElementById('product-list')?.addEventListener('click', function(e){
        const a = e.target.closest && e.target.closest('a.produit-link'); if(!a) return; e.preventDefault();
        const url = a.href;
        fetch(url, { headers: {'X-Requested-With': 'XMLHttpRequest'}, credentials: 'same-origin' })
            .then(r => {
                if(r.redirected || /login|connexion/i.test(r.url) || r.status === 401 || r.status === 403){ window.location.href = r.url; throw 'auth'; }
                if(!r.ok){ window.location.href = url; throw 'nav'; }
                return r.text();
            })
            .then(html => {
                // Replace main content if PageVendeur expects it
                try{ if(typeof replaceMainContent === 'function'){ replaceMainContent(html); return; } }catch(e){}
                const main = document.querySelector('#main-content'); if(main){ main.innerHTML = html; history.pushState(null,'', url); }
            }).catch(()=>{});
    });

    // modal controls (délégation d'événements pour que ça marche après chargement AJAX du partial Produits)
    document.addEventListener('click', function(e){
        const openAdd = e.target.id === 'openAddBtn' || e.target.closest('#openAddBtn');
        const fabAdd = e.target.id === 'fabAdd' || e.target.closest('#fabAdd');
        const closeAdd = e.target.id === 'closeAdd' || e.target.closest('#closeAdd');
        const modal = document.getElementById('addModal');
        if ((openAdd || fabAdd) && modal) {
            e.preventDefault();
            // Réinitialiser le formulaire à chaque ouverture du modal
            const formProduit = document.getElementById('formProduit');
            if(formProduit) {
                formProduit.reset();
                const fileInput = formProduit.querySelector('input[type="file"]');
                if(fileInput) fileInput.value = '';
            }
            modal.style.display = 'flex';
            modal.setAttribute('aria-hidden', 'false');
        }
        if (closeAdd && modal) {
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
        }
        // Fermer en cliquant sur le fond (backdrop)
        if (e.target.id === 'addModal' && modal) {
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
        }
    });

    // Intercepter l'envoi du formulaire d'ajout pour faire un POST AJAX (délégation pour contenu chargé en AJAX)
    document.addEventListener('submit', async function(e){
        if (e.target.id !== 'formProduit') return;
        e.preventDefault();
        const formProduit = e.target;
        const submitBtn = formProduit.querySelector('button[type="submit"]');
        if(submitBtn) submitBtn.disabled = true;
        try{
            const data = new FormData(formProduit);
            const res = await fetch(formProduit.action, {
                method: 'POST',
                body: data,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            if(!res.ok){
                const j = await res.json().catch(()=>null);
                alert(j?.message || 'Erreur lors de l\u2019ajout du produit');
                return;
            }
            const json = await res.json().catch(()=>null);
            if(json && json.success){
                // Rafraîchir la liste des produits via l'endpoint partiel
                try{
                    const fetchUrl = '/vendeur/produits?partial=1';
                    const resp = await fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
                    if(resp.ok){
                        const html = await resp.text();
                        const tmp = document.createElement('div'); tmp.innerHTML = html;
                        const inner = tmp.querySelector('#product-list') || tmp;
                        const container = document.getElementById('product-list');
                        if(container && inner) container.innerHTML = inner.innerHTML;
                    }
                }catch(err){ console.error('refresh error', err); }

                // Réinitialiser le formulaire
                formProduit.reset();
                // Réinitialiser aussi le champ fichier (reset() ne le fait pas toujours)
                const fileInput = formProduit.querySelector('input[type="file"]');
                if(fileInput) fileInput.value = '';

                // fermer la modal
                const modal = document.getElementById('addModal');
                if(modal){ modal.style.display = 'none'; modal.setAttribute('aria-hidden','true'); }
            } else {
                alert(json?.message || 'Réponse inattendue');
            }
        }catch(err){
            console.error('submit error', err);
            alert('Erreur réseau lors de l\u2019ajout');
        } finally {
            if(submitBtn) submitBtn.disabled = false;
        }
    });

    // Product detail view handler
    (function(){
        // history stack: each entry { html, scroll }
        let savedStack = [];
        function renderDetail(data){
            // parse similar products if provided as JSON string
            let similar = [];
            try{ if(data.similar) similar = JSON.parse(data.similar); }catch(e){ similar = []; }
            const similarHtml = similar.length ? `<div style="margin-top:12px">
                    <h5>Produits similaires</h5>
                    <div class="product-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:18px;">${similar.map(s => `
                        <div class="product-card card">
                            <div class="position-relative">
                                <img src="${s.img}" class="card-img-top" alt="${s.name}">
                                <button class="add-to-cart" title="Ajouter au panier" data-id="${s.id}" aria-label="Ajouter ${s.name} au panier">
                                    <i class="fa fa-cart-plus"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <h6 class="product-title">
                                    <button type="button" class="product-open btn-link" data-id="${s.id}" data-name="${s.name.replace(/\"/g,'')}" data-desc="" data-price="${s.price}" data-img="${s.img}" data-vendor-name="${data.vendorName||''}" data-vendor-address="${data.vendorAddress||''}" data-stock="" data-category="" data-similar="">${s.name}</button>
                                </h6>
                                <p class="product-meta mb-2">${s.name.substring(0,60)}...</p>
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <div class="product-price">${s.price}</div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary product-open" data-id="${s.id}" data-name="${s.name.replace(/\"/g,'')}" data-desc="" data-price="${s.price}" data-img="${s.img}" data-vendor-name="${data.vendorName||''}" data-vendor-address="${data.vendorAddress||''}" data-stock="" data-category="" data-similar="">Voir</button>
                                </div>
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
            const container = document.getElementById('product-list');
            if(!container) return;
            // push current view onto stack so we can return to it
            savedStack.push({ html: container.innerHTML, scroll: window.scrollY || window.pageYOffset || 0 });
            container.innerHTML = html;
            // push history state so refresh/back behavior is preserved
            try{ history.pushState({ produitId: data.id || null }, '', data.id ? ('?produit=' + encodeURIComponent(data.id)) : window.location.pathname); }catch(e){}
        }
        function restoreMain(){
            const container = document.getElementById('product-list');
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
            // Ne pas intercepter le clic sur la checkbox de sélection
            if (e.target.classList.contains('promotion-checkbox')) return;
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
                    const container = document.getElementById('product-list');
                    if(!container){ renderDetail(data); return; }
                    // push current view so closing the fragment returns here
                    savedStack.push({ html: container.innerHTML, scroll: window.scrollY || window.pageYOffset || 0 });
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                        .then(resp => {
                            if(resp.redirected || /login|connexion/i.test(resp.url) || resp.status === 401 || resp.status === 403){ window.location.href = resp.url; throw 'auth'; }
                            if(!resp.ok) throw new Error('non-ok');
                            return resp.text();
                        })
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
        // handle browser back/forward navigation to restore list/detail state
        window.addEventListener('popstate', function(event){
            try{
                // If we have a savedStack entry, the previous view is the list we saved — restore it immediately.
                if(Array.isArray(savedStack) && savedStack.length){
                    restoreMain();
                    return;
                }
                const state = event.state;
                if(!state || !state.produitId){
                    // no produit in history state -> restore product list view
                    if(typeof restoreMain === 'function'){
                        restoreMain();
                    } else {
                        window.location.reload();
                    }
                    return;
                }
                // state contains produitId: keep detail visible (or implement loading if needed)
            }catch(err){ console.error('popstate handler error', err); }
        });
    })();
    </script>
    <!-- Modal de réduction pour la promotion déplacé ici pour être global -->
    <div id="promotionModal" tabindex="-1" aria-hidden="true" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.35);z-index:3000;align-items:center;justify-content:center;">
        <div class="modal-dialog" style="max-width:400px;width:90vw;background:#fff;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,0.25);padding:24px;display:flex;flex-direction:column;align-items:center;position:relative;z-index:4000;pointer-events:auto;">
            <form id="promotionForm" style="width:100%;display:flex;flex-direction:column;align-items:center;">
                <h4 class="mb-3" style="font-weight:600;">Paramètres de la promotion</h4>
                <div class="mb-3 w-100" style="max-width:220px;">
                    <label for="promotionReductionInput" class="form-label">Réduction (%)</label>
                    <input type="number" id="promotionReductionInput" min="1" max="100" value="10" class="form-control" required>
                </div>
                <div class="mb-3 w-100" style="max-width:220px;">
                    <label for="promotionDureeInput" class="form-label">Durée de la promotion (en jours)</label>
                    <input type="number" id="promotionDureeInput" min="1" max="365" value="7" class="form-control" required>
                </div>
                <div class="d-flex gap-2 mt-2 justify-content-center w-100">
                    <button type="submit" id="promotionModalOk" class="btn btn-primary">Valider</button>
                    <button type="button" id="promotionModalCancel" class="btn btn-secondary">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
