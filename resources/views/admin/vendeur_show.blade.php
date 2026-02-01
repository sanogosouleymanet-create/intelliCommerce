<h2>Détail vendeur</h2>

@php /** Partial: admin.vendeur_show */ @endphp

<style>
.admin-vendeur-detail{ background:#fff; padding:18px; border-radius:8px; box-shadow:0 6px 18px rgba(2,6,23,0.04); max-width:900px; }
.admin-vendeur-detail .row{ display:flex; gap:18px; }
.admin-vendeur-avatar{ width:84px; height:84px; border-radius:50%; background:linear-gradient(135deg,#fff4e6,#fff8f2); display:flex; align-items:center; justify-content:center; font-weight:700; color:#c0392b; font-size:24px; }
.admin-vendeur-meta h3{ margin:0 0 6px 0; }
.admin-vendeur-actions{ margin-top:12px; display:flex; gap:8px; }
</style>

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

<script>
(function attachVendeurDelete(){
    function makeHandler(btn){
        return function(){
            const id = btn.dataset.id;
            if(!id) return;
            if(!confirm('Confirmer la suppression de ce vendeur ?')) return;
            const url = '/admin/vendeurs/' + encodeURIComponent(id) + '/delete';
            const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
            fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest', 'Accept':'application/json' } })
                .then(r => r.json())
                .then(d => {
                    if(d && d.success){
                        const vendeursUrl = '{{ route('admin.vendeurs') }}';
                        if(window.adminFetchAndInject){
                            window.adminFetchAndInject(vendeursUrl);
                        } else {
                            try{ location.hash = 'vendeurs'; }catch(e){ window.location.href = vendeursUrl; }
                        }
                    } else {
                        alert('Erreur lors de la suppression: ' + (d.message || ''));
                    }
                }).catch(e => { console.error(e); alert('Erreur lors de la suppression'); });
        };
    }

    function bindOnce(){
        const btn = document.getElementById('btn-delete-vendeur');
        if(!btn) return false;
        if(btn.__admin_delete_handler) btn.removeEventListener('click', btn.__admin_delete_handler);
        btn.__admin_delete_handler = makeHandler(btn);
        btn.addEventListener('click', btn.__admin_delete_handler);
        return true;
    }

    // Try to bind immediately; if the partial was injected after this script ran,
    // poll briefly until the element appears (covers SPA-injected content).
    if(bindOnce()) return;
    let tries = 0;
    const iv = setInterval(function(){
        if(bindOnce()){ clearInterval(iv); return; }
        if(++tries > 20) clearInterval(iv);
    }, 100);
})();
</script>
