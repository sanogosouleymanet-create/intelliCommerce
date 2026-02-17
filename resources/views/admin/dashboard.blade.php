<div class="main-content">
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

    <!-- Recent Orders Section -->
    <div class="card">
        <h2>Mes Commandes Récentes</h2>
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
                            <button class="btn-view" onclick="viewCommande({{ $commande->idCommande }})">
                                <i class="fa-solid fa-eye"></i> Voir
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Aucune commande récente</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div id="commandeModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Détails de la Commande</h3>
            <div id="commandeDetails"></div>
        </div>
    </div>
</div>