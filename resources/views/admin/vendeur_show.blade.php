<div class="main-content">
<h2>Détail vendeur</h2>

@php /** Partial: admin.vendeur_show */ @endphp

<div class="admin-vendeur-detail">
    <div class="row">
        <div class="admin-vendeur-avatar">{{ strtoupper(substr($vendeur->Nom,0,1) . ($vendeur->Prenom ? substr($vendeur->Prenom,0,1) : '')) }}</div>
        <div class="admin-vendeur-meta">
            <h3>{{ $vendeur->Nom }} {{ $vendeur->Prenom ?? '' }}</h3>
            <div><strong>Boutique:</strong> {{ $vendeur->NomBoutique ?? '—' }}</div>
            <div>{{ $vendeur->email ?? '—' }}</div>
            <div>{{ $vendeur->TelVendeur ?? '—' }}</div>
            <div style="color:#94a3b8;font-size:0.9rem">Membre depuis {{ \Carbon\Carbon::parse($vendeur->DateCreation ?? now())->format('d/m/Y') }}</div>
            <div class="admin-vendeur-actions">
                <button type="button" id="btn-message-vendeur" class="btn btn-primary" onclick="(function(){ try{ window.__admin_prefill = { recipient_type: 'single', recipient: 'vendeur:'+{{ $vendeur->{$vendeur->getKeyName()} }} }; window.adminFetchAndInject('{{ route('admin.messages') }}'); }catch(e){ console.warn(e); } })()">Envoyer un message</button>
                <button type="button" id="btn-delete-vendeur" class="btn btn-danger" data-id="{{ $vendeur->{$vendeur->getKeyName()} }}">Supprimer</button>
                <button type="button" id="btn-back-vendeur" class="btn btn-outline-secondary" onclick="window.adminFetchAndInject('{{ route('admin.vendeurs') }}')">Retour à la liste</button>
            </div>
        </div>
    </div>

</div>
</div>
