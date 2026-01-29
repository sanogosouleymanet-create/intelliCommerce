<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commandes</title>
    <link rel="stylesheet" href="{{ asset('css/StyleCommande.css') }}">
</head>
<body>
    <header class="header">
        <h1>Commandes</h1>
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
        <div class="filter-commandes">
            <div class="orders-actions">
                <input type="text" id="searchOrder" placeholder="Rechercher par n° commande ou client" class="search-input">
                <select id="filterStatus" class="filter-select">
                    <option value="">Tous statuts</option>
                    <option value="En attente">En attente</option>
                    <option value="En cours">En cours</option>
                    <option value="Terminé">Terminé</option>
                    <option value="Annulé">Annulé</option>
                </select>
                <button class="filter-btn" id="btnFilterOrders">Filtrer</button>
            </div>
        </div>
        
    </section>

    <section class="orders-summary">
        <div class="summary-card">
            <div>Commandes totales</div>
            <strong>{{ $commandes->count() }}</strong>
        </div>
        <div class="summary-card">
            <div>Total ventes</div>
            <strong>{{ number_format($commandes->sum('MontantTotal') ?? 0, 0, ',', ' ') }} FCFA</strong>
        </div>
    </section>

    <section class="orders-list">
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Commande</th>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Articles</th>
                    <th>Total</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="ordersBody">
                @foreach($commandes as $commande)
                    <tr data-id="{{ $commande->idCommande }}">
                        <td>#C-{{ $commande->idCommande }}</td>
                        <td>{{ $commande->DateCommande }}</td>
                        <td>{{ $commande->Client?->Nom ?? '—' }} {{ $commande->Client?->Prenom ?? '' }}</td>
                        <td>{{ $commande->Produit->count() }}</td>
                        <td>{{ number_format($commande->MontantTotal ?? 0, 0, ',', ' ') }} FCFA</td>
                        <td>
                            @php $s = strtolower($commande->Statut ?? ''); @endphp
                            <span class="order-badge {{ $s === 'en attente' ? 'badge-pending' : ($s === 'en cours' ? 'badge-processing' : ($s === 'terminé' ? 'badge-completed' : ($s === 'annulé' ? 'badge-cancelled' : 'badge-processing'))) }}">{{ $commande->Statut ?? '—' }}</span>
                        </td>
                        <td>
                            <button class="btn secondary btn-toggle-details">Voir</button>
                        </td>
                    </tr>
                    <tr class="order-row-details" style="display:none;" data-for="{{ $commande->idCommande }}">
                        <td colspan="7">
                            <div>
                                <strong>Détails</strong>
                                <ul>
                                    @foreach($commande->Produit as $p)
                                        <li>{{ $p->Nom }} x {{ $p->pivot->Quantite ?? 1 }} — {{ number_format($p->Prix ?? 0, 0, ',', ' ') }} FCFA</li>
                                    @endforeach
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <script>
        // Toggle details rows
        document.addEventListener('click', function(e){
            const btn = e.target.closest('.btn-toggle-details');
            if(!btn) return;
            const tr = btn.closest('tr');
            const id = tr?.dataset?.id;
            if(!id) return;
            const details = document.querySelector('tr.order-row-details[data-for="'+id+'"]');
            if(!details) return;
            details.style.display = (details.style.display === 'none' || !details.style.display) ? 'table-row' : 'none';
        });

        // Client-side quick filter (search + status)
        document.getElementById('btnFilterOrders')?.addEventListener('click', function(){
            const q = (document.getElementById('searchOrder')?.value || '').toLowerCase();
            const status = (document.getElementById('filterStatus')?.value || '').toLowerCase();
            document.querySelectorAll('#ordersBody tr[data-id]').forEach(function(row){
                const id = row.querySelector('td')?.textContent.toLowerCase() || '';
                const client = row.querySelectorAll('td')[2]?.textContent.toLowerCase() || '';
                const st = row.querySelectorAll('td')[5]?.textContent.toLowerCase() || '';
                const matches = (q === '' || id.includes(q) || client.includes(q)) && (status === '' || st.includes(status));
                row.style.display = matches ? '' : 'none';
                const details = document.querySelector('tr.order-row-details[data-for="'+row.dataset.id+'"]');
                if(details) details.style.display = matches ? details.style.display : 'none';
            });
        });
    </script>

</body>
</html>