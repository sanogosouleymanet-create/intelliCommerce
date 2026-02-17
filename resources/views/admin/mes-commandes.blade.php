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
                        <button class="btn-view" onclick="viewCommande({{ $commande->idCommande }})">
                            <i class="fa-solid fa-eye"></i> Voir
                        </button>
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

<!-- Order Details Modal -->
<div id="commandeModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Détails de la Commande</h3>
        <div id="commandeDetails"></div>
    </div>
</div>

<script>
function viewCommande(id) {
    // Fetch order details via AJAX (include credentials and accept JSON)
    fetch(`{{ url('/admin/commandes') }}/${id}`, { credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
        .then(async response => {
            const ct = response.headers.get('content-type') || '';
            if (!response.ok) {
                // try to extract JSON message or fallback to text
                let msg = '';
                try { msg = await response.json(); msg = msg.message || JSON.stringify(msg); } catch(e){ msg = await response.text(); }
                throw new Error('Erreur HTTP ' + response.status + '\n' + msg);
            }
            if (ct.indexOf('application/json') === -1) {
                // server returned HTML (likely a redirect to login) — capture it for debugging
                const text = await response.text();
                throw new Error('Réponse inattendue (HTML) reçue du serveur. Contenu:\n' + text.slice(0,2000));
            }
            return response.json();
        })
        .then(data => {
            const clientInfo = data.client ?
                `${data.client.Nom} ${data.client.Prenom}` :
                'Client inconnu';

            const clientEmail = data.client ? data.client.email : 'N/A';
            const clientPhone = data.client ? data.client.TelClient : 'N/A';
            const clientAddress = data.client ? data.client.Adresse : 'N/A';

            const dateStr = data.DateCommande ? new Date(data.DateCommande).toLocaleString('fr-FR') : 'N/A';

            const nf = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const montantFormate = (data.montant_total !== undefined && data.montant_total !== null)
                ? nf.format(Number(data.montant_total)) + ' FCFA'
                : '0,00 FCFA';

            const produitsList = data.produitcommandes.map(pc => {
                const produitName = pc.produit ? pc.produit.Nom : 'Produit inconnu';
                const prix = (pc.PrixUnitaire !== undefined && pc.PrixUnitaire !== null) ? nf.format(Number(pc.PrixUnitaire)) + ' FCFA' : '-';
                const img = (pc.produit && pc.produit.Image) ? ('<img src="' + pc.produit.Image + '" alt="' + String(produitName).replace(/"/g,'') + '" class="commande-thumb">') : '';
                return '<li class="produit-line">' + img + '<div class="produit-meta"><strong>' + produitName + '</strong><div>Quantité: ' + pc.Quantite + '</div><div>Prix: ' + prix + '</div></div></li>';
            }).join('');

            document.getElementById('commandeDetails').innerHTML = `
                <div class="commande-modal-body">
                  <dl class="commande-details">
                    <dt>ID</dt><dd>#${data.idCommande}</dd>
                    <dt>Client</dt><dd>${clientInfo}</dd>
                    <dt>Email</dt><dd>${clientEmail}</dd>
                    <dt>Téléphone</dt><dd>${clientPhone}</dd>
                    <dt>Adresse</dt><dd>${clientAddress}</dd>
                    <dt>Date</dt><dd>${dateStr}</dd>
                    <dt>Montant Total</dt><dd>${montantFormate}</dd>
                    <dt>Statut</dt><dd>${data.Statut}</dd>
                  </dl>
                  <h4>Produits</h4>
                  <ul class="modal-produits">
                    ${produitsList}
                  </ul>
                </div>
            `;
            document.getElementById('commandeModal').style.display = 'block';
        })
        .catch(error => {
            console.error('Erreur:', error);
            // show error in modal for easier debugging
            const details = document.getElementById('commandeDetails');
            if (details) {
                details.innerHTML = '<div class="card" style="padding:12px;background:#fff7f7;color:#a00;border-radius:6px;">' +
                    '<strong>Erreur lors du chargement :</strong><pre style="white-space:pre-wrap;margin:8px 0 0;font-size:0.9rem;color:#333">' + String(error.message || error) + '</pre></div>';
                document.getElementById('commandeModal').style.display = 'block';
                return;
            }
            alert('Erreur lors du chargement des détails: ' + (error.message || ''));
        });
}

function closeModal() {
    document.getElementById('commandeModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('commandeModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>

<style>
.btn-view {
    background: #28a745;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 3px;
    cursor: pointer;
}

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

.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: 5px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: black;
}

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

.commande-modal-body{display:block;padding:6px 0}
.commande-details{display:grid;grid-template-columns:140px 1fr;gap:6px 12px;margin:0 0 12px 0}
.commande-details dt{font-weight:700}
.commande-details dd{margin:0}
.modal-produits{margin:8px 0 0 18px;padding-left:0}
.modal-produits li{margin-bottom:6px}

.commande-thumb{width:60px;height:60px;object-fit:cover;border-radius:4px;margin-right:8px;vertical-align:middle}
.produit-line{display:flex;align-items:flex-start;gap:10px}
.produit-meta{font-size:0.95em}
</style>
</div>