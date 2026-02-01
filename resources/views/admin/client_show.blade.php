<h2>Détail client</h2>

@php /** Partial: admin.client_show */ @endphp

<style>
.admin-client-detail{ background:#fff; padding:18px; border-radius:8px; box-shadow:0 6px 18px rgba(2,6,23,0.04); max-width:900px; }
.admin-client-detail .row{ display:flex; gap:18px; }
.admin-client-avatar{ width:84px; height:84px; border-radius:50%; background:linear-gradient(135deg,#eef2ff,#f6fdff); display:flex; align-items:center; justify-content:center; font-weight:700; color:#0b63ff; font-size:24px; }
.admin-client-meta h3{ margin:0 0 6px 0; }
.admin-client-actions{ margin-top:12px; display:flex; gap:8px; }
</style>

<div class="admin-client-detail">
	<div class="row">
		<div class="admin-client-avatar">{{ strtoupper(substr($client->Nom,0,1) . ($client->Prenom ? substr($client->Prenom,0,1) : '')) }}</div>
		<div class="admin-client-meta">
			<h3>{{ $client->Nom }} {{ $client->Prenom ?? '' }}</h3>
			<div>{{ $client->email ?? '—' }}</div>
			<div>{{ $client->TelClient ?? '—' }}</div>
			<div style="color:#94a3b8;font-size:0.9rem">Inscrit le {{ \Carbon\Carbon::parse($client->DateCreation ?? now())->format('d/m/Y') }}</div>
			<div class="admin-client-actions">
				<button type="button" id="btn-message" class="btn btn-primary" onclick="adminComposeToClient({{ $client->{$client->getKeyName()} }})">Envoyer un message</button>
				<button type="button" id="btn-delete-client" class="btn btn-danger" data-id="{{ $client->{$client->getKeyName()} }}">Supprimer</button>
				<button type="button" id="btn-back" class="btn btn-outline-secondary" onclick="window.adminFetchAndInject('{{ route('admin.clients') }}')">Retour à la liste</button>
			</div>
		</div>
	</div>

</div>

		<script>
		(function attachClientDelete(){
			function makeHandler(btn){
				return function(){
					const id = btn.dataset.id;
					if(!id) return;
					if(!confirm('Confirmer la suppression de ce client ?')) return;
					const url = '/admin/clients/' + encodeURIComponent(id) + '/delete';
					const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
					fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest', 'Accept':'application/json' } })
						.then(r => r.json())
						.then(d => {
							if(d && d.success){
								const clientsUrl = '{{ route('admin.clients') }}';
								if(window.adminFetchAndInject){
									window.adminFetchAndInject(clientsUrl);
								} else {
									try{ location.hash = 'clients'; }catch(e){ window.location.href = clientsUrl; }
								}
							} else {
								alert('Erreur lors de la suppression: ' + (d.message || ''));
							}
						}).catch(e => { console.error(e); alert('Erreur lors de la suppression'); });
				};
			}

			function bindOnce(){
				const btn = document.getElementById('btn-delete-client');
				if(!btn) return false;
				if(btn.__admin_delete_handler) btn.removeEventListener('click', btn.__admin_delete_handler);
				btn.__admin_delete_handler = makeHandler(btn);
				btn.addEventListener('click', btn.__admin_delete_handler);
				return true;
			}

			if(bindOnce()) return;
			let tries = 0;
			const iv = setInterval(function(){
				if(bindOnce()){ clearInterval(iv); return; }
				if(++tries > 20) clearInterval(iv);
			}, 100);
		})();
		</script>
