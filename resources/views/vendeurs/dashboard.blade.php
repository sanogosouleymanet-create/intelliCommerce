@php
// Dashboard partiel pour injection par défaut dans main
@endphp

<!-- Statistiques rapides (cartes) -->
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card p-3">
            <h5 class="mb-2"><i class="fa-solid fa-box me-2"></i> Produits</h5>
            <div class="fs-2 fw-bold">{{ $produitsCount ?? $vendeur->produits()->count() }}</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3">
            <h5 class="mb-2"><i class="fa-solid fa-cart-shopping me-2"></i> Commandes</h5>
            <div class="fs-2 fw-bold">{{ $commandesCount ?? ($vendeur->commandes ? $vendeur->commandes->count() : 0) }}</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3">
            <h5 class="mb-2"><i class="fa-solid fa-envelope me-2"></i> Messages</h5>
            <div class="fs-2 fw-bold">{{ $messagesNonLus ?? ($vendeur->messages ? $vendeur->messages->where('Lu',0)->count() : 0) }}</div>
        </div>
    </div>
</div>

<!-- Commandes récentes (pleine largeur) -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card p-3">
            <h5>Commandes Récentes</h5>
            <div class="orders-list mt-2">
                @if(isset($vendeur) && $vendeur->commandes && $vendeur->commandes->count())
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Commande</th>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Total</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vendeur->commandes->sortByDesc('DateCommande')->take(8) as $commande)
                                    <tr>
                                        <td>#C-{{ $commande->idCommande }}</td>
                                        <td>{{ \Carbon\Carbon::parse($commande->DateCommande)->format('d/m/Y H:i') }}</td>
                                        <td>{{ $commande->Client?->Nom ?? '—' }} {{ $commande->Client?->Prenom ?? '' }}</td>
                                        <td>{{ number_format($commande->MontantTotal ?? ($commande->Montant ?? 0), 0, ',', ' ') }} FCFA</td>
                                        <td>{{ $commande->Statut ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-muted">Aucune commande récente.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Top Produits (les plus vendus) -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card p-3">
            <h5>Top Produits</h5>
            @php
                $topVentes = \DB::table('Produitcommande')
                    ->select('Produit_idProduit', \DB::raw('SUM(Quantite) as ventes'))
                    ->when(isset($vendeur) && $vendeur?->idVendeur, function ($q) use ($vendeur) {
                        return $q->whereIn('Produit_idProduit', \App\Models\Produit::where('Vendeur_idVendeur', $vendeur->idVendeur)->pluck('idProduit'));
                    })
                    ->groupBy('Produit_idProduit')
                    ->orderByDesc('ventes')
                    ->take(8)
                    ->get();
            @endphp

            @if($topVentes && $topVentes->count())
                <div class="table-responsive mt-2">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Vendus</th>
                                <th>Prix</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topVentes as $t)
                                @php $p = \App\Models\Produit::find($t->Produit_idProduit); @endphp
                                @if($p)
                                    <tr>
                                        <td>{{ $p->Nom }}</td>
                                        <td>{{ $t->ventes }}</td>
                                        <td>{{ number_format($p->Prix ?? 0, 0, ',', ' ') }} FCFA</td>
                                        <td><a href="/produit/{{ $p->idProduit }}" class="btn btn-sm btn-outline-secondary">Voir</a></td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-muted mt-2">Aucun produit vendu pour le moment.</div>
            @endif
        </div>
    </div>
</div>

