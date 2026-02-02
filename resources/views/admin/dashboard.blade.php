<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
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
        
        
        <!-- Deconnexion -->
        <li>
            <!--<form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" style="background:none;border:none;padding:0;color:inherit;cursor:pointer">
                    <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
                </button>
            </form>-->
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
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(resp => resp.text())
                    .then(html => {
                        main.innerHTML = html;
                    });
            });
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
            updateActiveFromLocation();
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

</div>


    
</body>
</html>