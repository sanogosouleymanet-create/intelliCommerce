<div class="main-content">
<h2>Détail vendeur</h2>

@php
    /** Partial: admin.vendeur_show */
    $isBlocked = !empty($vendeur->Bloque);
    $vendeurId = $vendeur->idVendeur ?? $vendeur->{$vendeur->getKeyName()};
@endphp

<div class="admin-vendeur-detail">
    <div class="row">
        <div class="admin-vendeur-avatar">{{ strtoupper(substr($vendeur->Nom,0,1) . ($vendeur->Prenom ? substr($vendeur->Prenom,0,1) : '')) }}</div>
        <div class="admin-vendeur-meta">
            <h3>{{ $vendeur->Nom }} {{ $vendeur->Prenom ?? '' }}</h3>
            @if($isBlocked)
                <div class="alert alert-warning py-2 mb-2" style="font-size:0.9rem;">Ce compte est bloqué. Certaines actions sont limitées.</div>
            @endif
            <div><strong>Boutique:</strong> {{ $vendeur->NomBoutique ?? '—' }}</div>
            <div>{{ $vendeur->email ?? '—' }}</div>
            <div>{{ $vendeur->TelVendeur ?? '—' }}</div>
            <div style="color:#94a3b8;font-size:0.9rem">Membre depuis {{ \Carbon\Carbon::parse($vendeur->DateCreation ?? now())->format('d/m/Y') }}</div>
            <div class="admin-vendeur-actions">
                <button type="button" id="btn-message-vendeur" class="btn btn-primary" onclick="(function(){ try{ window.__admin_prefill = { recipient_type: 'single', recipient: 'vendeur:'+{{ $vendeurId }} }; window.adminFetchAndInject('{{ route('admin.messages') }}'); }catch(e){ console.warn(e); } })()">Envoyer un message</button>
                <button type="button" id="btn-block-vendeur" class="btn {{ $isBlocked ? 'btn-success' : 'btn-danger' }}" data-id="{{ $vendeurId }}" data-blocked="{{ $isBlocked ? '1' : '0' }}">{{ $isBlocked ? 'Débloquer' : 'Bloquer' }}</button>
                <button type="button" id="btn-back-vendeur" class="btn btn-outline-secondary" onclick="window.adminFetchAndInject('{{ route('admin.vendeurs') }}')">Retour à la liste</button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var btn = document.getElementById('btn-block-vendeur');
    if (!btn) return;
    var csrf = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    btn.addEventListener('click', function() {
        var id = btn.getAttribute('data-id');
        var blocked = btn.getAttribute('data-blocked') === '1';
        var url = blocked
            ? '{{ url("/admin/messages/unblock/vendeur") }}/' + id
            : '{{ url("/admin/messages/block/vendeur") }}/' + id;
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
            if (data.success !== false && !data.error) {
                btn.setAttribute('data-blocked', blocked ? '0' : '1');
                btn.textContent = blocked ? 'Bloquer' : 'Débloquer';
                btn.className = 'btn ' + (blocked ? 'btn-danger' : 'btn-success');
                var alertEl = document.querySelector('.admin-vendeur-detail .alert-warning');
                if (blocked) {
                    if (alertEl) alertEl.remove();
                } else {
                    if (!alertEl) {
                        var meta = document.querySelector('.admin-vendeur-meta');
                        if (meta) {
                            var div = document.createElement('div');
                            div.className = 'alert alert-warning py-2 mb-2';
                            div.style.fontSize = '0.9rem';
                            div.textContent = 'Ce compte est bloqué. Certaines actions sont limitées.';
                            meta.insertBefore(div, meta.firstElementChild.nextElementSibling);
                        }
                    }
                }
            } else {
                alert(data.message || data.error || 'Erreur lors de l\'opération.');
            }
        })
        .catch(function() { alert('Erreur réseau.'); })
        .finally(function() { btn.disabled = false; });
    });
})();
</script>
</div>
