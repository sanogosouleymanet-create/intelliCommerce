<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Clients</title>
    <link rel="stylesheet" href="{{ asset('css/StyleClients.css') }}">
</head>
<body>
    <header class="header">
        <h1>Clients</h1>
        <div class="account">
            <i class="fa-solid fa-user"></i>
            @if(isset($vendeur->Prenom) || isset($vendeur->Nom))
                {{ trim(($vendeur->Prenom ?? '') . ' ' . ($vendeur->Nom ?? '')) }}
            @else
                Mon Compte
            @endif
        </div>
    </header>

    <section class="orders-header">
        <!--<div>
            <h2>Liste des clients</h2>
            <p class="small-muted">Recherchez et gérez vos clients.</p>
        </div>-->
        <div class="clients-filtres">
            <div class="orders-actions">
            <input type="text" id="searchClient" placeholder="Rechercher par nom ou email" class="search-input">
            <button class="btn" id="btnFilterClients">Rechercher</button>
        </div>
        </div>
        
    </section>

    <!--<section class="orders-summary">
        <div class="summary-card">
            <div>Clients totaux</div>
            <strong>{{ $clients->count() }}</strong>
        </div>
    </section>-->

    <section class="clients-list">
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Commandes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="clientsBody">
                @foreach($clients as $client)
                    <tr data-id="{{ $client->idClient }}">
                        <td>{{ $client->Nom }} {{ $client->Prenom }}</td>
                        <td>{{ $client->email }}</td>
                        <td>{{ $client->TelClient ?? '—' }}</td>
                        <td>{{ $client->commandes()->count() }}</td>
                        <td><button class="btn secondary btn-view-client">Voir</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <script>
        document.getElementById('btnFilterClients')?.addEventListener('click', function(){
            const q = (document.getElementById('searchClient')?.value || '').toLowerCase();
            document.querySelectorAll('#clientsBody tr[data-id]').forEach(function(row){
                const name = row.querySelectorAll('td')[0]?.textContent.toLowerCase() || '';
                const email = row.querySelectorAll('td')[1]?.textContent.toLowerCase() || '';
                const matches = q === '' || name.includes(q) || email.includes(q);
                row.style.display = matches ? '' : 'none';
            });
        });

        // Example client view action (placeholder)
        document.addEventListener('click', function(e){
            const btn = e.target.closest('.btn-view-client');
            if(!btn) return;
            const tr = btn.closest('tr');
            const id = tr?.dataset?.id;
            alert('Afficher le client: ' + id);
        });
    </script>

</body>
</html>
