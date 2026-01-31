@php
    // Espace Vendeur — Analyses
    // Variables attendues : $vendeur
@endphp

<section class="container vendeurs-analyses">
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card p-3">
                <h5>Total Ventes</h5>
                @php
                    $commandesQuery = \App\Models\Commande::whereHas('Produit', function($q) use ($vendeur){ $q->where('Vendeur_idVendeur', $vendeur->idVendeur); });
                    $totalVentes = $commandesQuery->get()->sum(function($c){ return $c->MontantTotal ?? $c->MontanTotal ?? $c->Montant ?? 0; });
                    $nbCommandes = $commandesQuery->count();
                @endphp
                <div class="fs-2 fw-bold">{{ number_format($totalVentes,0,',',' ') }} FCFA</div>
                <div class="text-muted">{{ $nbCommandes }} commandes</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3">
                <h5>Produits</h5>
                <div class="fs-2 fw-bold">{{ $vendeur->produits()->count() }}</div>
                <div class="text-muted">Produits au catalogue</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3">
                <h5>Clients actifs</h5>
                @php $clientsCount = \App\Models\Client::whereHas('commandes', function($q) use ($vendeur){ $q->whereHas('Produit', function($p) use ($vendeur){ $p->where('Vendeur_idVendeur', $vendeur->idVendeur); }); })->distinct()->count(); @endphp
                <div class="fs-2 fw-bold">{{ $clientsCount }}</div>
                <div class="text-muted">clients actifs</div>
            </div>
        </div>
    </div>

    <!-- Simple revenue by day (last 7 days) -->
    <div class="card p-3">
        <h5>Chiffre d'affaires / 7 derniers jours</h5>
        @php
            $days = collect();
            for($i=6;$i>=0;$i--){
                $d = \Carbon\Carbon::today()->subDays($i);
                $sum = \App\Models\Commande::whereHas('Produit', function($q) use ($vendeur){ $q->where('Vendeur_idVendeur', $vendeur->idVendeur); })->whereDate('DateCommande', $d)->get()->sum(function($c){ return $c->MontantTotal ?? $c->MontanTotal ?? $c->Montant ?? 0; });
                $days->push(['label' => $d->format('d/m'), 'value' => $sum]);
            }
            $max = $days->max('value') ?: 1;
        @endphp

        <div class="d-flex gap-2 mt-3" style="align-items:end; height:140px;">
            @foreach($days as $day)
                <div style="flex:1;text-align:center">
                    <div style="height:{{ (int) ( ($day['value'] / $max) * 100) }}px; background:#2b7cff; margin-bottom:6px; border-radius:4px"></div>
                    <div class="small text-muted">{{ $day['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
