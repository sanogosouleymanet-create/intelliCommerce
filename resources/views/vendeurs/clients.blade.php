@php
    // Espace Vendeur — Clients
    // Variables attendues : $vendeur, $clients
@endphp

<style>
    /* Espacement et style pour la barre de recherche
       - input sans coins arrondis
       - bouton avec bordure visible sur tous les côtés */
    .vendeurs-clients .input-group { gap: .5rem; display: inline-flex; align-items: center; }
    /* Make the search area visually transparent and nudge it to the right */
    .vendeurs-clients > .card.p-3.mb-3 { position: relative; background: transparent !important; box-shadow: none !important; border: none !important; }
    /* stronger target to override theme: remove white background around input and move it toward the right corner */
    .vendeurs-clients > .card.p-3.mb-3 .input-group { background: transparent !important; padding: 0 !important; margin-right: 0 !important; transform: translateX(36px); }
    .vendeurs-clients > .card.p-3.mb-3 .input-group .form-control { border-radius: 0 !important; background: transparent !important; border: 1px solid rgba(0,0,0,0.12) !important; box-shadow: none !important; color: #000; }
    .vendeurs-clients .input-group .form-control::placeholder { color: rgba(0,0,0,0.45); }
    .vendeurs-clients .input-group .btn {
        border-radius: .25rem;
        padding: .375rem .6rem;
        border: 1px solid #0d6efd;
        background-color: #0d6efd;
        color: #fff;
        box-shadow: none;
    }
</style>

<section class="container vendeurs-clients">
    @php
        $raw = $clients ?? ($vendeur->clients ?? collect());
        $list = collect($raw)->filter(function($c){
            // Keep client only if they have at least one commande
            if(isset($c->commandes_count)) return $c->commandes_count > 0;
            if($c->commandes instanceof \Illuminate\Support\Collection) return $c->commandes->count() > 0;
            // Fallback: try to count via relation method if exists
            if(method_exists($c, 'commandes')){
                try{ return $c->commandes()->count() > 0; } catch (\Throwable $e){ }
            }
            return false;
        });
    @endphp
    <div class="card p-3 mb-3 d-flex align-items-center">
        <div class="w-100 d-flex align-items-center gap-2">
            
            <div class="input-group ms-auto" style="max-width:360px">
                <input id="searchClients" class="form-control form-control-sm" placeholder="Rechercher un client (nom, email, téléphone)">
                <button id="btnSearchClients" class="btn btn-sm btn-primary">Rechercher</button>
            </div>
        </div>
    </div>

    

    <div class="card p-3">
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
(function(){
    const input = document.getElementById('searchClients');
    const btn = document.getElementById('btnSearchClients');
    function performSearch(q){
        q = (q || '').toLowerCase();
        document.querySelectorAll('#clientsTable tr').forEach(tr => {
            const txt = tr.textContent.toLowerCase();
            tr.style.display = q === '' || txt.includes(q) ? '' : 'none';
        });
    }

    input?.addEventListener('input', function(){ performSearch(this.value); });
    btn?.addEventListener('click', function(e){ e.preventDefault(); performSearch(input.value); });
    // trigger search on Enter key
    input?.addEventListener('keydown', function(e){ if(e.key === 'Enter'){ e.preventDefault(); performSearch(this.value); } });
})();
</script>
