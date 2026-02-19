<div class="main-content">
<div class="card">
    <h2>Mes Commandes</h2>

    <!-- Orders Table -->
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID Commande</th>
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
                    <td colspan="6" style="text-align: center;">Aucune commande trouvée</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>

<style>
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

.table-container{overflow-x:auto}
.table{width:100%;border-collapse:collapse;table-layout:fixed}
.table th,.table td{padding:8px 10px;border-bottom:1px solid #eee;vertical-align:top}
.table th{font-weight:700;background:transparent;text-align:left;white-space:nowrap}
.table td{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.table td:nth-child(1){width:80px}
.table td:nth-child(2){width:160px}
.table td:nth-child(3){width:140px;text-align:right;white-space:nowrap}
.table td:nth-child(4){width:120px;text-align:center}
.table td:nth-child(5){width:260px;white-space:normal}
.table td:nth-child(6){width:110px;text-align:center}

.status{display:inline-block;padding:4px 8px;border-radius:6px;font-size:.85em}

.btn-view{min-width:70px}
</style>