<div class="main-content">
<div class="card admin-commande-detail">
    <style>
        .admin-commande-detail{max-width:720px;margin:20px auto;padding:24px}
        .admin-commande-detail h2{margin:0 0 16px;font-size:1.35rem}
        .admin-commande-detail .commande-details{display:grid;grid-template-columns:140px 1fr;gap:8px 16px;margin:0 0 20px}
        .admin-commande-detail .commande-details dt{font-weight:700;color:#333}
        .admin-commande-detail .commande-details dd{margin:0;color:#555}
        .admin-commande-detail .commande-produits{margin:16px 0 0;padding:0;list-style:none}
        .admin-commande-detail .commande-produits li{display:flex;align-items:flex-start;gap:12px;margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid #eee}
        .admin-commande-detail .commande-produits li:last-child{border-bottom:0;margin-bottom:0;padding-bottom:0}
        .admin-commande-detail .commande-thumb{width:60px;height:60px;object-fit:cover;border-radius:6px;flex-shrink:0}
        .admin-commande-detail .commande-produit-meta{flex:1;font-size:0.95rem}
        .admin-commande-detail .commande-produit-meta strong{display:block;margin-bottom:4px}
        .admin-commande-detail .status{padding:4px 10px;border-radius:6px;font-size:0.9em;font-weight:600;display:inline-block}
        .admin-commande-detail .status-en-attente{background:#fff3cd;color:#856404}
        .admin-commande-detail .status-confirmée{background:#d1ecf1;color:#0c5460}
        .admin-commande-detail .status-en-préparation{background:#d4edda;color:#155724}
        .admin-commande-detail .status-expédiée{background:#cce5ff;color:#004085}
        .admin-commande-detail .status-livrée{background:#d4edda;color:#155724}
        .admin-commande-detail .status-annulée{background:#f8d7da;color:#721c24}
        .admin-commande-detail .btn-retour{margin-top:20px;display:inline-block;padding:10px 20px;background:#007bff;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;border:none;cursor:pointer}
        .admin-commande-detail .btn-retour:hover{background:#0056b3;color:#fff}
    </style>
    <h2>Détails de la commande #{{ $commande->idCommande }}</h2>
    <dl class="commande-details">
        <dt>Client</dt>
        <dd>{{ $commande->client ? $commande->client->Nom . ' ' . $commande->client->Prenom : 'Client inconnu' }}</dd>
        <dt>Email</dt>
        <dd>{{ $commande->client ? $commande->client->email : 'N/A' }}</dd>
        <dt>Téléphone</dt>
        <dd>{{ $commande->client ? $commande->client->TelClient : 'N/A' }}</dd>
        <dt>Adresse</dt>
        <dd>{{ $commande->client ? $commande->client->Adresse : 'N/A' }}</dd>
        <dt>Date</dt>
        <dd>{{ $commande->DateCommande ? \Carbon\Carbon::parse($commande->DateCommande)->format('d/m/Y H:i:s') : 'N/A' }}</dd>
        <dt>Montant total</dt>
        <dd>{{ number_format($commande->montant_total, 2, ',', ' ') }} FCFA</dd>
        <dt>Statut</dt>
        <dd><span class="status status-{{ strtolower($commande->Statut ?? '') }}">{{ $commande->Statut ?? 'N/A' }}</span></dd>
    </dl>
    <h4 style="margin:0 0 12px;">Produits</h4>
    <ul class="commande-produits">
        @foreach($commande->produitcommandes as $pc)
            <li>
                @php
                    $produit = $pc->produit;
                    $imgUrl = 'https://via.placeholder.com/60x60?text=No';
                    if ($produit && !empty($produit->Image)) {
                        $imgUrl = \Illuminate\Support\Facades\Storage::exists('public/'.$produit->Image)
                            ? asset('storage/'.$produit->Image)
                            : (preg_match('/^https?:\/\//i', $produit->Image) ? $produit->Image : asset('images/placeholder.png'));
                    }
                @endphp
                <img src="{{ $imgUrl }}" alt="{{ $produit ? $produit->Nom : 'Produit' }}" class="commande-thumb">
                <div class="commande-produit-meta">
                    <strong>{{ $produit ? $produit->Nom : 'Produit inconnu' }}</strong>
                    <div>Quantité: {{ $pc->Quantite }}</div>
                    <div>Prix: {{ $pc->PrixUnitaire !== null ? number_format($pc->PrixUnitaire, 2, ',', ' ') . ' FCFA' : '-' }}</div>
                </div>
            </li>
        @endforeach
    </ul>
    <a href="{{ route('admin.commandes') }}" class="btn-retour" id="commande-detail-retour">
        <i class="fa-solid fa-arrow-left"></i> Retour à la liste
    </a>
</div>
</div>

<script>
(function(){
    const link = document.getElementById('commande-detail-retour');
    if (link && window.adminFetchAndInject) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            window.adminFetchAndInject(link.getAttribute('href'));
        });
    }
})();
</script>
