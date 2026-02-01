<h2>Clients</h2>

@php /** Partial: admin.clients - Vue stylée façon e‑commerce pour la gestion des clients */ @endphp

<style>
/* Styles locaux pour la vue admin clients (responsive, e‑commerce feel) */
.admin-clients-header{ display:flex; gap:12px; align-items:center; justify-content:space-between; margin-bottom:14px; }
.admin-clients-search{ display:flex; gap:8px; align-items:center; }
.admin-clients-search .form-control{ padding:8px 12px; border-radius:8px; border:1px solid #d1d5db; }
.admin-clients-actions{ display:flex; gap:8px; align-items:center; }
.clients-grid{ display:grid; grid-template-columns: repeat(auto-fill,minmax(280px,1fr)); gap:18px; align-items:start; }
.client-card{ background:#fff; border:1px solid #e6edf3; border-radius:10px; padding:14px; box-shadow: 0 4px 12px rgba(2,6,23,0.06); display:flex; gap:12px; align-items:flex-start; min-height:150px; }
.client-avatar{ width:56px; height:56px; border-radius:50%; background:linear-gradient(135deg,#eef2ff,#f6fdff); display:flex; align-items:center; justify-content:center; font-weight:700; color:#0b63ff; font-size:18px; flex-shrink:0; }
.client-meta{ flex:1; min-width:0; display:flex; flex-direction:column; }
.client-meta h4{ margin:0 0 6px 0; font-size:1rem; }
.client-meta p{ margin:0; color:#6b7280; font-size:0.9rem; }
.client-stats{ display:flex; gap:10px; margin-top:8px; align-items:center; }
.chip{ background:#f1f5f9; padding:6px 8px; border-radius:999px; font-size:0.85rem; color:#0b63ff; }
.client-actions{ display:flex; gap:8px; margin-left:auto; align-items:center; }
.client-actions .btn{ padding:6px 8px; font-size:0.85rem; }
td a{ text-decoration:none; color: #007bff; border:1px solid transparent; padding:6px 10px; border-radius:6px; transition: background-color 0.2s , border-color 0.2s; }
td a:hover{ background-color: #f0f0f0; border-color: #d1d5db; }
.clients-empty{ padding:28px; text-align:center; color:#64748b; }
.clients-table{ width:100%; border-collapse:collapse; margin-top:12px; }
.clients-table th, .clients-table td{ text-align:left; padding:10px 12px; border-bottom:1px solid #eef2f6; }
.view-toggle{ background:transparent; border:none; cursor:pointer; color:#0b63ff; }
@media (max-width:640px){ .admin-clients-header{ flex-direction:column; align-items:stretch; gap:10px; } .clients-grid{ grid-template-columns: 1fr; } .client-card{ min-height:auto; } }
</style>

<div class="admin-clients">
	<div class="admin-clients-header">
		<div class="admin-clients-search">
			<input id="adminClientSearch" class="form-control" type="search" placeholder="Rechercher client (nom, email)...">
			<select id="adminClientStatus" class="form-control">
				<option value="">Tous statuts</option>
				<option value="active">Actif</option>
				<option value="disabled">Désactivé</option>
			</select>
			<button id="adminClientClear" class="btn btn-outline-secondary">Réinitialiser</button>
		</div>
	</div>

	@if(isset($clients) && count($clients))
		<table id="clientsTable" class="clients-table" style="display:table; margin-top:12px; background:#fff; border-radius:8px; overflow:hidden;">
			<thead>
				<tr>
					<th>Client</th>
					<th>Email</th>
					<th>Téléphone</th>
					<th>Inscription</th>
					<th style="text-align:right">Actions</th>
				</tr>
			</thead>
			<tbody>
				@foreach($clients as $c)
					<tr data-name="{{ strtolower(trim($c->Nom . ' ' . ($c->Prenom ?? ''))) }}" data-email="{{ strtolower($c->email ?? '') }}">
						<td>{{ $c->Nom }} {{ $c->Prenom ?? '' }}</td>
						<td>{{ $c->email ?? '—' }}</td>
						<td>{{ $c->TelClient ?? '—' }}</td>
						<td>{{ $c->DateCreation ?? '—'  }}</td>
						<td style="text-align:right">
							<a class="btn btn-sm btn-outline-primary" href="/admin/clients/{{ $c->idClient ?? $c->id }}">Voir</a>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	@else
		<div class="clients-empty">Aucun client trouvé.</div>
	@endif

</div>

<script>
// Small client-side filtering & view toggle for the partial (works without page reload)
(() => {
	const search = document.getElementById('adminClientSearch');
	const status = document.getElementById('adminClientStatus');
	const clear = document.getElementById('adminClientClear');
	const grid = document.getElementById('clientsGrid');
	const table = document.getElementById('clientsTable');
	const toggle = document.getElementById('toggleView');

	function filterClients(){
		const q = (search.value || '').trim().toLowerCase();
		const st = (status.value || '');
		if(grid){ Array.from(grid.children).forEach(card => {
			const name = card.dataset.name || '';
			const email = card.dataset.email || '';
			const s = card.dataset.status || 'active';
			const matches = (!q || name.includes(q) || email.includes(q)) && (!st || st === s);
			card.style.display = matches ? '' : 'none';
		}); }
		if(table){ Array.from(table.tBodies[0].rows).forEach(row => {
			const name = row.dataset.name || '';
			const email = row.dataset.email || '';
			const matches = (!q || name.includes(q) || email.includes(q));
			row.style.display = matches ? '' : 'none';
		}); }
	}

	toggle?.addEventListener('click', () => {
		if(!grid || !table) return;
		const showingGrid = grid.style.display !== 'none';
		grid.style.display = showingGrid ? 'none' : 'grid';
		table.style.display = showingGrid ? '' : 'none';
	});
	search?.addEventListener('input', filterClients);
	status?.addEventListener('change', filterClients);
	clear?.addEventListener('click', () => { search.value=''; status.value=''; filterClients(); });
})();
</script>
