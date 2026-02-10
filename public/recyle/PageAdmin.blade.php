
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Tableau de bord</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/StyleAdmin.css') }}">
</head>
<body>
    @include('admin.header')
    <div class="container">
        <aside class="sidebar">
            <img src="{{ asset('Logo-Site.png') }}" width="200" alt="Logo de la plateforme" title="LOGO" class="logo">
            <ul>
                <li data-view="dashboard"><a href="{{ url('/admin') }}" data-view="dashboard"><i class="fa-solid fa-chart-line"></i> Tableau de Bord</a></li>
                <li data-view="produits"><a href="{{ route('admin.produits') }}" data-view="produits"><i class="fa-solid fa-box"></i> Produits</a></li>
                <li data-view="clients"><a href="{{ route('admin.clients') }}" data-view="clients"><i class="fa-solid fa-users"></i> Clients</a></li>
                <li data-view="vendeurs"><a href="{{ route('admin.vendeurs') }}" data-view="vendeurs"><i class="fa-solid fa-store"></i> Vendeurs</a></li>
                <li data-view="messages"><a href="{{ route('admin.messages') }}" data-view="messages"><i class="fa-solid fa-inbox"></i> Messages</a></li>
                <li data-view="commandes"><a href="{{ route('admin.commandes') }}" data-view="commandes"><i class="fa-solid fa-shopping-cart"></i> Commandes</a></li>
                <li data-view="cart"><a href="{{ route('admin.cart') }}" data-view="cart"><i class="fa-solid fa-shopping-cart"></i> Mon Panier</a></li>
                <li><a href="{{ url('/PagePrincipale') }}"><i class="fa-solid fa-house"></i> Accueil</a></li>
                <li>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="BT"><i class="fa-solid fa-right-from-bracket"></i> Se déconnecter</button>
                    </form>
                </li>
            </ul>
        </aside>
        <main class="main-content" id="main-content">
            <!-- Le contenu AJAX sera injecté ici -->
        </main>
    </div>
    <script>
    // SPA Admin : gestion AJAX centralisée
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.sidebar');
        const main = document.getElementById('main-content');
        // Navigation sidebar
        sidebar.addEventListener('click', function(e) {
            const link = e.target.closest('a[data-view]');
            if (!link) return;
            e.preventDefault();
            const view = link.getAttribute('data-view');
            loadView(view);
            window.location.hash = view;
        });
        // Navigation par hash (back/forward)
        window.addEventListener('hashchange', function() {
            const view = window.location.hash.replace(/^#/, '') || 'dashboard';
            loadView(view);
        });
        // Chargement initial
        const initialView = window.location.hash.replace(/^#/, '') || 'dashboard';
        loadView(initialView);

        // Fonction de chargement AJAX
        async function loadView(view) {
            // Met à jour l'état actif de la sidebar
            sidebar.querySelectorAll('li').forEach(li => li.classList.remove('active'));
            const li = sidebar.querySelector('li[data-view="' + view + '"]');
            if (li) li.classList.add('active');
            // Mapping des routes
            const routes = {
                dashboard: '{{ url('/admin') }}',
                produits: '{{ route('admin.produits') }}',
                clients: '{{ route('admin.clients') }}',
                vendeurs: '{{ route('admin.vendeurs') }}',
                messages: '{{ route('admin.messages') }}',
                commandes: '{{ route('admin.commandes') }}',
                cart: '{{ route('admin.cart') }}'
            };
            const url = routes[view] || routes.dashboard;
            try {
                const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) { main.innerHTML = '<div class="card"><p>Erreur de chargement : ' + res.status + '</p></div>'; return; }
                const text = await res.text();
                // On injecte uniquement le HTML utile (pas de layout)
                main.innerHTML = text;
            } catch (e) {
                main.innerHTML = '<div class="card"><p>Erreur réseau lors du chargement.</p></div>';
            }
        }
    });
    </script>
</body>
</html>