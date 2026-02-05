@php
    // Espace Vendeur — Clients
    // Variables attendues : $vendeur, $clients
@endphp

<section class="container vendeurs-clients">
    <div class="card p-3 mb-3 d-flex align-items-center">
        <div class="w-100 d-flex align-items-center gap-2">
            <h4 class="mb-0">Clients</h4>
            <input id="searchClients" class="form-control form-control-sm ms-auto" placeholder="Rechercher un client (nom, email, téléphone)" style="max-width:360px">
        </div>
    </div>

    <div class="card p-3">
        @php $list = $clients ?? ($vendeur->clients ?? collect()); @endphp
        @if($list && $list->count())
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Commandes</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="clientsTable">
                        @foreach($list as $c)
                            <tr>
                                <td>{{ $c->Nom ?? '—' }} {{ $c->Prenom ?? '' }}</td>
                                <td>{{ $c->email ?? '—' }}</td>
                                <td>{{ $c->TelClient ?? '—' }}</td>
                                <td>{{ $c->commandes ? $c->commandes->count() : 0 }}</td>
                                <td class="text-end"><a href="#" class="btn btn-sm btn-outline-secondary">Voir</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-muted">Aucun client à afficher.</div>
        @endif
    </div>
</section>

<script>
document.getElementById('searchClients')?.addEventListener('input', function(){
    const q = (this.value || '').toLowerCase();
    document.querySelectorAll('#clientsTable tr').forEach(tr => {
        const txt = tr.textContent.toLowerCase();
        tr.style.display = q === '' || txt.includes(q) ? '' : 'none';
    });
});
</script>
