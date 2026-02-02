<header class="header">
    <h1>Analyses</h1>
    <div class="account">
        <i class="fa-solid fa-user"></i>
        @if(isset($vendeur->Prenom) || isset($vendeur->Nom))
            {{ trim(($vendeur->Prenom ?? '') . ' ' . ($vendeur->Nom ?? '')) }}
        @else
            Mon Compte
        @endif
    </div>
</header>

<section class="card">
    <p class="small-muted">Vue récapitulative des performances de la boutique.</p>

    <div class="cards-grid">
        <div class="summary-card">
            <h3>Ventes (30j)</h3>
            <strong>{{ isset($ventes30) ? number_format($ventes30, 0, ',', ' ') . ' FCFA' : '—' }}</strong>
            <p class="small-muted">Comparé au mois précédent: N/A</p>
        </div>
        <div class="summary-card">
            <h3>Commandes</h3>
            <strong>{{ $commandesCount ?? 0 }}</strong>
            <p class="small-muted">Produits listés: {{ $produitsCount ?? 0 }}</p>
        </div>
        <div class="summary-card">
            <h3>Produits populaires</h3>
            <ul>
                @if(isset($topProducts) && $topProducts->count())
                    @foreach($topProducts as $p)
                        <li>{{ $p['nom'] }} - {{ $p['ventes'] }} ventes</li>
                    @endforeach
                @else
                    <li>Aucun produit récent</li>
                @endif
            </ul>
        </div>
    </div>
</section>

<style>
    /* Styles spécifiques à la page Analyses */
@import url('StylePageVendeur.css');

.cards-grid{ display:flex; gap:12px; margin-top:12px; }
.summary-card{ background:#07263b; color:#fff; padding:12px; border-radius:8px; }

</style>
