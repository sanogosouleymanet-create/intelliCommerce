<!-- Scripts SPA déplacés après la définition de window.adminFetchAndInject -->
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ...existing code...
    (function(){
        var el = document.getElementById('header-messages');
        if(!el) return;
        el.addEventListener('click', function(ev){
            ev.preventDefault();
            var url = el.getAttribute('href') || '';
            if(window.adminFetchAndInject){
                window.adminFetchAndInject(url);
                // Update sidebar active state and hash like sidebar click does
                const sidebar = document.querySelector('.sidebar');
                if(sidebar){
                    sidebar.querySelectorAll('li').forEach(li => li.classList.remove('active'));
                    const targetLi = sidebar.querySelector('li[data-view="messages"]');
                    if(targetLi) targetLi.classList.add('active');
                    location.hash = 'messages';
                }
                return;
            }
            window.location = url;
        });
    })();
});
</script>
@endpush
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <link rel="stylesheet" href="{{ asset('css/StyleAdmin.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
</head>
<body>

    @include('admin.header')

<div class="container">
    <!-- Conteneur principal de la page -->

    <!-- SIDEBAR : navigation latérale pour accéder aux sections du vendeur -->
    <aside class="sidebar">
    <img src="Logo-Site.png" width="200" alt="Logo de la plateforme" title="LOGO" class="logo">
    <ul>
            <!-- Lien vers le tableau de bord -->
            <li data-view="dashboard" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}" data-view="dashboard"><i class="fa-solid fa-chart-line"></i> Tableau de Bord</a>
            </li>
            <!-- Lien vers la page produits admin -->
            <li data-view="produits" class="{{ request()->routeIs('admin.produits') ? 'active' : '' }}">
                <a href="{{ route('admin.produits') }}" data-view="produits"><i class="fa-solid fa-box"></i> Produits</a>
            </li>
            <!-- Lien vers les clients admin -->
            <li data-view="clients" class="{{ request()->routeIs('admin.clients') ? 'active' : '' }}">
                <a href="{{ route('admin.clients') }}" data-view="clients"><i class="fa-solid fa-users"></i> Clients</a>
            </li>
            <!-- Lien vers la gestion des vendeurs admin -->
            <li data-view="vendeurs" class="{{ request()->routeIs('admin.vendeurs') ? 'active' : '' }}">
                <a href="{{ route('admin.vendeurs') }}" data-view="vendeurs"><i class="fa-solid fa-store"></i> Vendeurs</a>
            </li>
            <!-- Lien vers la boite de réception admin -->
            <li data-view="messages" class="{{ request()->routeIs('admin.messages') ? 'active' : '' }}">
                <a href="{{ route('admin.messages') }}" data-view="messages"><i class="fa-solid fa-inbox"></i> Messages</a>
            </li>
            <!-- Lien vers les commandes admin -->
            <li data-view="commandes" class="{{ request()->routeIs('admin.commandes') ? 'active' : '' }}">
                <a href="{{ route('admin.commandes') }}" data-view="commandes"><i class="fa-solid fa-shopping-cart"></i> Commandes</a>
            </li>

            <li data-view="cart" class="{{ request()->routeIs('admin.cart') ? 'active' : '' }}">
                <a href="{{ route('admin.cart') }}" data-view="cart"><i class="fa-solid fa-cart-shopping"></i> Panier</a>
                <!-- Lien vers la page d'acceuil -->
            <li>
                <a href="{{ url('/PagePrincipale') }}"><i class="fa-solid fa-house"></i> Accueil</a>
            </li>
        
        
        <!-- Deconnexion -->
        <li>
            <form  method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="BT" ><i class="fa-solid fa-right-from-bracket"></i> Se déconnecter</button>
        </form>
        </li>   
    </ul>
</aside>
    <!-- CONTENU PRINCIPAL : zone où le contenu change selon la navigation -->
    <main class="main-content" id="main-content">
        <!-- HEADER : titre et affichage du nom du vendeur si disponible -->
        
    <div class="row">
        <div class="stat card">
            <strong>Produits</strong>
            <div style="font-size:24px;margin-top:8px">{{ $counts['produits'] }}</div>
        </div>
        <div class="stat card">
            <strong>Vendeurs</strong>
            <div style="font-size:24px;margin-top:8px">{{ $counts['vendeurs'] }}</div>
        </div>
        <div class="stat card">
            <strong>Clients</strong>
            <div style="font-size:24px;margin-top:8px">{{ $counts['clients'] }}</div>
        </div>
        <div class="stat card">
            <strong>Admins</strong>
            <div style="font-size:24px;margin-top:8px">{{ $counts['administrateurs'] }}</div>
        </div>
    </div>

    <!-- Recent Orders Section -->
    <div class="card">

        <h2>Mes Commandes Récentes</h2>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Commande</th>
                        <th>Date</th>
                        <th>Montant Total</th>
                        <th>Statut</th>
                        <th>Produits</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($commandes as $commande)
                    <tr>
                        <td>#{{ $commande->idCommande }}</td>
                        <td>{{ $commande->DateCommande ? \Carbon\Carbon::parse($commande->DateCommande)->format('d/m/Y H:i') : 'N/A' }}</td>
                        <td>{{ number_format($commande->montant_total, 2) }} FCFA</td>
                        <td>
                            <span class="status status-{{ strtolower($commande->Statut) }}">
                                {{ $commande->Statut }}
                            </span>
                        </td>
                        <td>
                            @foreach($commande->produitcommandes as $pc)
                                <div>{{ $pc->produit ? $pc->produit->Nom : 'Produit inconnu' }} (x{{ $pc->Quantite }})</div>
                            @endforeach
                        </td>
                        <td>
                            <button class="btn-view" onclick="viewCommande({{ $commande->idCommande }})">
                                <i class="fa-solid fa-eye"></i> Voir
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Aucune commande récente</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Liste complète des clients -->
    <div class="card" style="margin-top: 32px;">
        <h2>Liste complète des clients</h2>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Adresse</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                    <tr>
                        <td>{{ $client->idClient }}</td>
                        <td>{{ $client->Nom }}</td>
                        <td>{{ $client->Prenom }}</td>
                        <td>{{ $client->email }}</td>
                        <td>{{ $client->TelClient }}</td>
                        <td>{{ $client->Adresse }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Aucun client trouvé</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Liste complète des vendeurs -->
    <div class="card" style="margin-top: 32px;">
        <h2>Liste complète des vendeurs</h2>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Adresse</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendeurs as $vendeur)
                    <tr>
                        <td>{{ $vendeur->idVendeur }}</td>
                        <td>{{ $vendeur->Nom }}</td>
                        <td>{{ $vendeur->Prenom }}</td>
                        <td>{{ $vendeur->email }}</td>
                        <td>{{ $vendeur->TelVendeur }}</td>
                        <td>{{ $vendeur->Adresse }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Aucun vendeur trouvé</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Liste complète des administrateurs -->
    <div class="card" style="margin-top: 32px;">
        <h2>Liste complète des administrateurs</h2>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Adresse</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $adminUser)
                    <tr>
                        <td>{{ $adminUser->idAdministrateur }}</td>
                        <td>{{ $adminUser->Nom }}</td>
                        <td>{{ $adminUser->Prenom }}</td>
                        <td>{{ $adminUser->email }}</td>
                        <td>{{ $adminUser->TelAdministrateur }}</td>
                        <td>{{ $adminUser->Adresse }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Aucun administrateur trouvé</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div id="commandeModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Détails de la Commande</h3>
            <div id="commandeDetails"></div>
        </div>
    </div>

</div>


    </main>

    <script>
function viewCommande(id) {
    // Fetch order details via AJAX
    fetch(`/admin/commandes/${id}`)
        .then(response => response.json())
        .then(data => {
            const clientInfo = data.client ?
                `${data.client.Nom} ${data.client.Prenom}` :
                'Client inconnu';

            const clientEmail = data.client ? data.client.email : 'N/A';
            const clientPhone = data.client ? data.client.TelClient : 'N/A';
            const clientAddress = data.client ? data.client.Adresse : 'N/A';

            const dateStr = data.DateCommande ? new Date(data.DateCommande).toLocaleString('fr-FR') : 'N/A';

            const nf = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const montantFormate = (data.montant_total !== undefined && data.montant_total !== null)
                ? nf.format(Number(data.montant_total)) + ' FCFA'
                : '0,00 FCFA';

            const produitsList = data.produitcommandes.map(pc => {
                const produitName = pc.produit ? pc.produit.Nom : 'Produit inconnu';
                const prix = (pc.PrixUnitaire !== undefined && pc.PrixUnitaire !== null) ? nf.format(Number(pc.PrixUnitaire)) + ' FCFA' : '-';
                const img = (pc.produit && pc.produit.Image) ? ('<img src="' + pc.produit.Image + '" alt="' + String(produitName).replace(/"/g,'') + '" class="commande-thumb">') : '';
                return '<li class="produit-line">' + img + '<div class="produit-meta"><strong>' + produitName + '</strong><div>Quantité: ' + pc.Quantite + '</div><div>Prix: ' + prix + '</div></div></li>';
            }).join('');

            document.getElementById('commandeDetails').innerHTML = `
                <div class="commande-modal-body">
                  <dl class="commande-details">
                    <dt>ID</dt><dd>#${data.idCommande}</dd>
                    <dt>Client</dt><dd>${clientInfo}</dd>
                    <dt>Email</dt><dd>${clientEmail}</dd>
                    <dt>Téléphone</dt><dd>${clientPhone}</dd>
                    <dt>Adresse</dt><dd>${clientAddress}</dd>
                    <dt>Date</dt><dd>${dateStr}</dd>
                    <dt>Montant Total</dt><dd>${montantFormate}</dd>
                    <dt>Statut</dt><dd>${data.Statut}</dd>
                  </dl>
                  <h4>Produits</h4>
                  <ul class="modal-produits">
                    ${produitsList}
                  </ul>
                </div>
            `;
            document.getElementById('commandeModal').style.display = 'block';
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du chargement des détails');
        });
}

function closeModal() {
    document.getElementById('commandeModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('commandeModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
    </script>

    <style>
.btn-view {
    background: #28a745;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 3px;
    cursor: pointer;
}

.status {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 0.9em;
    font-weight: bold;
}

.status-en-attente { background: #fff3cd; color: #856404; }
.status-confirmée { background: #d1ecf1; color: #0c5460; }
.status-en-préparation { background: #d4edda; color: #155724; }
.status-expédiée { background: #cce5ff; color: #004085; }
.status-livrée { background: #d4edda; color: #155724; }
.status-annulée { background: #f8d7da; color: #721c24; }

.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: 5px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: black;
}

.table-container{overflow-x:auto}
.table{width:100%;border-collapse:collapse;table-layout:fixed}
.table th,.table td{padding:8px 10px;border-bottom:1px solid #eee;vertical-align:top}
.table th{font-weight:700;background:transparent;text-align:left;white-space:nowrap}
.table td{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.table td:nth-child(1){width:80px}
.table td:nth-child(2){width:160px}
.table td:nth-child(3){width:140px;text-align:right;white-space:nowrap}
.table td:nth-child(4){width:120px;text-align:center}
.table td:nth-child(5){width:260px;white-space:normal}
.table td:nth-child(6){width:110px;text-align:center}

.status{display:inline-block;padding:4px 8px;border-radius:6px;font-size:.85em}

.commande-modal-body{display:block;padding:6px 0}
.commande-details{display:grid;grid-template-columns:140px 1fr;gap:6px 12px;margin:0 0 12px 0}
.commande-details dt{font-weight:700}
.commande-details dd{margin:0}
.modal-produits{margin:8px 0 0 18px;padding-left:0}
.modal-produits li{margin-bottom:6px}

.commande-thumb{width:60px;height:60px;object-fit:cover;border-radius:4px;margin-right:8px;vertical-align:middle}
.produit-line{display:flex;align-items:flex-start;gap:10px}
.produit-meta{font-size:0.95em}
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const main = document.getElementById('main-content');
        document.querySelectorAll('.sidebar a[data-view]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                try{ window.adminFetchAndInject(url); }catch(err){ console.error(err); }
            });
        });

        // Delegate clicks inside main-content: load internal admin links via AJAX
        main.addEventListener('click', function(e){
            const a = e.target.closest && e.target.closest('a');
            if(!a) return;
            const href = a.getAttribute('href');
            if(!href) return;
            // Ignore mailto and external links
            if(href.startsWith('mailto:') || href.startsWith('http') && !href.startsWith(window.location.origin)) return;
            const sameOrigin = href.startsWith('/') || href.startsWith(window.location.origin);
            if(sameOrigin){
                e.preventDefault();
                try{ window.adminFetchAndInject(href); }catch(err){ console.error(err); main.innerHTML = '<div class="card"><p>Erreur de chargement.</p></div>'; }
            }
        });
    });
    </script>
    <script>
document.addEventListener('DOMContentLoaded', function(){
    const sidebar = document.querySelector('.sidebar');
    const main = document.querySelector('.main-content');
    if(!sidebar || !main) return;
    let skipNextHashChange = false;
    // mapping of view keys to URLs to fetch
    const viewMap = {
        dashboard: '{{ route('admin.dashboard') }}',
        produits: '{{ route('admin.produits') }}',
        clients: '{{ route('admin.clients') }}',
        messages: '{{ route('admin.messages') }}',
        vendeurs: '{{ route('admin.vendeurs') }}',
        commandes: '{{ route('admin.commandes') }}',
        cart: '{{ route('admin.cart') }}',
        parametres: '{{ route('admin.parametres') }}'
    };
    function updateActiveFromLocation(){
        const currentHash = decodeURIComponent(location.hash || '');
        const viewKey = currentHash ? (currentHash.startsWith('#') ? currentHash.slice(1) : currentHash) : 'dashboard';
        sidebar.querySelectorAll('li').forEach(li => li.classList.remove('active'));
        const targetLi = sidebar.querySelector('li[data-view="' + viewKey + '"]');
        if(targetLi) { targetLi.classList.add('active'); return; }
        const first = sidebar.querySelector('li'); if(first) first.classList.add('active');
    }
    async function loadView(viewKey){
        const url = viewMap[viewKey] || viewMap.dashboard;
        if(!url || url === '#'){
            updateActiveFromLocation();
            return;
        }
        try{
            const res = await fetch(url, {headers: {'X-Requested-With': 'XMLHttpRequest'}});
            if(!res.ok){ main.innerHTML = '<div class="card"><p>Erreur de chargement : ' + res.status + '</p></div>'; return; }
            const text = await res.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');
            // Copy stylesheet links and inline <style> from fetched document into current head
            try{
                const fetchedLinks = doc.querySelectorAll('link[rel="stylesheet"]');
                fetchedLinks.forEach(function(link){
                    const hrefAttr = link.getAttribute('href') || '';
                    try{
                        const resolved = new URL(hrefAttr, url).href;
                        const already = Array.from(document.querySelectorAll('link[rel="stylesheet"]')).some(function(l){ return l.href === resolved; });
                        if(!already){ const nl = document.createElement('link'); nl.rel = 'stylesheet'; nl.href = resolved; document.head.appendChild(nl); }
                    }catch(e){ /* ignore bad URLs */ }
                });
                const fetchedStyles = doc.querySelectorAll('style');
                fetchedStyles.forEach(function(s){ document.head.appendChild(s.cloneNode(true)); });
            }catch(e){ /* safety */ }
            const newMain = doc.querySelector('.main-content') || doc.querySelector('main');
            if(newMain) main.innerHTML = newMain.innerHTML; else { const body = doc.querySelector('body'); main.innerHTML = body ? body.innerHTML : text; }
            // Execute any scripts from the fetched document so injected views initialize correctly
            try{
                const fetchedScripts = doc.querySelectorAll('script');
                fetchedScripts.forEach(function(s){
                    try{
                        const ns = document.createElement('script');
                        if(s.src){
                            try{ ns.src = new URL(s.src, url).href; } catch(e){ ns.src = s.src; }
                            ns.async = false;
                            try{ document.body.appendChild(ns); }catch(err){ console.warn('Skipping external script append', err); }
                        } else {
                            // For inline scripts, use a text node and guard against malformed script content
                            try{
                                const txt = s.textContent || s.innerHTML || '';
                                ns.appendChild(document.createTextNode(txt));
                                document.body.appendChild(ns);
                            }catch(err){ console.warn('Skipping inline script due to parse error', err); }
                        }
                    }catch(err){ console.warn('Skipping fetched script', err); }
                });
            }catch(e){ console.warn('adminFetchAndInject: script execution failed', e); }
            // Call global initializer so partials can wire delegated handlers
            try{ if(window.adminInitPartials) window.adminInitPartials(); }catch(e){ console.warn('adminInitPartials failed', e); }
        }catch(e){ main.innerHTML = '<div class="card"><p>Erreur réseau lors du chargement.</p></div>'; }
    }
    sidebar.addEventListener('click', async function(e){
        const li = e.target.closest('li[data-view]');
        if(!li) return;
        const a = li.querySelector('a');
        if(!a) return;
        const viewKey = li.getAttribute('data-view');
        e.preventDefault();
        await loadView(viewKey);
        skipNextHashChange = true;
        location.hash = viewKey;
        updateActiveFromLocation();
    });
    window.addEventListener('hashchange', function(){
        if(skipNextHashChange){ skipNextHashChange = false; return; }
        const viewKey = decodeURIComponent(location.hash || '').replace(/^#/, '') || 'dashboard';
        loadView(viewKey);
    });
    if(location.hash){
        const initial = decodeURIComponent(location.hash).replace(/^#/, '') || 'dashboard';
        loadView(initial);
    } else {
        updateActiveFromLocation();
    }
});
</script>

<!-- Global admin helpers used by partials -->
<script>
// fetchAndInject: load a URL (AJAX) and inject into the main content area; executes scripts in the response
window.adminFetchAndInject = async function(url, opts){
    const main = document.getElementById('main-content') || document.querySelector('main');
    if(!main) { console.warn('adminFetchAndInject: main content area not found'); return; }
    try{
        console.log('adminFetchAndInject ->', url);
        const res = await fetch(url, Object.assign({headers:{'X-Requested-With':'XMLHttpRequest'}, credentials: 'same-origin'}, opts || {}));
        console.log('adminFetchAndInject status', res.status);
        const text = await res.text();
        // Parse the response and inject only the .main-content (or <main>) to avoid nesting full pages
        try{
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');
            // Copy stylesheet links and inline styles from fetched document
            try{
                const fetchedLinks = doc.querySelectorAll('link[rel="stylesheet"]');
                fetchedLinks.forEach(function(link){
                    const hrefAttr = link.getAttribute('href') || '';
                    try{
                        const resolved = new URL(hrefAttr, url).href;
                        const already = Array.from(document.querySelectorAll('link[rel="stylesheet"]')).some(function(l){ return l.href === resolved; });
                        if(!already){ const nl = document.createElement('link'); nl.rel = 'stylesheet'; nl.href = resolved; document.head.appendChild(nl); }
                    }catch(e){ /* ignore bad URLs */ }
                });
                const fetchedStyles = doc.querySelectorAll('style');
                fetchedStyles.forEach(function(s){ document.head.appendChild(s.cloneNode(true)); });
            }catch(e){ /* ignore style copying errors */ }

            const newMain = doc.querySelector('.main-content') || doc.querySelector('main');
            if(newMain) main.innerHTML = newMain.innerHTML; else { const body = doc.querySelector('body'); main.innerHTML = body ? body.innerHTML : text; }

            // Execute scripts from fetched document so injected views initialize correctly
            try{
                const fetchedScripts = doc.querySelectorAll('script');
                fetchedScripts.forEach(function(s){
                    try{
                        if(s.src){
                            const ns = document.createElement('script');
                            try{ ns.src = new URL(s.src, url).href; } catch(e){ ns.src = s.src; }
                            ns.async = false;
                            try{ document.body.appendChild(ns); }catch(err){ console.warn('Skipping external script append', err); }
                        } else {
                            // Skip inline scripts from fetched pages to avoid parse/execution errors.
                            // Partials should initialize via `window.adminInitPartials` and global helpers.
                            try{
                                const snippet = (s.textContent || s.innerHTML || '').slice(0,200);
                                if(snippet && snippet.length) console.info('Skipping inline script snippet from fetched page:', snippet);
                            }catch(e){ /* ignore */ }
                        }
                    }catch(err){ console.warn('Skipping fetched script', err); }
                });
            }catch(e){ console.warn('adminFetchAndInject: script execution failed', e); }

            // Call any global initializer that partials can rely on
            try{ if(window.adminInitPartials) window.adminInitPartials(); }catch(e){ console.warn('adminInitPartials failed', e); }
        }catch(e){
            // fallback: inject raw text
            main.innerHTML = text;
        }
    }catch(e){ console.error('adminFetchAndInject error', e); }
};

// Global checkout helper: ensures `window.doCheckout` exists for inline onclicks
window.doCheckout = window.doCheckout || function(selectedIds){
    try{
        var ids = Array.isArray(selectedIds) ? selectedIds.slice() : [];
        if(!ids.length){ ids = Array.from(document.querySelectorAll('.select-product:checked')).map(function(cb){ return cb.value; }); }
        if(!ids.length){ alert('Sélectionnez au moins un produit à commander'); return; }
        var token = (document.querySelector('meta[name="csrf-token"]')||{}).getAttribute && document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var fd = new FormData(); ids.forEach(function(id){ fd.append('selected_products[]', id); }); if(token) fd.append('_token', token);
        var headers = { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }; if(token) headers['X-CSRF-TOKEN'] = token;
        fetch('/admin/cart/place-order', { method: 'POST', headers: headers, body: fd, credentials: 'same-origin' })
            .then(function(r){ var ct = r.headers.get('content-type') || ''; if (ct.indexOf('application/json') === -1) { return r.text().then(function(text){ throw { type: 'text', text: text, status: r.status }; }); } return r.json().then(function(json){ if(!r.ok) throw { type: 'json', json: json, status: r.status }; return json; }); })
            .then(function(json){ if(!json || !json.success){ alert(json && json.message ? json.message : 'Erreur lors de la commande'); return; }
                var toast = document.createElement('div'); toast.className = 'order-toast alert alert-success'; toast.style.position = 'fixed'; toast.style.top = '20px'; toast.style.left = '50%'; toast.style.transform = 'translateX(-50%)'; toast.style.zIndex = 99999; toast.style.minWidth = '240px'; toast.style.textAlign = 'center'; toast.textContent = json.message || 'Commande passée'; document.body.appendChild(toast); setTimeout(function(){ toast.remove(); }, 3500);
                // refresh mini-cart fragment
                fetch('/admin/cart', { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                    .then(function(r){ return r.text(); })
                    .then(function(html){ try{ var tmp=document.createElement('div'); tmp.innerHTML=html; var frag=tmp.querySelector('.mini-cart-fragment'); if(frag){ var cur=document.querySelector('.mini-cart-fragment'); if(cur) cur.innerHTML=frag.innerHTML; } }catch(e){ console.error(e); } });
            }).catch(function(err){ console.error('doCheckout error', err); if(err && err.type === 'json'){ console.error('server response json:', err.json); alert((err.json && err.json.message ? err.json.message + '\n' + JSON.stringify(err.json) : 'Erreur lors de la commande')); } else if(err && err.type === 'text'){ alert('Réponse inattendue du serveur: ' + err.text); } else { alert(err.message || 'Erreur lors de la commande'); } });
    }catch(e){ console.error('doCheckout exception', e); alert('Erreur lors de la commande'); }
};

// Shortcut used by partials and inline buttons
window.adminComposeToClient = function(id){
    try{ window.__admin_prefill = { recipient_type: 'single', recipient: 'client:' + id }; }catch(e){ console.warn(e); }
    window.adminFetchAndInject('{{ route('admin.messages') }}');
};

// Initialize delegated handlers for partials (products, clients, etc.)
window.adminInitPartials = function(){
    if(window.__admin_partials_initialized) return;
    console.log('adminInitPartials -> initializing');
    function getCsrf(){ const m = document.querySelector('meta[name="csrf-token"]'); return m ? m.content : ''; }

    // delegated submit for filter forms (e.g., #filterForm)
    document.addEventListener('submit', function(e){
        const form = e.target;
        if(!form || form.id !== 'filterForm') return;
        e.preventDefault();
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();
        const url = '{{ route('admin.produits') }}' + (params ? ('?' + params) : '');
        if(window.adminFetchAndInject){ window.adminFetchAndInject(url); return; }
        fetch(window.location.pathname + '?' + params, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(resp => resp.text())
            .then(html => {
                const temp = document.createElement('div'); temp.innerHTML = html;
                const newTbody = temp.querySelector('tbody');
                const tableBody = document.getElementById('produitsBody');
                if(newTbody && tableBody) tableBody.innerHTML = newTbody.innerHTML;
            });
    });

    // Ensure direct handler is attached to form after injection (works reliably when submit originates from inside partial)
    try{
        const directForm = document.getElementById('filterForm');
        if(directForm && !directForm.__admin_filter_attached){
            directForm.addEventListener('submit', function(e){
                e.preventDefault();
                const formData = new FormData(directForm);
                const params = new URLSearchParams(formData).toString();
                const url = '{{ route('admin.produits') }}' + (params ? ('?' + params) : '');
                if(window.adminFetchAndInject){ window.adminFetchAndInject(url); return; }
                fetch(window.location.pathname + '?' + params, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(resp => resp.text())
                    .then(html => {
                        const temp = document.createElement('div'); temp.innerHTML = html;
                        const newTbody = temp.querySelector('tbody');
                        const tableBody = document.getElementById('produitsBody');
                        if(newTbody && tableBody) tableBody.innerHTML = newTbody.innerHTML;
                    });
            });
            directForm.__admin_filter_attached = true;
        }
    }catch(e){ console.warn('adminInitPartials: attach direct filter handler failed', e); }

    // delegated click for view/delete actions
    document.addEventListener('click', function(e){
        const target = e.target;
        if(!target) return;
        // Block / unblock vendeur from vendor detail partial
        try{
            const blockBtn = target.closest && target.closest('#btn-block-vendeur');
            if(blockBtn){
                e.preventDefault();
                const id = blockBtn.getAttribute('data-id');
                if(!id) return;
                const blocked = (blockBtn.getAttribute('data-blocked') === '1');
                const url = blocked
                    ? ('/admin/messages/unblock/vendeur/' + encodeURIComponent(id))
                    : ('/admin/messages/block/vendeur/' + encodeURIComponent(id));

                blockBtn.disabled = true;
                const headers = { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' };
                const token = getCsrf();
                if(token) headers['X-CSRF-TOKEN'] = token;

                fetch(url, { method: 'POST', headers: headers, credentials: 'same-origin' })
                    .then(function(r){ return r.json().catch(function(){ return {}; }); })
                    .then(function(data){
                        if(data && (data.success === true || (data.success !== false && !data.error))){
                            // Toggle UI state
                            const newBlocked = !blocked;
                            blockBtn.setAttribute('data-blocked', newBlocked ? '1' : '0');
                            blockBtn.textContent = newBlocked ? 'Débloquer' : 'Bloquer';
                            blockBtn.className = 'btn ' + (newBlocked ? 'btn-success' : 'btn-danger');

                            const alertEl = document.querySelector('.admin-vendeur-detail .alert-warning');
                            if(newBlocked){
                                if(!alertEl){
                                    const meta = document.querySelector('.admin-vendeur-meta');
                                    if(meta){
                                        const div = document.createElement('div');
                                        div.className = 'alert alert-warning py-2 mb-2';
                                        div.style.fontSize = '0.9rem';
                                        div.textContent = 'Ce compte est bloqué. Certaines actions sont limitées.';
                                        meta.insertBefore(div, meta.firstElementChild ? meta.firstElementChild.nextElementSibling : meta.firstChild);
                                    }
                                }
                            } else {
                                if(alertEl) alertEl.remove();
                            }
                        } else {
                            alert((data && (data.message || data.error)) ? (data.message || data.error) : 'Erreur lors de l’opération.');
                        }
                    })
                    .catch(function(){ alert('Erreur réseau.'); })
                    .finally(function(){ blockBtn.disabled = false; });
                return;
            }
        }catch(err){ console.warn('block vendeur handler failed', err); }
        // Block / unblock client from client detail partial
        try{
            const blockBtn = target.closest && target.closest('#btn-block-client');
            if(blockBtn){
                e.preventDefault();
                const id = blockBtn.getAttribute('data-id');
                if(!id) return;
                const blocked = (blockBtn.getAttribute('data-blocked') === '1');
                const url = blocked
                    ? ('/admin/messages/unblock/client/' + encodeURIComponent(id))
                    : ('/admin/messages/block/client/' + encodeURIComponent(id));

                blockBtn.disabled = true;
                const headers = { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' };
                const token = getCsrf();
                if(token) headers['X-CSRF-TOKEN'] = token;

                fetch(url, { method: 'POST', headers: headers, credentials: 'same-origin' })
                    .then(function(r){ return r.json().catch(function(){ return {}; }); })
                    .then(function(data){
                        if(data && (data.success === true || (data.success !== false && !data.error))){
                            // Toggle UI state
                            const newBlocked = !blocked;
                            blockBtn.setAttribute('data-blocked', newBlocked ? '1' : '0');
                            blockBtn.textContent = newBlocked ? 'Débloquer' : 'Bloquer';
                            blockBtn.className = 'btn ' + (newBlocked ? 'btn-success' : 'btn-danger');

                            const alertEl = document.querySelector('.admin-client-detail .alert-warning');
                            if(newBlocked){
                                if(!alertEl){
                                    const meta = document.querySelector('.admin-client-meta');
                                    if(meta){
                                        const div = document.createElement('div');
                                        div.className = 'alert alert-warning py-2 mb-2';
                                        div.style.fontSize = '0.9rem';
                                        div.textContent = 'Ce compte est bloqué. Certaines actions sont limitées.';
                                        meta.insertBefore(div, meta.firstElementChild ? meta.firstElementChild.nextElementSibling : meta.firstChild);
                                    }
                                }
                            } else {
                                if(alertEl) alertEl.remove();
                            }
                        } else {
                            alert((data && (data.message || data.error)) ? (data.message || data.error) : 'Erreur lors de l\'opération.');
                        }
                    })
                    .catch(function(){ alert('Erreur réseau.'); })
                    .finally(function(){ blockBtn.disabled = false; });
                return;
            }
        }catch(err){ console.warn('block client handler failed', err); }
        // View product
        if(target.classList && target.classList.contains('btn-view-produit')){
            const tr = target.closest('tr'); if(!tr) return;
            const id = tr.getAttribute('data-id'); if(!id) return;
            const adminPrefix = window.location.pathname.startsWith('/admin') ? '/admin' : '';
            const url = adminPrefix + '/produits/' + encodeURIComponent(id);
            if(window.adminFetchAndInject){ window.adminFetchAndInject(url); } else { window.location.href = url; }
            return;
        }
        // Delete product
        if(target.classList && target.classList.contains('btn-delete-produit')){
            const tr = target.closest('tr'); if(!tr) return;
            const id = tr.getAttribute('data-id'); if(!id) return;
            if(!confirm('Supprimer ce produit ?')) return;
            const adminPrefix = window.location.pathname.startsWith('/admin') ? '/admin' : '';
            const deleteUrl = adminPrefix + '/produits/' + encodeURIComponent(id) + '/delete';
            fetch(deleteUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': getCsrf(), 'X-Requested-With': 'XMLHttpRequest' } })
                .then(resp => { if(resp.ok) tr.remove(); else resp.text().then(t=>alert('Erreur lors de la suppression: '+t)); })
                .catch(()=> alert('Erreur lors de la suppression'));
            return;
        }
        // Back button handled inline via adminFetchAndInject in partials
    });
    
    // Delegated handlers for cart partials (ensure buttons work when partials are injected)
    document.addEventListener('click', function(e){
        try{
            var btn = e.target.closest && e.target.closest('#cart-close-floating, #checkout-top-btn');
            if(btn){
                e.preventDefault();
                var checked = Array.from(document.querySelectorAll('.select-product:checked')).map(function(i){ return i.value; });
                if(!checked.length){ alert('Sélectionnez au moins un produit à commander'); return; }
                var token = (document.querySelector('meta[name="csrf-token"]')||{}).getAttribute && document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                var fd = new FormData(); checked.forEach(function(id){ fd.append('selected_products[]', id); }); if(token) fd.append('_token', token);
                var headers = { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }; if(token) headers['X-CSRF-TOKEN'] = token;
                fetch('/admin/cart/place-order', { method: 'POST', headers: headers, body: fd, credentials: 'same-origin' })
                    .then(function(r){ var ct = r.headers.get('content-type') || ''; if (ct.indexOf('application/json') === -1) { return r.text().then(function(text){ throw { type: 'text', text: text, status: r.status }; }); } return r.json().then(function(json){ if(!r.ok) throw { type: 'json', json: json, status: r.status }; return json; }); })
                    .then(function(json){ if(!json || !json.success){ alert(json && json.message ? json.message : 'Erreur lors de la commande'); return; }
                        var toast = document.createElement('div'); toast.className = 'order-toast alert alert-success'; toast.style.position = 'fixed'; toast.style.top = '20px'; toast.style.left = '50%'; toast.style.transform = 'translateX(-50%)'; toast.style.zIndex = 99999; toast.style.minWidth = '240px'; toast.style.textAlign = 'center'; toast.textContent = json.message || 'Commande passée'; document.body.appendChild(toast); setTimeout(function(){ toast.remove(); }, 3500);
                        // refresh mini-cart fragment
                        fetch('/admin/cart', { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                            .then(function(r){ return r.text(); })
                            .then(function(html){ try{ var tmp=document.createElement('div'); tmp.innerHTML=html; var frag=tmp.querySelector('.mini-cart-fragment'); if(frag){ var cur=document.querySelector('.mini-cart-fragment'); if(cur) cur.innerHTML=frag.innerHTML; } }catch(e){ console.error(e); } });
                    }).catch(function(err){ console.error('checkout error', err); if(err && err.type === 'json'){ console.error('server response json:', err.json); alert((err.json && err.json.message ? err.json.message + '\n' + JSON.stringify(err.json) : 'Erreur lors de la commande')); } else if(err && err.type === 'text'){ alert('Réponse inattendue du serveur: ' + err.text); } else { alert(err.message || 'Erreur lors de la commande'); } });
            }
        }catch(e){ console.error('cart delegated click error', e); }
    });

    // Delegated submit handlers for cart forms (update/remove)
    document.addEventListener('submit', function(e){
        var form = e.target;
        if(!form) return;
        if(form.classList && (form.classList.contains('cart-update-form') || form.classList.contains('cart-remove-form'))){
            e.preventDefault();
            try{
                var url = form.getAttribute('action') || window.location.href;
                var fd = new FormData(form);
                var token = (document.querySelector('meta[name="csrf-token"]')||{}).getAttribute && document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                if(token && !fd.has('_token')) fd.append('_token', token);
                var headers = { 'X-Requested-With': 'XMLHttpRequest' };
                if(token) headers['X-CSRF-TOKEN'] = token;
                fetch(url, { method: 'POST', headers: headers, body: fd, credentials: 'same-origin' })
                    .then(function(r){ return r.json(); })
                    .then(function(json){ if(!json || !json.success){ alert(json && json.message ? json.message : 'Erreur lors de la mise à jour du panier'); return; }
                        // update header counters if helper exists
                        if(window.updateHeaderCart) updateHeaderCart(json.count || 0, json.total || 0);
                        // refresh fragment
                        fetch('/admin/cart', { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                            .then(function(r){ return r.text(); })
                            .then(function(html){ try{ var tmp=document.createElement('div'); tmp.innerHTML=html; var frag=tmp.querySelector('.mini-cart-fragment'); if(frag){ var cur=document.querySelector('.mini-cart-fragment'); if(cur) cur.innerHTML=frag.innerHTML; } }catch(e){ console.error(e); } });
                    }).catch(function(err){ console.error('cart form submit error', err); alert('Erreur réseau lors de la requête panier'); });
            }catch(err){ console.error(err); }
        }
    }, true);

    // Keep checkout button state and total updated when checkboxes change
    document.addEventListener('change', function(e){
        try{
            if(!e.target) return;
            if(e.target.classList && e.target.classList.contains('select-product') || e.target.id === 'select-all'){
                // compute total
                var selected = document.querySelectorAll('.select-product:checked');
                var total = 0;
                if(selected.length === 0){
                    // No selection => total should be zero (business requirement)
                    total = 0;
                } else {
                    selected.forEach(function(cb){ total += parseFloat(cb.getAttribute('data-subtotal')) || 0; });
                }
                var totalEl = document.getElementById('cart-total'); if(totalEl) totalEl.innerHTML = total.toLocaleString('fr-FR') + ' FCFA';
                // enable/disable checkout buttons
                var hasSelection = document.querySelectorAll('.select-product:checked').length > 0;
                var topBtn = document.getElementById('checkout-top-btn'); if(topBtn) topBtn.disabled = !hasSelection;
                var floatingBtn = document.getElementById('cart-close-floating'); if(floatingBtn) floatingBtn.disabled = !hasSelection;
            }
        }catch(e){ console.error('cart checkbox change handler', e); }
    }, true);

    // Delegated click handler to ensure select-all works even for newly injected fragments
    document.addEventListener('click', function(e){
        try{
            var sel = e.target.closest && e.target.closest('#select-all');
            if(!sel) return;
            // Toggle all product checkboxes to match select-all
            var checked = !!sel.checked;
            Array.from(document.querySelectorAll('.select-product')).forEach(function(cb){ cb.checked = checked; });
            try{ window.updateCartTotal(); window.updateCheckoutButton(); }catch(err){ console.error(err); }
        }catch(err){ console.error('select-all delegated click error', err); }
    }, true);

    // Cart selection helpers: ensure select-all and individual checkboxes are bound when partial is present
    try{
        // safe definitions if not present
        if(typeof window.updateCartTotal !== 'function'){
            window.updateCartTotal = function(){
                try{
                    var selected = document.querySelectorAll('.select-product:checked');
                    var total = 0;
                    if(selected.length === 0){
                        // When nothing is selected, show 0
                        total = 0;
                    } else { selected.forEach(function(cb){ total += parseFloat(cb.getAttribute('data-subtotal')) || 0; }); }
                    var totalEl = document.getElementById('cart-total'); if(totalEl) totalEl.innerHTML =  total.toLocaleString('fr-FR') + ' FCFA';
                }catch(e){ console.error('updateCartTotal error', e); }
            };
        }
        if(typeof window.updateCheckoutButton !== 'function'){
            window.updateCheckoutButton = function(){
                try{ var hasSelection = document.querySelectorAll('.select-product:checked').length > 0; var topBtn=document.getElementById('checkout-top-btn'); if(topBtn) topBtn.disabled = !hasSelection; var floatingBtn=document.getElementById('cart-close-floating'); if(floatingBtn) floatingBtn.disabled = !hasSelection; }catch(e){ console.error(e); }
            };
        }

        function bindCartSelection(){
            var selectAll = document.getElementById('select-all');
            if(selectAll && !selectAll.__admin_bound){
                selectAll.addEventListener('change', function(){
                    var list = document.querySelectorAll('.select-product');
                    list.forEach(function(cb){ cb.checked = selectAll.checked; });
                    try{ window.updateCartTotal(); window.updateCheckoutButton(); }catch(e){ console.error(e); }
                });
                selectAll.__admin_bound = true;
            }
            document.querySelectorAll('.select-product').forEach(function(cb){
                if(cb.__admin_bound) return; cb.__admin_bound = true;
                cb.addEventListener('change', function(){
                    try{
                        var all = Array.from(document.querySelectorAll('.select-product'));
                        if(all.length>0){ var allChecked = all.every(function(i){ return i.checked; }); var sa = document.getElementById('select-all'); if(sa) sa.checked = allChecked; }
                        window.updateCartTotal(); window.updateCheckoutButton();
                    }catch(e){ console.error(e); }
                });
            });
        }
        if(document.querySelector('.mini-cart-fragment')) bindCartSelection();
    }catch(e){ console.error('bindCartSelection failed', e); }

    window.__admin_partials_initialized = true;
};
// Ensure partials initializer runs at least once on initial page load
try{ if(window.adminInitPartials) window.adminInitPartials(); }catch(e){ console.warn('adminInitPartials init failed', e); }
</script>

</div>


    
</body>
</html>