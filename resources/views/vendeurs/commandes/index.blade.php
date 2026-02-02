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
            <strong>{{ is_array($commandes) ? count($commandes) : $commandes->count() }}</strong>
        </div>
        <div class="summary-card">
            <div>Total ventes</div>
            <strong>
                {{ is_array($commandes)
                    ? number_format(array_sum(array_column($commandes, 'MontantTotal')), 0, ',', ' ')
                    : number_format($commandes->sum('MontantTotal') ?? 0, 0, ',', ' ')
                }} FCFA
            </strong>
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
                        <td>{{ is_array($commande->Produit) ? count($commande->Produit) : (method_exists($commande->Produit, 'count') ? $commande->Produit->count() : 0) }}</td>
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

<style>
    .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.account {
    background: white;
    padding: 10px 15px;
    border-radius: 8px;
    cursor: pointer;
}

/* Orders page styles */
.orders-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
}
.orders-actions{
    display:flex;
    gap:8px;
    align-items:center;
}
.orders-summary{ 
    display:flex; 
    gap:12px; 
    margin:16px 0; 
}
.summary-card{ 
    background:#0b2340; 
    color:#fff; 
    padding:12px 16px; 
    border-radius:8px; 
    min-width:120px; 
}
.orders-table{ 
    width:100%; 
    border-collapse:collapse; 
    background:transparent; 
}
.orders-table th, .orders-table td{ 
    padding:12px 10px; 
    text-align:left; 
    border-bottom:1px solid rgba(255,255,255,0.06); 
    color:#e6eef8; 
}
.orders-table th{ 
    color:#cfe6ff; 
    font-weight:600; 
    font-size:13px; 
}
.order-badge{ 
    padding:6px 10px; 
    border-radius:999px; 
    font-size:13px; 
    color:#fff; 
    display:inline-block; 
}
.badge-pending{ 
    background:#f59e0b; 
}
.badge-processing{ 
    background:#3b82f6; 
}
.badge-completed{ 
    background:#10b981; 
}
.badge-cancelled{ 
    background:#ef4444; 
}
.btn{ 
    padding:8px 12px; 
    border-radius:6px; 
    background:#1f2937; 
    color:#fff; 
    border:none; 
    cursor:pointer; 
}
.btn.secondary{ 
    background:#374151; 
}
.order-row-details{ 
    background: rgba(255,255,255,0.02); 
    padding:12px; 
    border-radius:6px; 
    margin:8px 0 20px 0; 
    color:#dbeafe; 
}
.small-muted{ 
    color: rgba(255,255,255,0.6); 
    font-size:13px; 
}

@media (max-width:900px){
    .orders-table thead{ 
        display:none; 
    }
    .orders-table tr{ 
        display:block; 
        margin-bottom:12px; 
        border-bottom: none; 
    }
    .orders-table td{ 
        display:flex; 
        justify-content:space-between; 
        padding:8px 6px; 
    }
}

.filter-commandes {
    display: flex;
    gap: 12px;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;

    background-color: #ffffff;
    padding: 15px 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}
.search-input {
    padding: 10px 14px;
    width: 250px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    outline: none;
    transition: border-color 0.3s ease;
}
.search-input:focus {
    border-color: #007bff;
}

/* Select (catégorie, prix, etc.) */
.filter-select {
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    cursor: pointer;
    background-color: #fff;
    transition: border-color 0.3s ease;
}

.filter-select:hover,
.filter-select:focus {
    border-color: #007bff;
}
.filter-btn {
    padding: 10px 18px;
    border-radius: 8px;
    border: none;
    background-color: #007bff;
    color: #fff;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.filter-btn:hover {
    background-color: #0056b3;
    transform: translateY(-1px);
}
</style>