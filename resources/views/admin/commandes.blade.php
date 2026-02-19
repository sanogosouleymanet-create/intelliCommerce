<div class="main-content">
<div class="card">
    <h2>Gestion des Commandes</h2>
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.mes-commandes') }}" class="btn btn-primary">Mes commandes</a>
    </div>

    <!-- Filters -->
    <div class="filters" style="margin-bottom: 20px;">
        <form id="filterForm" action="javascript:void(0);">
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <div>
                    <label for="statut">Statut:</label>
                    <select name="statut" id="statut">
                        <option value="">Tous</option>
                        <option value="En cours" {{ request('statut') === 'En cours' ? 'selected' : '' }}>En cours</option>
                        <option value="Livrée" {{ request('statut') === 'Livrée' ? 'selected' : '' }}>Livrée</option>
                        <option value="Annulée" {{ request('statut') === 'Annulée' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>
                <div>
                    <label for="recherche">Recherche:</label>
                    <input type="text" name="recherche" id="recherche" value="{{ request('recherche') }}" placeholder="Client ou produit...">
                </div>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID Commande</th>
                    <th>Client</th>
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
                    <td>{{ $commande->client ? $commande->client->Nom . ' ' . $commande->client->Prenom : 'Client inconnu' }}</td>
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
                        <a href="{{ route('admin.commandes.show', $commande->idCommande) }}" class="btn btn-view">
                            <i class="fa-solid fa-eye"></i> Voir
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Aucune commande trouvée</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>

<script>
// Live AJAX filtering without page navigation
(function(){
    const base = '{{ route('admin.commandes') }}';
    const form = document.getElementById('filterForm');
    const recherche = document.getElementById('recherche');
    const statut = document.getElementById('statut');

    function buildUrl(){
        const params = new URLSearchParams();
        if(statut && statut.value) params.set('statut', statut.value);
        if(recherche && recherche.value) params.set('recherche', recherche.value);
        const q = params.toString();
        return q ? (base + '?' + q) : base;
    }

    function updateFilters(){
        const url = buildUrl();
        fetch(url)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.querySelector('.table-container');
                if (newTable) {
                    document.querySelector('.table-container').innerHTML = newTable.innerHTML;
                }
            })
            .catch(e => console.error('Live filter failed', e));
    }

    function debounce(fn, wait){ let t; return function(){ clearTimeout(t); const args=arguments; t=setTimeout(()=>fn.apply(this,args), wait); }; }

    if(form){ form.addEventListener('submit', function(e){ e.preventDefault(); updateFilters(); }); }
    if(recherche){
        recherche.addEventListener('keydown', function(e){ if(e.key === 'Enter'){ e.preventDefault(); updateFilters(); } });
        recherche.addEventListener('input', debounce(updateFilters, 350));
    }
    if(statut){ statut.addEventListener('change', updateFilters); }
})();
</script>

<style>
.filters {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.filters label {
    margin-right: 5px;
    font-weight: bold;
}

.filters input, .filters select {
    padding: 5px;
    margin-right: 10px;
    border: 1px solid #ddd;
    border-radius: 3px;
}

.btn-primary, .btn-secondary {
    padding: 8px 15px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-view {
    background: #28a745;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 3px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-view:hover { color: #fff; opacity: 0.9; }

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

/* Table & layout improvements to avoid crowded text */
.table-container{overflow-x:auto}
.table{width:100%;border-collapse:collapse;table-layout:fixed}
.table th,.table td{padding:8px 10px;border-bottom:1px solid #eee;vertical-align:top}
.table th{font-weight:700;background:transparent;text-align:left;white-space:nowrap}
.table td{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.table td:nth-child(1){width:80px}
.table td:nth-child(2){width:220px}
.table td:nth-child(3){width:160px}
.table td:nth-child(4){width:140px;text-align:right;white-space:nowrap}
.table td:nth-child(5){width:120px;text-align:center}
.table td:nth-child(6){width:260px;white-space:normal}
.table td:nth-child(7){width:110px;text-align:center}

.status{display:inline-block;padding:4px 8px;border-radius:6px;font-size:.85em}

.filters .row, .filters > div { box-sizing: border-box }
.filters form > div{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
.filters label{display:block;margin-bottom:3px}
.filters input[type="date"], .filters input[type="text"], .filters select{min-width:140px}

.btn-view{min-width:70px}

</style>
