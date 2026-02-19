<div class="main-content">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<h2>Détail client</h2>

	@php
		/** Partial: admin.client_show */
		$isBlocked = !empty($client->Bloque);
		$clientId = $client->{$client->getKeyName()};
	@endphp

	<div class="admin-client-detail">
		<div class="row">
			<div class="admin-client-avatar">{{ strtoupper(substr($client->Nom,0,1) . ($client->Prenom ? substr($client->Prenom,0,1) : '')) }}</div>
			<div class="admin-client-meta">
				<h3>{{ $client->Nom }} {{ $client->Prenom ?? '' }}</h3>
				@if($isBlocked)
					<div class="alert alert-warning py-2 mb-2" style="font-size:0.9rem;">Ce compte est bloqué. Certaines actions sont limitées.</div>
				@endif
				<div>{{ $client->email ?? '—' }}</div>
				<div>{{ $client->TelClient ?? '—' }}</div>
				<div style="color:#94a3b8;font-size:0.9rem">Inscrit le {{ \Carbon\Carbon::parse($client->DateCreation ?? now())->format('d/m/Y') }}</div>
				<div class="admin-client-actions">
					<button type="button" id="btn-message" class="btn btn-primary" onclick="adminComposeToClient({{ $clientId }})">Envoyer un message</button>
					<button type="button" id="btn-block-client" class="btn {{ $isBlocked ? 'btn-success' : 'btn-danger' }}" data-id="{{ $clientId }}" data-blocked="{{ $isBlocked ? '1' : '0' }}">{{ $isBlocked ? 'Débloquer' : 'Bloquer' }}</button>
					<button type="button" id="btn-back" class="btn btn-outline-secondary" onclick="window.adminFetchAndInject('{{ route('admin.clients') }}')">Retour à la liste</button>
				</div>
			</div>
		</div>

	</div>
</div>

<script>
(function() {
	var btn = document.getElementById('btn-block-client');
	if (!btn) return;
	var csrf = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content');
	btn.addEventListener('click', function() {
		var id = btn.getAttribute('data-id');
		var blocked = btn.getAttribute('data-blocked') === '1';
		var url = blocked
			? '{{ url("/admin/messages/unblock/client") }}/' + id
			: '{{ url("/admin/messages/block/client") }}/' + id;
		btn.disabled = true;
		fetch(url, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': (typeof csrf !== 'undefined' && csrf) ? csrf : '{{ csrf_token() }}',
				'X-Requested-With': 'XMLHttpRequest',
				'Accept': 'application/json'
			}
		})
		.then(function(r) { return r.json().catch(function() { return {}; }); })
		.then(function(data) {
			if (data && (data.success === true || (data.success !== false && !data.error))) {
				// Toggle UI state
				var newBlocked = !blocked;
				btn.setAttribute('data-blocked', newBlocked ? '1' : '0');
				btn.textContent = newBlocked ? 'Débloquer' : 'Bloquer';
				btn.className = 'btn ' + (newBlocked ? 'btn-success' : 'btn-danger');
				var alertEl = document.querySelector('.admin-client-detail .alert-warning');
				if (newBlocked) {
					if (!alertEl) {
						var meta = document.querySelector('.admin-client-meta');
						if (meta) {
							var div = document.createElement('div');
							div.className = 'alert alert-warning py-2 mb-2';
							div.style.fontSize = '0.9rem';
							div.textContent = 'Ce compte est bloqué. Certaines actions sont limitées.';
							meta.insertBefore(div, meta.firstElementChild ? meta.firstElementChild.nextElementSibling : meta.firstChild);
						}
					}
				} else {
					if (alertEl) alertEl.remove();
				}
			} else {
				alert((data && (data.message || data.error)) ? (data.message || data.error) : 'Erreur lors de l\'opération.');
			}
		})
		.catch(function() { alert('Erreur réseau.'); })
		.finally(function() { btn.disabled = false; });
	});
})();
</script>
