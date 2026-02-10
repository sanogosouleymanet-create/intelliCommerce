@php /** Partial: vendeurs.client_show */ @endphp

<style>
.vendeur-client-detail{ background:#fff; padding:18px; border-radius:8px; box-shadow:0 6px 18px rgba(2,6,23,0.04); max-width:900px; }
.vendeur-client-detail .row{ display:flex; gap:18px; }
.vendeur-client-avatar{ width:84px; height:84px; border-radius:50%; background:linear-gradient(135deg,#eef2ff,#f6fdff); display:flex; align-items:center; justify-content:center; font-weight:700; color:#0b63ff; font-size:24px; }
.vendeur-client-meta h3{ margin:0 0 6px 0; }
.vendeur-client-actions{ margin-top:12px; display:flex; gap:8px; }
</style>

@if($client)
<div class="vendeur-client-detail">
	<div class="row">
		<div class="vendeur-client-avatar">{{ strtoupper( ($client->Nom ? substr($client->Nom,0,1) : '?') . ($client->Prenom ? substr($client->Prenom,0,1) : '') ) }}</div>
		<div class="vendeur-client-meta">
			<h3>{{ $client->Nom }} {{ $client->Prenom ?? '' }}</h3>
			<div>{{ $client->email ?? '—' }}</div>
			<div>{{ $client->TelClient ?? '—' }}</div>
			<div style="color:#94a3b8;font-size:0.9rem">Inscrit le {{ \Carbon\Carbon::parse($client->DateCreation ?? now())->format('d/m/Y') }}</div>
			<div class="vendeur-client-actions">
				<button type="button" id="btn-message" class="btn btn-primary" onclick="vendeurComposeToClient({{ $client->{$client->getKeyName()} }})">Envoyer un message</button>
				<button type="button" id="btn-back" class="btn btn-outline-secondary" onclick="history.back()">Retour à la liste</button>
			</div>
		</div>
	</div>

</div>
@endif

<script>
(function(){
    // Function to compose message to client
    window.vendeurComposeToClient = function(clientId) {
        // Assuming there's a message composition modal or similar in the vendeur interface
        // For now, redirect to messages page with client pre-selected
        const messagesUrl = '{{ route('vendeur.messages') }}';
        window.location.href = messagesUrl + '?compose=client:' + clientId;
    };
})();
</script>
