<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel ="stylesheet" href="{{ asset('css/StylePagePrincipale.css') }}">
    <link rel ="stylesheet" href="{{ asset('css/StyleProduit.css') }}">
    <link rel="stylesheet" href="{{ asset('css/StyleVendeurProduits.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Mon Espace - Vendeur</title>
    <style>
        /* small overrides to adapt PagePrincipale style to vendeur dashboard */
        .vendeur-dashboard { padding: 24px 0; }
        .profile-card { border:1px solid #eee; padding:16px; border-radius:6px; background:#fff; }
        .profile-actions { margin-top:12px; }
        /* Active state for sidebar buttons */
        .profile-actions a.active { background:#2b7cff; color:#fff; border-color:#2b7cff; }
        .profile-actions a.active i { color:#fff; }
        .orders-list .order { border-bottom:1px dashed #efefef; padding:12px 0; }
        .recommended { margin-top:18px; }
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
                                    $vendeur = $vendeur ?? Auth::guard('vendeur')->user();
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
            <div class="container py-4 vendeur-dashboard">
                <div class="row">
                    <aside class="col-md-3">
                        <div class="profile-card text-center">
                            <h5 class="mb-1">{{ $vendeur ? ($vendeur->Nom . ' ' . ($vendeur->Prenom ?? '')) : 'Vendeur' }}</h5>
                            <div class="text-muted small">Espace Vendeur</div>
                            <div class="profile-actions d-flex flex-column">
                                <a href="{{ route('PageVendeur') }}?view=dashboard" class="btn btn-sm btn-outline-primary mb-2" data-vendeur-nav><i class="fa-solid fa-chart-line me-2"></i> Tableau de Bord</a>
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

    <script>
        (function(){
            const navLinks = document.querySelectorAll('[data-vendeur-nav]');
            const contentEl = document.getElementById('partial-body');

            function setActive(link){
                navLinks.forEach(l => l.classList.remove('active'));
                if(link) link.classList.add('active');
            }

            async function loadUrl(url, link, addHistory = true){
                try{
                    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
                    if(!res.ok){
                        contentEl.innerHTML = '<div class="alert alert-danger">Erreur de chargement</div>';
                        return;
                    }
                    const html = await res.text();
                    contentEl.innerHTML = html;
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

            navLinks.forEach(link => {
                link.addEventListener('click', function(e){
                    const href = link.getAttribute('href');
                    const sameOrigin = href && (href.startsWith('/') || href.startsWith(window.location.origin));
                    if(sameOrigin){
                        e.preventDefault();
                        loadUrl(href, link);
                    }
                });
            });

            window.addEventListener('popstate', function(e){
                const url = (e.state && e.state.url) || window.location.href;
                const match = Array.from(navLinks).find(l => l.href === url || l.getAttribute('href') === (new URL(url)).pathname + (new URL(url)).search);
                loadUrl(url, match || null, false);
            });

            // set initial active link based on current URL
            const current = window.location.pathname + window.location.search;
            const initial = Array.from(navLinks).find(l => l.getAttribute('href') === current || l.href === window.location.href);
            if(initial) setActive(initial);

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
</body>
</html>