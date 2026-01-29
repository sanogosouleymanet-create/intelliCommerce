<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Vue: PageVendeur.blade.php - Tableau de bord du vendeur -->
    <meta charset="UTF-8">
    <title>Tableau de Bord Vendeur</title>
    <!-- Charge les icônes Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Charge la feuille de style spécifique à la page vendeur -->
    <link rel="stylesheet" href="{{ asset('css/StylePageVendeur.css') }}">
</head>
<body>

<div class="container">
    <!-- Conteneur principal de la page -->

    <!-- SIDEBAR : navigation latérale pour accéder aux sections du vendeur -->
    <aside class="sidebar">
    <img src="Logo-Site.png" width="200" alt="Logo de la plateforme" title="LOGO" class="logo">
    <ul>
        <!-- Lien vers le tableau de bord -->
        <li data-view="dashboard" class="{{ request()->routeIs('PageVendeur') ? 'active' : '' }}">
            <a href="{{ route('PageVendeur') }}" data-view="dashboard"><i class="fa-solid fa-chart-line"></i> Tableau de Bord</a>
        </li>
        <!-- Lien vers la page produits du vendeur -->
        <li data-view="produits" class="{{ request()->is('produits') ? 'active' : '' }}">
            <a href="{{ url('/produits') }}" data-view="produits"><i class="fa-solid fa-box"></i> Produits</a>
        </li>
        <!-- Lien vers la liste des commandes -->
        <li data-view="commandes" class="{{ request()->is('commandes') ? 'active' : '' }}">
            <a href="{{ url('/commandes') }}" data-view="commandes"><i class="fa-solid fa-cart-shopping"></i> Commandes</a>
        </li>
        <!-- Lien vers les clients associés au vendeur -->
        <li data-view="clients" class="{{ request()->is('clients') ? 'active' : '' }}">
            <a href="{{ url('/clients') }}" data-view="clients"><i class="fa-solid fa-users"></i> Clients</a>
        </li>
        <!-- Lien vers les analyses (placeholder) -->
        <li data-view="analyses" class="{{ request()->is('analyses') ? 'active' : '' }}">
            <a href="#" data-view="analyses"><i class="fa-solid fa-chart-pie"></i> Analyses</a>
        </li>
        <!-- Lien vers la messagerie (placeholder) -->
        <li data-view="messages" class="{{ request()->is('messages') ? 'active' : '' }}">
            <a href="#" data-view="messages"><i class="fa-solid fa-envelope"></i> Messages</a>
        </li>
        <!-- Lien vers les paramètres du compte (placeholder) -->
        <li data-view="parametres" class="{{ request()->is('parametres') ? 'active' : '' }}">
            <a href="#" data-view="parametres"><i class="fa-solid fa-gear"></i> Paramètres</a>
        </li>
        
        <!-- Deconnexion -->
        <li>
            <a href="{{ url('/ConnexionVendeur') }}"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
        </li>   
    </ul>
</aside>


    <!-- CONTENU PRINCIPAL : zone où le contenu change selon la navigation -->
    <main class="main-content">
        <!-- HEADER : titre et affichage du nom du vendeur si disponible -->
        <header class="header">
            <h1>Tableau de Bord Vendeur</h1>
            <div class="account">
                <i class="fa-solid fa-user"></i>
                {{-- Affiche le prénom et le nom du vendeur si présents, sinon affiche "Mon Compte" --}}
                @if(isset($vendeur->Prenom) || isset($vendeur->Nom))
                    {{ trim(($vendeur->Prenom ?? '') . ' ' . ($vendeur->Nom ?? '')) }}
                @else
                    Mon Compte
                @endif
            </div>
        </header>

        <!-- Section statistiques rapides -->
        <section class="stats">
            <div class="card">
                <h3><i class="fa-solid fa-box"></i> Produits</h3>
                {{-- Affiche le nombre de produits: utilise $produitsCount si fourni, sinon compte via la relation --}}
                <p class="number">{{ $produitsCount ?? $vendeur->produits()->count() }}</p>
                <span class="green"><i class="fa-solid fa-list"></i> Total produits</span>
            </div>

            <div class="card">
                <h3><i class="fa-solid fa-cart-shopping"></i> Commandes</h3>
                {{-- Nombre de commandes récentes, valeur par défaut 0 si non fournie --}}
                <p class="number">{{ $commandesCount ?? 0 }}</p>
                <span class="green"><i class="fa-solid fa-clock"></i> Récentes</span>
            </div>

            <div class="card">
                <h3><i class="fa-solid fa-envelope"></i> Messages non lus</h3>
                {{-- Affiche le nombre de messages non lus --}}
                <p class="number">{{ $messagesNonLus ?? 0 }}</p>
                <span class="green"><i class="fa-solid fa-comments"></i> À lire</span>
            </div>

        </section>

<!-- Section commandes récentes affichée sous forme de tableau -->
<section class="orders">
    <h2>Commandes Récentes</h2>
    <table>
        <tr>
            <th>N° Commande</th>
            <th>Date</th>
            <th>Statut</th>
        </tr>
        {{-- Si des commandes récentes existent, les afficher dans une boucle --}}
        @if(isset($commandesRecentes) && $commandesRecentes->count())
            @foreach($commandesRecentes as $commande)
                <tr>
                    {{-- Identifiant de la commande personnalisé --}}
                    <td>#C-{{ $commande->idCommande }}</td>
                    {{-- Date de la commande telle qu'enregistrée --}}
                    <td>{{ $commande->DateCommande }}</td>
                    {{-- Statut ou montant comme fallback --}}
                    <td>{{ $commande->Statut ?? ($commande->MontantTotal ?? '') }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="3">Aucune commande récente.</td>
            </tr>
        @endif
    </table>
</section>

<section class="top-products">
    <h2>Top Produits</h2>

    {{-- Récupère les 3 premiers produits du vendeur pour affichage rapide --}}
    @php $top = $vendeur->produits->take(3); @endphp
    @if($top->count())
        @foreach($top as $produit)
            <div class="product">
                {{-- Nom du produit --}}
                <span><i class="fa-solid fa-box"></i> {{ $produit->Nom }}</span>
                {{-- Stock disponible, affiche un tiret si inconnu --}}
                <small>Stock: {{ $produit->Stock ?? '—' }}</small>
            </div>
        @endforeach
    @else
        <p>Aucun produit disponible.</p>
    @endif

</section>


    </main>

</div>

    <script>
document.addEventListener('submit', function(e) {

    if (!e.target || e.target.id !== 'formProduit') return;

    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    fetch("{{ route('produits.AjouterProduit') }}", {
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                || document.querySelector('input[name="_token"]').value
        },
        body: formData
    })
    .then(res => {
        if (!res.ok) throw res;
        return res.json();
    })
    .then(data => {
        alert(data.message);
        document.getElementById('addModal').style.display = 'none';

        // recharge la vue produits
        location.hash = 'produits';
        location.reload();
    })
    .catch(err => {
        alert("Erreur lors de l'ajout du produit");
        console.error(err);
    });
});
</script>


</body>
</html>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const sidebar = document.querySelector('.sidebar');
    const main = document.querySelector('.main-content');

    if(!sidebar || !main) return;

    let skipNextHashChange = false;

    // mapping of view keys to URLs to fetch
    const viewMap = {
        dashboard: '{{ route('PageVendeur') }}',
        produits: '{{ url('/produits') }}',
        commandes: '{{ url('/commandes') }}',
        clients: '{{ url('/clients') }}',
        analyses: '{{ url('/analyses') }}',
        parametres: '{{ url('/parametres') }}',
        messages: '{{ url('/messages') }}'
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
            // no remote view: just update active state
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
                        // resolve relative URLs
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
        if(!viewKey) return; // not a SPA-managed link
        e.preventDefault();
        // load view and set hash to view key
        await loadView(viewKey);
        skipNextHashChange = true;
        location.hash = viewKey;
    });

    window.addEventListener('hashchange', function(){
        if(skipNextHashChange){ skipNextHashChange = false; return; }
        const viewKey = decodeURIComponent(location.hash || '').replace(/^#/, '') || 'dashboard';
        loadView(viewKey);
    });

    // initial load: if hash present, load that view, otherwise show dashboard and set active
    if(location.hash){
        const initial = decodeURIComponent(location.hash).replace(/^#/, '') || 'dashboard';
        loadView(initial);
    } else {
        updateActiveFromLocation();
    }
    // Intercept product filter form submits inside main (works for injected content)
    document.addEventListener('submit', async function(ev){
        if(!ev.target || ev.target.id !== 'filterForm') return;
        ev.preventDefault();
        const form = ev.target;
        const params = new URLSearchParams(new FormData(form));
        const publicUrl = form.action + (params.toString() ? ('?' + params.toString()) : '');
        const fetchUrl = form.action + (params.toString() ? ('?' + params.toString() + '&partial=1') : '?partial=1');
        try{
            const main = document.querySelector('.main-content');
            const productList = main ? main.querySelector('#product-list') : null;
            const loader = productList ? productList.querySelector('#loading') : null;
            if(loader) loader.style.display = 'flex';
            const res = await fetch(fetchUrl, {headers: {'X-Requested-With': 'XMLHttpRequest'}});
            if(!res.ok){ window.location.href = publicUrl; return; }
            const html = await res.text();
            if(productList){ productList.innerHTML = html; }
            // update browser visible URL without navigating (optional)
            history.replaceState(null, '', publicUrl);
        }catch(err){
            window.location.href = publicUrl;
        }finally{
            const main2 = document.querySelector('.main-content');
            const productList2 = main2 ? main2.querySelector('#product-list') : null;
            const loader2 = productList2 ? productList2.querySelector('#loading') : null;
            if(loader2) loader2.style.display = 'none';
        }
    });

    // Intercept clicks on product links inside injected content
    document.addEventListener('click', async function(ev){
        const a = ev.target.closest && ev.target.closest('a');
        if(!a) return;
        // only handle links inside the main-content area
        const main = document.querySelector('.main-content');
        if(!main || !main.contains(a)) return;
        const href = a.getAttribute('href') || '';
        // handle product detail links like /produits/{id}
        try{
            const url = new URL(href, window.location.origin);
            if(url.pathname.startsWith('/produits/')){
                ev.preventDefault();
                const res = await fetch(url.href, {headers: {'X-Requested-With': 'XMLHttpRequest'}});
                if(!res.ok){ window.location.href = url.href; return; }
                const text = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(text, 'text/html');
                const newMain = doc.querySelector('.main-content') || doc.querySelector('main') || doc.querySelector('body');
                if(newMain) main.innerHTML = newMain.innerHTML; else main.innerHTML = text;
                // execute scripts from fetched document
                try{
                    const fetchedScripts = doc.querySelectorAll('script');
                    fetchedScripts.forEach(function(s){
                        const ns = document.createElement('script');
                        if(s.src){
                            try{ ns.src = new URL(s.src, url.href).href; } catch(e){ ns.src = s.src; }
                            ns.async = false;
                        } else {
                            ns.textContent = s.textContent;
                        }
                        document.body.appendChild(ns);
                    });
                }catch(e){}
                // update browser URL without full navigation
                history.pushState(null, '', url.href);
                // mark produits as active in sidebar
                sidebar.querySelectorAll('li').forEach(li => li.classList.remove('active'));
                const prodLi = sidebar.querySelector('li[data-view="produits"]'); if(prodLi) prodLi.classList.add('active');
            }
        }catch(e){ /* ignore malformed URLs */ }
    });
});
</script>