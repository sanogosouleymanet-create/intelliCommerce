<h2>Vendeurs</h2>

@php /** Partial: admin.vendeurs - Vue stylée pour la gestion des vendeurs */ @endphp

<style>
.admin-vendeurs-header{ display:flex; gap:12px; align-items:center; justify-content:space-between; margin-bottom:14px; }
.admin-vendeurs-search{ display:flex; gap:8px; align-items:center; }
.admin-vendeurs-search .form-control{ padding:8px 12px; border-radius:8px; border:1px solid #d1d5db; }
.vendeurs-grid{ display:grid; grid-template-columns: repeat(auto-fill,minmax(280px,1fr)); gap:18px; align-items:start; }
.vendeur-card{ background:#fff; border:1px solid #e6edf3; border-radius:10px; padding:14px; box-shadow: 0 4px 12px rgba(2,6,23,0.06); display:flex; gap:12px; align-items:flex-start; min-height:150px; }
.vendeur-avatar{ width:56px; height:56px; border-radius:50%; background:linear-gradient(135deg,#ffefe6,#fff8f2); display:flex; align-items:center; justify-content:center; font-weight:700; color:#c0392b; font-size:18px; flex-shrink:0; }
.vendeur-meta{ flex:1; min-width:0; display:flex; flex-direction:column; }
.vendeur-meta h4{ margin:0 0 6px 0; font-size:1rem; }
.vendeur-meta p{ margin:0; color:#6b7280; font-size:0.9rem; }
.vendeur-actions{ display:flex; gap:8px; margin-left:auto; align-items:center; }
.vendeurs-empty{ padding:28px; text-align:center; color:#64748b; }
.vendeurs-table{ width:100%; border-collapse:collapse; margin-top:12px; }
.vendeurs-table th, .vendeurs-table td{ text-align:left; padding:10px 12px; border-bottom:1px solid #eef2f6; }
.view-toggle{ background:transparent; border:none; cursor:pointer; color:#0b63ff; }
@media (max-width:640px){ .admin-vendeurs-header{ flex-direction:column; align-items:stretch; gap:10px; } .vendeurs-grid{ grid-template-columns: 1fr; } .vendeur-card{ min-height:auto; } }
td a{ text-decoration:none; color: #007bff; border:1px solid transparent; padding:6px 10px; border-radius:6px; transition: background-color 0.2s , border-color 0.2s; }
td a:hover{ background-color: #f0f0f0; border-color: #d1d5db; }
</style>

<div class="admin-vendeurs">
    <div class="admin-vendeurs-header">
        <div class="admin-vendeurs-search">
            <input id="adminVendeurSearch" class="form-control" type="search" placeholder="Rechercher vendeur (nom, boutique)">
            <button id="adminVendeurClear" class="btn btn-outline-secondary">Réinitialiser</button>
        </div>
    </div>

    @if(isset($vendeurs) && count($vendeurs))
        <table id="vendeursTable" class="vendeurs-table" style="display:table; margin-top:12px; background:#fff; border-radius:8px; overflow:hidden;">
            <thead>
                <tr>
                    <th>Vendeur</th>
                    <th>Boutique</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Inscription</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vendeurs as $v)
                    <tr data-name="{{ strtolower(trim($v->Nom . ' ' . ($v->Prenom ?? ''))) }}" data-email="{{ strtolower($v->email ?? '') }}" data-boutique="{{ strtolower(trim($v->NomBoutique ?? '')) }}">
                        <td>{{ $v->Nom }} {{ $v->Prenom ?? '' }}</td>
                        <td>{{ $v->NomBoutique ?? '—' }}</td>
                        <td>{{ $v->email ?? '—' }}</td>
                        <td>{{ $v->TelVendeur ?? '—' }}</td>
                        <td>{{ $v->DateCreation ?? '—'  }}</td>
                        <td style="text-align:right">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.vendeurs') . '/' . ($v->idVendeur ?? $v->id) }}">Voir</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="vendeurs-empty">Aucun vendeur trouvé.</div>
    @endif

</div>

<script>
(function(){
    const search = document.getElementById('adminVendeurSearch');
    const clear = document.getElementById('adminVendeurClear');
    const table = document.getElementById('vendeursTable');

    function filter(){
        const q = (search.value || '').trim().toLowerCase();
        if(table){ Array.from(table.tBodies[0].rows).forEach(row => {
            const name = row.dataset.name || '';
            const email = row.dataset.email || '';
            const boutique = (row.dataset.boutique || '').toLowerCase();
            const matches = !q || name.includes(q) || email.includes(q) || boutique.includes(q);
            row.style.display = matches ? '' : 'none';
        }); }
    }
    search?.addEventListener('input', filter);
    clear?.addEventListener('click', ()=>{ search.value=''; filter(); });
})();
</script>
