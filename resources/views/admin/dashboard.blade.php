<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <link rel="stylesheet" href="{{ asset('css/StyleAdmin.css') }}">
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

</div>


    </main>

        

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
        vendeurs: '{{ route('admin.vendeurs') }}'
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
                    const ns = document.createElement('script');
                    if(s.src){
                        try{ ns.src = new URL(s.src, url).href; } catch(e){ ns.src = s.src; }
                        ns.async = false;
                    } else {
                        ns.textContent = s.textContent;
                    }
                    document.body.appendChild(ns);
                });
            }catch(e){ /* ignore script execution errors */ }
            // Call global initializer so partials can wire delegated handlers
            try{ if(window.adminInitPartials) window.adminInitPartials(); }catch(e){ console.warn('adminInitPartials failed', e); }
        }catch(e){ main.innerHTML = '<div class="card"><p>Erreur réseau lors du chargement.</p></div>'; }
    }
    sidebar.addEventListener('click', async function(e){
        const a = e.target.closest('a');
        if(!a) return;
        const viewKey = a.getAttribute('data-view') || a.dataset.view || null;
        if(!viewKey) return;
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
        const res = await fetch(url, Object.assign({headers:{'X-Requested-With':'XMLHttpRequest'}}, opts || {}));
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
                    const ns = document.createElement('script');
                    if(s.src){
                        try{ ns.src = new URL(s.src, url).href; } catch(e){ ns.src = s.src; }
                        ns.async = false;
                    } else {
                        ns.textContent = s.textContent;
                    }
                    document.body.appendChild(ns);
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

    window.__admin_partials_initialized = true;
};
// Ensure partials initializer runs at least once on initial page load
try{ if(window.adminInitPartials) window.adminInitPartials(); }catch(e){ console.warn('adminInitPartials init failed', e); }
</script>

</div>


    
</body>
</html>