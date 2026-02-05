@php
    // Espace Vendeur — Messages
    // Variables attendues : $vendeur, $messages
@endphp

<section class="container vendeurs-messages">
    <div class="card p-3 mb-3 d-flex align-items-center">
        <div class="w-100 d-flex align-items-center">
            <h4 class="mb-0">Messages</h4>
            <div class="ms-auto small text-muted">{{ isset($messages) ? $messages->count() : ($vendeur->messages ? $vendeur->messages->count() : 0) }} messages</div>
        </div>
    </div>

    <div class="card p-3">
        @php $list = $messages ?? ($vendeur->messages ?? collect()); @endphp
        @if($list && $list->count())
            <div class="list-group">
                @foreach($list as $m)
                    <div class="list-group-item d-flex align-items-start" data-id="{{ $m->idMessage }}">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <div><strong>{{ $m->client?->Nom ?? 'Client' }} {{ $m->client?->Prenom ?? '' }}</strong> <span class="text-muted small">— {{ \Carbon\Carbon::parse($m->DateEnvoi)->format('d/m/Y H:i') }}</span></div>
                                <div class="small">
                                    @if(isset($m->Statut) && $m->Statut == 0)
                                        <span class="badge bg-warning">Non lu</span>
                                    @elseif(isset($m->Lu) && !$m->Lu)
                                        <span class="badge bg-warning">Non lu</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-2">{{ \Illuminate\Support\Str::limit($m->Contenu, 240) }}</div>
                            <div class="mt-2 small text-muted">ID: {{ $m->idMessage }}</div>
                        </div>
                        <div class="ms-3 text-end">
                            <button class="btn btn-sm btn-outline-primary btn-open">Ouvrir</button>
                            <button class="btn btn-sm btn-outline-success btn-mark" data-id="{{ $m->idMessage }}">Marquer lu</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-muted">Aucun message reçu.</div>
        @endif
    </div>
</section>

<script>
// Simple AJAX to mark message as read
window.CSRF_TOKEN = '{{ csrf_token() }}';

document.querySelectorAll('.btn-mark').forEach(b => b.addEventListener('click', async function(){
    const id = this.dataset.id; if(!id) return;
    try{
        const res = await fetch('/vendeur/messages/' + id + '/lire', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN },
            body: JSON.stringify({})
        });
        if(res.ok){
            this.disabled = true; this.textContent = 'Lu';
            const badge = this.closest('.list-group-item').querySelector('.badge'); if(badge){ badge.classList.remove('bg-warning'); badge.classList.add('bg-secondary'); badge.textContent = 'Lu'; }
        } else {
            console.error('Erreur');
        }
    }catch(e){ console.error(e); }
}));

// Open message in a simple modal (native alert fallback)
document.querySelectorAll('.btn-open').forEach(b => b.addEventListener('click', function(){
    const item = this.closest('.list-group-item');
    const id = item.dataset.id; const content = item.querySelector('div.mt-2')?.textContent || '';
    const title = item.querySelector('strong')?.textContent || 'Message';
    // simple inline modal
    const modal = document.createElement('div'); modal.className = 'modal'; modal.style.cssText = 'position:fixed;left:0;top:0;width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.4);z-index:9999';
    modal.innerHTML = `<div class="card p-3" style="max-width:720px;width:95%"><h5>${title}</h5><p>${content}</p><div class="text-end"><button class="btn btn-sm btn-primary btn-close">Fermer</button></div></div>`;
    document.body.appendChild(modal);
    modal.querySelector('.btn-close').addEventListener('click', function(){ document.body.removeChild(modal); });
}));
</script>
