<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord Vendeur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/StylePageVendeur.css') }}">
</head>
<body>
<div class="container">
    <aside class="sidebar">
        <img src="{{ asset('Logo-site.png') }}" width="200" alt="Logo de la plateforme" title="LOGO" class="logo">
        <ul>
            <li data-view="dashboard"><a href="{{ route('PageVendeur') }}" data-view="dashboard"><i class="fa-solid fa-chart-line"></i> Tableau de Bord</a></li>
            <li data-view="produits"><a href="/vendeur/produits" data-view="produits"><i class="fa-solid fa-box"></i> Produits</a></li>
            <li data-view="commandes"><a href="/vendeur/commandes" data-view="commandes"><i class="fa-solid fa-cart-shopping"></i> Commandes</a></li>
            <li data-view="clients"><a href="/vendeur/clients" data-view="clients"><i class="fa-solid fa-users"></i> Clients</a></li>
            <li data-view="analyses"><a href="/vendeur/analyses" data-view="analyses"><i class="fa-solid fa-chart-pie"></i> Analyses</a></li>
            <li data-view="messages"><a href="/vendeur/messages" data-view="messages"><i class="fa-solid fa-envelope"></i> Messages</a></li>
            <li data-view="parametres"><a href="/vendeur/parametres" data-view="parametres"><i class="fa-solid fa-gear"></i> Paramètres</a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" style="background:none;border:none;padding:0;color:inherit;cursor:pointer">
                        <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
                    </button>
                </form>
            </li>
        </ul>
    </aside>
    <main class="main-content" id="main-content">
        @if(isset($partial))
            @include($partial)
        @else
            @include('vendeurs.dashboard')
        @endif
    </main>
</div>
<script>
function replaceMainContent(html) {
    const oldMain = document.getElementById('main-content');
    if (!oldMain) return;
    const container = oldMain.parentNode;

    // Parse HTML in a temp container so we can handle scripts
    const tmp = document.createElement('div');
    tmp.innerHTML = html;

    // Extract scripts
    const scripts = Array.from(tmp.querySelectorAll('script'));
    scripts.forEach(s => s.parentNode && s.parentNode.removeChild(s));

    // Crée un nouveau main identique et injecte le HTML sans scripts
    const newMain = document.createElement('main');
    newMain.className = 'main-content';
    newMain.id = 'main-content';
    newMain.innerHTML = tmp.innerHTML;
    container.replaceChild(newMain, oldMain);
    newMain.scrollTop = 0;

    // Re-exécute les scripts (inline and external) in order
    scripts.reduce((prev, script) => {
        return prev.then(() => {
            return new Promise((resolve, reject) => {
                const s = document.createElement('script');
                if (script.src) {
                    s.src = script.src;
                    s.onload = resolve;
                    s.onerror = resolve; // don't block on error
                    document.body.appendChild(s);
                } else {
                    try {
                        s.text = script.textContent;
                        document.body.appendChild(s);
                    } catch (e) {
                        console.error('Error executing inline script', e);
                    }
                    resolve();
                }
            });
        });
    }, Promise.resolve());
}
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.sidebar a[data-view]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(resp => resp.text())
                .then(html => {
                    replaceMainContent(html);
                    history.pushState(null, '', url);
                });
        });
    });

    // Global interception for product links (left-click only) so SPA handles product detail navigation
    document.addEventListener('click', function(e){
        // Only handle left mouse button without modifier keys; allow ctrl/cmd/middle-click to open new tabs
        if (e.button !== 0 || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
        const anchor = e.target.closest && e.target.closest('a.produit-link');
        if(!anchor) return;
        e.preventDefault();
        const url = anchor.getAttribute('href');
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(resp => {
                if(!resp.ok){ window.location.href = url; throw new Error('nav'); }
                return resp.text();
            })
            .then(html => {
                replaceMainContent(html);
                history.pushState(null, '', url);
            }).catch(()=>{});
    });

    window.addEventListener('popstate', function() {
        fetch(location.pathname, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(resp => resp.text())
            .then(html => {
                replaceMainContent(html);
            });
    });

    // Intercept common reload keys (F5, Ctrl/Cmd+R) to refresh current SPA view (products list or product detail)
    document.addEventListener('keydown', function(e){
        const path = window.location.pathname;
        const isF5 = e.key === 'F5';
        const isCtrlR = (e.ctrlKey || e.metaKey) && (e.key === 'r' || e.key === 'R');
        if(!(isF5 || isCtrlR)) return;
        // only handle when we're on products list or a product detail
        if(!(path === '/produits' || path.endsWith('/produits') || path.match(/^\/produits\/.+$/))) return;
        e.preventDefault();
        const fetchUrl = path + (window.location.search ? (window.location.search + '&partial=1') : '?partial=1');
        // show a small loader indicator if present
        (async function(){
            try{
                const res = await fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if(!res.ok){ window.location.href = path + window.location.search; return; }
                const html = await res.text();
                replaceMainContent(html);
                // keep URL unchanged (without partial)
                history.replaceState(null, '', path + window.location.search);
            }catch(err){
                window.location.href = path + window.location.search;
            }
        })();
    });

    // If the navigation was a reload, trigger an AJAX refresh so the SPA updates in-place
    try{
        const nav = performance.getEntriesByType('navigation')[0];
        const isReload = nav ? nav.type === 'reload' : (performance.navigation && performance.navigation.type === 1);
        if(isReload){
            const path = window.location.pathname;
            if(path === '/produits' || path.match(/^\/produits(\/.*)?$/)){
                setTimeout(function(){
                    const fetchUrl = path + (window.location.search ? (window.location.search + '&partial=1') : '?partial=1');
                    fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(r => { if(!r.ok) throw new Error('nav'); return r.text(); })
                        .then(html => { replaceMainContent(html); history.replaceState(null, '', path + window.location.search); })
                        .catch(()=>{});
                }, 0);
            }
        }
    }catch(e){ /* ignore */ }
});
</script>
</body>
</html>