<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Tableau de Bord Client</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/StylePageClient.css') }}">
</head>
<body>
<div class="container">
    <aside class="sidebar">
        <img src="{{ asset('Logo-site.png') }}" width="200" alt="Logo" class="logo">
        <ul>
            <li data-view="dashboard"><a href="{{ route('PageClient') }}" data-view="dashboard"><i class="fa-solid fa-user"></i> Mon Profil</a></li>
            <li data-view="commandes"><a href="/commandes" data-view="commandes"><i class="fa-solid fa-cart-shopping"></i> Mes Commandes</a></li>
            <li data-view="messages"><a href="/messages" data-view="messages"><i class="fa-solid fa-envelope"></i> Messages</a></li>
            <li data-view="parametres"><a href="/parametres" data-view="parametres"><i class="fa-solid fa-gear"></i> Paramètres</a></li>
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
            @include('clients.profile')
        @endif
    </main>
</div>

<script>
function replaceMainContent(html) {
    console.debug('replaceMainContent called — content length:', html ? html.length : 0);
    const oldMain = document.getElementById('main-content');
    if (!oldMain) return;
    const container = oldMain.parentNode;

    const tmp = document.createElement('div');
    tmp.innerHTML = html;

    // Debug: detect whether response looks like a partial or a full page
    console.debug('replaceMainContent: contains .main-content? ', !!tmp.querySelector('.main-content'), 'contains header.header? ', !!tmp.querySelector('header.header'));

    const scripts = Array.from(tmp.querySelectorAll('script'));
    scripts.forEach(s => s.parentNode && s.parentNode.removeChild(s));

    const newMain = document.createElement('main');
    newMain.className = 'main-content';
    newMain.id = 'main-content';
    newMain.innerHTML = tmp.innerHTML;
    container.replaceChild(newMain, oldMain);
    newMain.scrollTop = 0;

    scripts.reduce((prev, script) => {
        return prev.then(() => {
            return new Promise((resolve) => {
                const s = document.createElement('script');
                if (script.src) {
                    s.src = script.src;
                    s.onload = resolve;
                    s.onerror = resolve;
                    document.body.appendChild(s);
                } else {
                    try { s.text = script.textContent; document.body.appendChild(s); } catch(e){}
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
            console.debug('SPA nav (client) fetch ->', url);
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(resp => resp.text())
                .then(html => {
                    console.debug('SPA nav (client) fetched', url, 'len:', html.length);
                    replaceMainContent(html);
                    history.pushState(null, '', url);
                });
        });
    });

    window.addEventListener('popstate', function() {
        fetch(location.pathname, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(resp => resp.text())
            .then(html => {
                replaceMainContent(html);
            });
    });

    // Intercept reload keys on client pages to do AJAX refresh
    document.addEventListener('keydown', function(e){
        const path = window.location.pathname;
        const isF5 = e.key === 'F5';
        const isCtrlR = (e.ctrlKey || e.metaKey) && (e.key === 'r' || e.key === 'R');
        if(!(isF5 || isCtrlR)) return;
        if(!(path === '/PageClient' || path === '/commandes' || path.match(/^\/commandes\/.+$/))) return;
        e.preventDefault();
        const fetchUrl = path + (window.location.search ? (window.location.search + '&partial=1') : '?partial=1');
        (async function(){
            try{
                const res = await fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if(!res.ok){ window.location.href = path + window.location.search; return; }
                const html = await res.text();
                replaceMainContent(html);
                history.replaceState(null, '', path + window.location.search);
            }catch(err){ window.location.href = path + window.location.search; }
        })();
    });

    // On reload navigation, refresh partial if needed
    try{
        const nav = performance.getEntriesByType('navigation')[0];
        const isReload = nav ? nav.type === 'reload' : (performance.navigation && performance.navigation.type === 1);
        if(isReload){
            const path = window.location.pathname;
            if(path === '/PageClient' || path === '/commandes' || path.match(/^\/commandes(\/.*)?$/)){
                setTimeout(function(){
                    const fetchUrl = path + (window.location.search ? (window.location.search + '&partial=1') : '?partial=1');
                    fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(r => { if(!r.ok) throw new Error('nav'); return r.text(); })
                        .then(html => { replaceMainContent(html); history.replaceState(null, '', path + window.location.search); })
                        .catch(()=>{});
                }, 0);
            }
        }
    }catch(e){ }
});
</script>
</body>
</html>