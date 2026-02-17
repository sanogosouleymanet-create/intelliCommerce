<div class="main-content">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<h2>Détail client</h2>

	@php /** Partial: admin.client_show */ @endphp

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
					<button type="button" id="btn-back" class="btn btn-outline-secondary" onclick="window.adminFetchAndInject('{{ route('admin.clients') }}')">Retour à la liste</button>
				</div>
			</div>
		</div>

	</div>
</div>
