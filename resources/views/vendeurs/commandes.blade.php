@php
    // Espace Vendeur — Commandes
    // Variables attendues : $vendeur, $commandes
@endphp

<section class="container vendeurs-orders">
    <div class="card p-3 mb-3">
        <div class="d-flex align-items-center">
            <h4 class="mb-0">Commandes</h4>
            <div class="ms-auto d-flex gap-2">
                <input id="searchOrders" placeholder="Rechercher par #commande ou nom client" class="form-control form-control-sm" style="min-width:240px">
                <select id="filterStatut" class="form-select form-select-sm" style="min-width:160px">
                    <option value="">Tous les statuts</option>
                    <option value="En attente">En attente</option>
                    <option value="En cours">En cours</option>
                    <option value="Livrée">Livrée</option>
                    <option value="Annulée">Annulée</option>
                </select>
            </div>
        </div>
    </div>

    <div class="card p-3">
        <div class="table-responsive">
            @php
                $list = $commandes ?? ($vendeur->commandes ?? collect());
            @endphp

            @if($list && $list->count())
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Commande</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Total</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="ordersTable">
                        @foreach($list->sortByDesc('DateCommande') as $commande)
                            @php
                                $total = $commande->MontantTotal ?? $commande->MontanTotal ?? $commande->Montant ?? 0;
                                $date = $commande->DateCommande ? \Carbon\Carbon::parse($commande->DateCommande)->format('d/m/Y H:i') : '-';
                            @endphp
                            <tr data-id="{{ $commande->idCommande }}">
                                <td>#C-{{ $commande->idCommande }}</td>
                                <td>{{ $date }}</td>
                                <td>{{ $commande->Client?->Nom ?? '—' }} {{ $commande->Client?->Prenom ?? '' }}</td>
                                <td>{{ number_format($total, 0, ',', ' ') }} FCFA</td>
                                <td><span class="badge bg-secondary statut">{{ $commande->Statut ?? '—' }}</span></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary btn-view" data-id="{{ $commande->idCommande }}">Voir</button>
                                    <button class="btn btn-sm btn-outline-success btn-mark" data-id="{{ $commande->idCommande }}">Marquer livrée</button>
                                </td>
                            </tr>
                            <tr class="order-details d-none" data-details-for="{{ $commande->idCommande }}">
                                <td colspan="6">
                                    <div class="small text-muted">Détails de la commande :</div>
                                    @if(method_exists($commande, 'Produit') && $commande->Produit && $commande->Produit->count())
                                        <ul>
                                            @foreach($commande->Produit as $p)
                                                <li>{{ $p->Nom ?? 'Produit' }} × {{ $p->pivot->Quantite ?? 1 }} — {{ number_format($p->Prix ?? 0, 0, ',', ' ') }} FCFA</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="text-muted">Aucun détail produit disponible.</div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-muted">Aucune commande trouvée.</div>
            @endif
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const search = document.getElementById('searchOrders');
    const filter = document.getElementById('filterStatut');

    function applyFilters(){
        const q = (search.value || '').toLowerCase();
        const s = (filter.value || '').toLowerCase();
        document.querySelectorAll('#ordersTable tr[data-id]').forEach(tr => {
            const txt = tr.textContent.toLowerCase();
            const statut = tr.querySelector('.statut')?.textContent.toLowerCase() || '';
            tr.style.display = ((q === '' || txt.includes(q)) && (s === '' || statut.includes(s))) ? '' : 'none';
        });
    }

    search?.addEventListener('input', applyFilters);
    filter?.addEventListener('change', applyFilters);

    // Toggle details
    document.querySelectorAll('.btn-view').forEach(btn => btn.addEventListener('click', function(){
        const id = this.dataset.id; const details = document.querySelector('.order-details[data-details-for="'+id+'"]');
        if(details) details.classList.toggle('d-none');
    }));

    // Mock mark as delivered (updates UI only)
    document.querySelectorAll('.btn-mark').forEach(btn => btn.addEventListener('click', async function(){
        const id = this.dataset.id; const tr = document.querySelector('tr[data-id="'+id+'"]');
        try{
            // Attempt optimistic UI change — server endpoint may be added later
            tr.querySelector('.statut').textContent = 'Livrée';
            tr.querySelector('.statut').classList.remove('bg-secondary');
            tr.querySelector('.statut').classList.add('bg-success');
            this.disabled = true;
        }catch(e){ console.error(e); }
    }));
});
</script>
