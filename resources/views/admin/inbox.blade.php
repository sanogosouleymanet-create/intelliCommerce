
<div class="card" style="display:flex;height:80vh;overflow:hidden;">
    <!-- Sidebar for conversations -->
    <div id="conversations-sidebar" style="width:30%;border-right:1px solid #eee;padding:12px;height:100%;overflow-y:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <h4>Conversations</h4>
            <button id="btn-compose" class="btn btn-sm btn-primary">Composer</button>
        </div>
        @if(empty($conversations))
            <p>Aucune conversation pour le moment.</p>
        @else
            <ul id="conversations-list" style="list-style:none;padding:0;">
                @foreach($conversations as $key => $conv)
                    <li class="conversation-item" data-type="{{ $conv['senderType'] }}" data-id="{{ $conv['sender']->idClient ?? $conv['sender']->idVendeur ?? $conv['sender']->idAdmi }}" data-name="{{ $conv['sender']->Nom }} {{ $conv['sender']->Prenom }}" style="padding:8px;border-bottom:1px solid #f0f0f0;cursor:pointer;">
                        <div style="display:flex;justify-content:space-between;">
                            <strong>{{ $conv['sender']->Nom }} {{ $conv['sender']->Prenom }}</strong>
                            <div>
                                
                                <button class="btn btn-sm delete-conv" data-type="{{ $conv['senderType'] }}" data-id="{{ $conv['sender']->idClient ?? $conv['sender']->idVendeur ?? $conv['sender']->idAdmi }}" style="margin-left:8px;color:red;" title="Supprimer">&times;</button>
                            </div>
                        </div>
                        <small style="color:#6b7280;">{{ \Carbon\Carbon::parse($conv['lastMessageDate'])->format('d/m H:i') }}</small>
                        <!--<div style="color:#6b7280;font-size:0.9rem;">{{ Str::limit($conv['lastMessage']->Contenu ?? '', 50) }}</div>-->
                        @if($conv['unreadCount'] > 0)
                            <span class="badge badge-danger">{{ $conv['unreadCount'] }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <!-- Chat area -->
    <div id="chat-area" style="width:70%;display:flex;flex-direction:column;height:100%;">
        <div id="chat-header" style="padding:12px;border-bottom:1px solid #eee;display:none;flex-shrink:0;">
            <h5 id="chat-title">Sélectionnez une conversation</h5>
        </div>
        <div id="messages-container" style="flex:1;padding:12px;display:none;overflow-y:auto;display:flex;flex-direction:column;">
            <!-- Messages will be loaded here -->
        </div>
        <div id="reply-area" style="padding:12px;border-top:1px solid #eee;display:none;flex-shrink:0;">
            <div style="display:flex;gap:8px;">
                <textarea id="reply-input" placeholder="Tapez votre message..." style="flex:1;padding:8px;border:1px solid #ddd;border-radius:4px;resize:none;" rows="2"></textarea>
                <button id="btn-send-reply" class="btn btn-primary">Envoyer</button>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    const main = document.getElementById('main-content') || document.querySelector('main');
    const csrf = '{{ csrf_token() }}';
    let currentConversation = null;

    function openCompose(prefill) {
        const modal = document.createElement('div');
        modal.innerHTML = `
        <div id="admin-compose-modal" style="position:fixed;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(2,6,23,0.5);z-index:9999">
          <div style="background:#fff;padding:18px;border-radius:8px;max-width:720px;width:100%">
            <h3>Composer un message</h3>
            <div style="margin-top:8px">
                            <div style="display:flex;gap:8px;margin-bottom:8px">
                                <select id="compose-recipient-type" style="flex:1;padding:8px;border:1px solid #ddd;border-radius:4px">
                                    <option value="single">-- Destinataire spécifique --</option>
                                    <option value="clients">Tous les clients</option>
                                    <option value="vendeurs">Tous les vendeurs</option>
                                    <option value="admins">Tous les administrateurs</option>
                                    <option value="all">Tous les utilisateurs</option>
                                </select>
                                <select id="compose-recipient" style="flex:2;padding:8px;border:1px solid #ddd;border-radius:4px">
                                    <option value="">-- Choisir un destinataire (client / vendeur / admin) --</option>
                                    <optgroup label="Clients">
                                        @foreach($clients as $c)
                                            <option value="client:{{ $c->idClient }}">{{ $c->Nom }} {{ $c->Prenom }} ({{ $c->email }})</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Vendeurs">
                                        @foreach($vendeurs as $v)
                                            <option value="vendeur:{{ $v->idVendeur }}">{{ $v->Nom }} {{ $v->Prenom }} ({{ $v->email ?? '' }})</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Administrateurs">
                                        @foreach($admins as $a)
                                            <option value="admin:{{ $a->idAdmi }}">{{ $a->Nom }} {{ $a->Prenom }} ({{ $a->email }})</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
              <input id="compose-subject" placeholder="Sujet" style="width:100%;padding:8px;margin-bottom:8px;border:1px solid #ddd;border-radius:4px" />
              <textarea id="compose-body" placeholder="Message" style="width:100%;height:140px;padding:8px;border:1px solid #ddd;border-radius:4px"></textarea>
            </div>
            <div style="margin-top:12px;display:flex;gap:8px;justify-content:flex-end">
              <button id="compose-cancel" class="btn btn-outline-secondary" type="button">Annuler</button>
              <button id="compose-send" class="btn btn-primary" type="button">Envoyer</button>
            </div>
          </div>
        </div>`;
        document.body.appendChild(modal.firstElementChild);
        const container = document.getElementById('admin-compose-modal');
        const rt = container.querySelector('#compose-recipient-type');
        const rr = container.querySelector('#compose-recipient');
        const subj = container.querySelector('#compose-subject');
        const body = container.querySelector('#compose-body');

        function applyPrefill(pref) {
            if(!pref) return;
            if(pref.recipient_type) rt.value = pref.recipient_type;
            if(pref.recipient) rr.value = pref.recipient;
            if(pref.subject) subj.value = pref.subject;
            if(pref.body) body.value = pref.body;
            // disable recipient select if not single
            rr.disabled = (rt.value !== 'single');
        }

        container.querySelector('#compose-cancel').addEventListener('click', () => container.remove());
        container.querySelector('#compose-send').addEventListener('click', async function(){
            const recipient_type = rt.value;
            const recipient = rr.value;
            const subject = subj.value;
            const bodyVal = body.value;
            if(!bodyVal){ alert('Saisissez un message'); return; }
            if(recipient_type === 'single' && !recipient){ alert('Sélectionnez un destinataire ou changez le type de destinataire'); return; }
            this.disabled = true;
            try{
                const payload = { recipient_type, recipient: recipient || null, subject, body: bodyVal };
                const res = await fetch('{{ route('admin.messages.send') }}', {
                    method: 'POST',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest'},
                    body: JSON.stringify(payload)
                });
                const json = await res.json().catch(()=>null);
                if(res.ok){ alert('Message envoyé'); container.remove(); location.reload(); }
                else { alert((json && json.message) ? json.message : 'Erreur lors de l envoi'); }
            }catch(e){ alert('Erreur réseau'); }
            this.disabled = false;
        });

        rt.addEventListener('change', function(){ rr.disabled = (this.value !== 'single'); });

        // apply prefill if provided
        try{ if(window.__admin_prefill){ applyPrefill(window.__admin_prefill); delete window.__admin_prefill; } }catch(e){}
    }

    function loadConversation(type, id, name) {
        fetch(`{{ url('/admin/messages/conversation') }}/${type}/${id}`, {
            headers: {'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(res => res.json())
        .then(messages => {
            const container = document.getElementById('messages-container');
            container.innerHTML = '';
            messages.forEach(msg => {
                const msgDiv = document.createElement('div');
                const isAdmin = msg.isOutgoing;
                msgDiv.style.cssText = `margin-bottom:12px;padding:8px;border-radius:8px;max-width:70%;word-wrap:break-word;${isAdmin ? 'margin-left:auto;background:#007bff;color:white;' : 'margin-right:auto;background:#f1f1f1;'}`;
                msgDiv.innerHTML = `<div style="white-space:pre-wrap;">${msg.content}</div><small style="color:${isAdmin ? '#e0e0e0' : '#666'};">${msg.date}</small><button class="btn btn-sm delete-msg" data-id="${msg.id}" style="margin-left:8px;color:red;" title="Supprimer">&times;</button>`;
                container.appendChild(msgDiv);
            });
            container.scrollTop = container.scrollHeight;
            document.getElementById('chat-title').textContent = name;
            document.getElementById('chat-header').style.display = 'block';
            document.getElementById('messages-container').style.display = 'block';
            document.getElementById('reply-area').style.display = 'block';
            currentConversation = {type, id};
        })
        .catch(e => alert('Erreur lors du chargement des messages'));
    }

    function sendReply() {
        const input = document.getElementById('reply-input');
        const body = input.value.trim();
        if (!body || !currentConversation) return;
        const payload = { recipient_type: 'single', recipient: `${currentConversation.type}:${currentConversation.id}`, body };
        fetch('{{ route('admin.messages.send') }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest'},
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(() => {
            input.value = '';
            loadConversation(currentConversation.type, currentConversation.id);
        })
        .catch(e => alert('Erreur lors de l\'envoi'));
    }

    document.getElementById('btn-compose').addEventListener('click', function(){ openCompose(); });

    document.getElementById('conversations-list').addEventListener('click', function(e){
        const item = e.target.closest('.conversation-item');
        if (item) {
            const type = item.dataset.type;
            const id = item.dataset.id;
            const name = item.dataset.name;
            loadConversation(type, id, name);
        }
    });

    document.getElementById('btn-send-reply').addEventListener('click', sendReply);

    document.getElementById('reply-input').addEventListener('keydown', function(e){
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendReply();
        }
    });

    // Delete message
    document.getElementById('messages-container').addEventListener('click', function(e){
        if (e.target.classList.contains('delete-msg')) {
            const id = e.target.dataset.id;
            if (confirm('Supprimer ce message ?')) {
                fetch(`{{ url('/admin/messages') }}/${id}`, {
                    method: 'DELETE',
                    headers: {'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest'}
                })
                .then(res => res.json())
                .then(() => {
                    if (currentConversation) {
                        loadConversation(currentConversation.type, currentConversation.id);
                    }
                })
                .catch(e => alert('Erreur lors de la suppression'));
            }
        }
    });

    // Delete conversation
    document.getElementById('conversations-list').addEventListener('click', function(e){
        if (e.target.classList.contains('delete-conv')) {
            e.stopPropagation();
            const type = e.target.dataset.type;
            const id = e.target.dataset.id;
            if (confirm('Supprimer cette conversation ?')) {
                fetch(`{{ url('/admin/messages/conversation') }}/${type}/${id}`, {
                    method: 'DELETE',
                    headers: {'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest'}
                })
                .then(res => res.json())
                .then(() => location.reload())
                .catch(e => alert('Erreur lors de la suppression'));
            }
        }
    });

    // If a prefill object was set before fetching this view, open compose automatically
    try{ if(window.__admin_prefill){ openCompose(window.__admin_prefill); delete window.__admin_prefill; } }catch(e){}
})();
</script>
