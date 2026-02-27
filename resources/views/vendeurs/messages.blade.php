@php
// Partial: messages du vendeur
@endphp

@if(isset($vendeur) && $vendeur)

<div class="card" style="display:flex;flex-direction:row;height:80vh;overflow:hidden; width:100%; margin-top:10px;">
    <!-- Sidebar for conversations -->
    <div id="conversations-sidebar" style="width:40%;border-right:1px solid #eee;padding:12px;height:100%;overflow-y:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <h4>Conversations</h4>
            @if(empty($vendeur->Bloque))
                <button id="btn-compose" class="btn btn-sm btn-primary">Nouveau Message</button>
            @else
                <span class="text-muted small"><i class="fas fa-lock"></i> Envoi désactivé</span>
            @endif
        </div>
        <input type="text" id="search-conversations" placeholder="Rechercher une conversation..." style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;margin-bottom:12px;">
        @if(empty($conversations))
            <p>Aucune conversation pour le moment.</p>
        @else
            <ul id="conversations-list" style="list-style:none;padding:0;">
                @foreach($conversations as $key => $conv)
                        @php
                            $sender = $conv['sender'] ?? null;
                            $senderId = null;
                            $senderName = 'Utilisateur supprimé';
                            if($sender) {
                                $senderId = ($conv['senderType'] === 'client') ? ($sender->idClient ?? null) : ($sender->idAdmi ?? ($sender->idVendeur ?? null));
                                if(!empty($sender->Nom) || !empty($sender->Prenom)) {
                                    $senderName = trim(($sender->Nom ?? '') . ' ' . ($sender->Prenom ?? '')) ?: $senderName;
                                } elseif(!empty($sender->email)) {
                                    $senderName = $sender->email;
                                }
                            }
                        @endphp
                        <li class="conversation-item" data-type="{{ $conv['senderType'] }}" data-id="{{ $senderId }}" data-name="{{ $conv['senderType'] === 'admin' ? 'Administrateur' : $senderName }}" data-blocked="{{ ($conv['isBlocked'] ?? false) ? 'true' : 'false' }}" style="padding:8px;border-bottom:1px solid #f0f0f0;cursor:pointer;">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <strong>
                                        @if($conv['isBlocked'] ?? false)
                                            <i class="fas fa-ban" style="color:red;margin-right:4px;"></i>
                                        @endif
                                        {{ $senderName }}
                                </strong>
                                    <div><small style="color:#6b7280;">{{ \Carbon\Carbon::parse($conv['lastMessageDate'])->format('d/m H:i') }}</small></div>
                            </div>
                            <div style="display:flex;align-items:center;gap:8px">
                                <div style="color:#6b7280;font-size:0.9rem;">{{ Str::limit($conv['lastMessage']->Contenu ?? '', 50) }}</div>
                                @if($conv['unreadCount'] > 0 && !($conv['isBlocked'] ?? false))
                                    <span class="unread-badge" style="background:#25D366;color:#fff;padding:6px 10px;border-radius:999px;font-size:0.85rem;min-width:28px;text-align:center;display:inline-block;">{{ $conv['unreadCount'] }}</span>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <!-- Chat area -->
    <div id="chat-area" style="width:60%;display:flex;flex-direction:column;height:100%;">
        <div id="chat-header" style="padding:12px;border-bottom:1px solid #eee;display:none;flex-shrink:0;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <h5 id="chat-title">Sélectionnez une conversation</h5>
                <div style="position:relative;">
                    <button id="chat-options-btn" style="background:none;border:none;font-size:18px;cursor:pointer;color:black;width: 200px;">&#8942;</button>
                    <div id="chat-options-menu" style="position:absolute;top:100%;right:0;background:#fff;border:1px solid #ddd;border-radius:4px;padding:8px;display:none;z-index:10;">
                        <button id="delete-conversation-btn" style="width:100%;margin-bottom:4px;background-color:white !important;border:1px solid #ddd;color:black;">Supprimer la conversation</button>
                        <button id="block-user-btn" style="width:100%;display:none;margin-bottom:4px;background-color:white !important;border:1px solid #ddd;color:black;">Bloquer la personne</button>
                        <button id="unblock-user-btn" style="width:100%;display:none;background-color:white !important;border:1px solid #ddd;color:green;">Débloquer la personne</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="messages-container" style="flex:1;padding:12px;display:none;overflow-y:auto;flex-direction:column;">
            <!-- Messages will be loaded here -->
        </div>
        <div id="reply-area" style="padding:12px;border-top:1px solid #eee;display:none;flex-shrink:0;" data-vendeur-blocked="{{ !empty($vendeur->Bloque) ? '1' : '0' }}">
            @if(empty($vendeur->Bloque))
                <div style="display:flex;gap:8px;">
                    <textarea id="reply-input" placeholder="Tapez votre message..." style="flex:1;padding:4px;border:1px solid #ddd;border-radius:4px;resize:none;" rows="1"></textarea>
                    <button id="btn-send-reply" class="btn btn-sm btn-primary">Envoyer</button>
                </div>
            @else
                <p class="text-muted small mb-0"><i class="fas fa-lock"></i> L'envoi de messages est désactivé pour votre compte.</p>
            @endif
        </div>
    </div>
</div>

<script>
(function(){
    const csrf = '{{ csrf_token() }}';
    let currentConversation = null;

    function openCompose(prefill) {
        const modal = document.createElement('div');
        modal.innerHTML = `
        <div id="vendeur-compose-modal" style="position:fixed;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(2,6,23,0.5);z-index:9999">
          <div style="background:#fff;padding:18px;border-radius:8px;max-width:720px;width:100%">
            <h3>Composer un message</h3>
            <div style="margin-top:8px">
                            <div style="margin-bottom:8px">
                                <input id="compose-recipient" type="email" placeholder="Email du destinataire" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px" />
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
        const container = document.getElementById('vendeur-compose-modal');
        const rr = container.querySelector('#compose-recipient');
        const subj = container.querySelector('#compose-subject');
        const body = container.querySelector('#compose-body');

        function applyPrefill(pref) {
            if(!pref) return;
            if(pref.recipient) rr.value = pref.recipient;
            if(pref.subject) subj.value = pref.subject;
            if(pref.body) body.value = pref.body;
        }

        container.querySelector('#compose-cancel').addEventListener('click', () => container.remove());
        container.querySelector('#compose-send').addEventListener('click', async function(){
            const recipient = rr.value;
            const subject = subj.value;
            const bodyVal = body.value;
            if(!bodyVal){ alert('Saisissez un message'); return; }
            if(!recipient){ alert('Sélectionnez un client'); return; }
            this.disabled = true;
            try{
                const payload = { recipient, subject, body: bodyVal };
                const res = await fetch('{{ route('vendeur.messages.send') }}', {
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

        // No need for recipient type change since only clients

        // apply prefill if provided (explicit param has priority, then global helper)
        try{
            const toApply = prefill || (window.__vendeur_prefill || null);
            if(toApply){
                applyPrefill(toApply);
            }
            if(window.__vendeur_prefill){
                delete window.__vendeur_prefill;
            }
        }catch(e){}
    }

    function loadConversation(type, id, name, blocked) {
        console.log('loadConversation called with:', type, id, name, blocked);
        // normalize name and blocked when not provided
        if (typeof name === 'undefined' || name === null) {
            name = (currentConversation && currentConversation.name) ? currentConversation.name : (document.getElementById('chat-title') ? document.getElementById('chat-title').textContent : 'Conversation');
        }
        blocked = (typeof blocked !== 'undefined') ? blocked : (currentConversation ? currentConversation.blocked : false);
        const url = '/vendeur/messages/conversation/' + type + '/' + id;
        console.log('Fetching URL:', url);
        fetch(url, {
            headers: {'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest'},
            credentials: 'same-origin'
        })
        .then(res => {
            console.log('Fetch response status:', res.status);
            if (!res.ok) {
                throw new Error('Erreur serveur: ' + res.status);
            }
            return res.json();
        })
        .then(messages => {
            console.log('Messages received:', messages);
            const container = document.getElementById('messages-container');
            container.innerHTML = '';
            messages.forEach(msg => {
                const msgDiv = document.createElement('div');
                const isVendeur = msg.isOutgoing;
                msgDiv.style.cssText = `margin-bottom:12px;padding:8px;border-radius:8px;max-width:70%;word-wrap:break-word;${isVendeur ? 'margin-left:auto;background:#007bff;color:white;' : 'margin-right:auto;background:#f1f1f1;'}`;
                msgDiv.innerHTML = `<div style="white-space:pre-wrap;">${msg.content || ''}</div><small style="color:${isVendeur ? '#e0e0e0' : '#666'};">${msg.date || ''}</small><button class="btn btn-sm delete-msg" data-id="${msg.id}" style="margin-left:8px;color:red;" title="Supprimer">&times;</button>`;
                container.appendChild(msgDiv);
            });
            container.scrollTop = container.scrollHeight;
            document.getElementById('chat-title').textContent = name;
            document.getElementById('chat-header').style.display = 'block';
            document.getElementById('messages-container').style.display = 'flex';
            document.getElementById('reply-area').style.display = 'block';
            currentConversation = {type, id, name, blocked};
            updateMenuOptions();
            console.log('Conversation loaded successfully');
        })
        .catch(e => {
            console.error('Error in loadConversation:', e);
            alert('Erreur lors du chargement des messages: ' + e.message);
        });
    }

    function updateMenuOptions() {
        const blockBtn = document.getElementById('block-user-btn');
        const unblockBtn = document.getElementById('unblock-user-btn');
        const replyArea = document.getElementById('reply-area');
        if (currentConversation && currentConversation.blocked) {
            if (blockBtn) blockBtn.style.display = 'none';
            if (unblockBtn) unblockBtn.style.display = 'block';
            if (replyArea) replyArea.style.display = 'none';
        } else {
            if (blockBtn) blockBtn.style.display = 'block';
            if (unblockBtn) unblockBtn.style.display = 'none';
            if (replyArea) replyArea.style.display = 'block';
        }
    }

    function sendReply() {
        if (currentConversation && currentConversation.blocked) {
            alert('Vous ne pouvez pas envoyer de message à une personne bloquée.');
            return;
        }
        const input = document.getElementById('reply-input');
        if (!input) { alert('Champ de réponse introuvable'); return; }
        const body = input.value.trim();
        if (!body) { alert('Saisissez un message'); input.focus(); return; }
        if (!currentConversation) { alert('Sélectionnez une conversation avant d\'envoyer'); return; }

        const sendBtn = document.getElementById('btn-send-reply');
        if (sendBtn) sendBtn.disabled = true;

        const payload = { recipient_type: 'single', recipient: `${currentConversation.type}:${currentConversation.id}`, body };
        fetch('{{ route('vendeur.messages.send') }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},
            body: JSON.stringify(payload)
        })
        .then(res => {
            if (!res.ok) return res.json().then(js => Promise.reject(js)).catch(() => Promise.reject({message: 'Erreur serveur'}));
            return res.json().catch(() => ({}));
        })
            .then(() => {
            input.value = '';
            loadConversation(currentConversation.type, currentConversation.id, currentConversation.name, currentConversation.blocked);
        })
        .catch(e => {
            const msg = (e && e.message) ? e.message : 'Erreur lors de l\'envoi';
            alert(msg);
        })
        .finally(() => { if (sendBtn) sendBtn.disabled = false; });
    }

    const btnCompose = document.getElementById('btn-compose');
    if (btnCompose) btnCompose.addEventListener('click', function(){ openCompose(); });

    // Use delegated listener so clicks work after AJAX partial loads
    document.addEventListener('click', function(e){
        const item = e.target.closest('.conversation-item');
        if (item) {
            const type = item.dataset.type;
            const id = item.dataset.id;
            const name = item.dataset.name;
            const blocked = item.dataset.blocked === 'true';
            loadConversation(type, id, name, blocked);
        }
    });

    const sendBtn = document.getElementById('btn-send-reply');
    if (sendBtn) sendBtn.addEventListener('click', sendReply);

    const replyInput = document.getElementById('reply-input');
    if (replyInput) {
        replyInput.addEventListener('keydown', function(e){
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendReply();
            }
        });
    }

    // Delete message
    const _messagesContainer = document.getElementById('messages-container');
    if (_messagesContainer) {
        _messagesContainer.addEventListener('click', function(e){
        if (e.target.classList.contains('delete-msg')) {
            const id = e.target.dataset.id;
            if (confirm('Supprimer ce message ?')) {
                fetch('/vendeur/messages/' + id, {
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
    }

    // Toggle chat options menu
    const _chatOptionsBtn = document.getElementById('chat-options-btn');
    if (_chatOptionsBtn) {
        _chatOptionsBtn.addEventListener('click', function(e){
            e.stopPropagation();
            const menu = document.getElementById('chat-options-menu');
            if (menu) menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        });
    }

    // Close menu when clicking outside
    document.addEventListener('click', function(e){
        const menu = document.getElementById('chat-options-menu');
        const btn = document.getElementById('chat-options-btn');
        if (!menu) return;
        if (!btn || (!btn.contains(e.target) && !menu.contains(e.target))) {
            menu.style.display = 'none';
        }
    });

    // Delete current conversation
    const deleteConvBtn = document.getElementById('delete-conversation-btn');
    if (deleteConvBtn) {
        deleteConvBtn.addEventListener('click', function(){
            if (!currentConversation) return;
            if (confirm('Supprimer cette conversation ?')) {
                fetch('/vendeur/messages/conversation/' + currentConversation.type + '/' + currentConversation.id, {
                    method: 'DELETE',
                    headers: {'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest'}
                })
                .then(res => res.json())
                .then(() => location.reload())
                .catch(e => alert('Erreur lors de la suppression'));
            }
        });
    }

    // Block user
    const blockBtn = document.getElementById('block-user-btn');
    if (blockBtn) {
        blockBtn.addEventListener('click', function(){
            if (!currentConversation) return;
            if (confirm('Bloquer cette personne ?')) {
                fetch('/vendeur/messages/block/' + currentConversation.type + '/' + currentConversation.id, {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest'}
                })
                .then(res => res.json())
                .then(() => {
                    location.reload();
                })
                .catch(e => alert('Erreur lors du blocage'));
            }
        });
    }

    // Unblock user
    const unblockBtnElm = document.getElementById('unblock-user-btn');
    if (unblockBtnElm) {
        unblockBtnElm.addEventListener('click', function(){
            if (!currentConversation) return;
            if (confirm('Débloquer cette personne ?')) {
                fetch('/vendeur/messages/unblock/' + currentConversation.type + '/' + currentConversation.id, {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest'}
                })
                .then(res => res.json())
                .then(() => {
                    location.reload();
                })
                .catch(e => alert('Erreur lors du déblocage'));
            }
        });
    }

    // If a prefill object was set before fetching this view, open compose automatically
    try{ if(window.__vendeur_prefill){ openCompose(); } }catch(e){}

    // Auto-open conversation or compose when ?vendeur=ID is present in URL
    (function() {
        try {
            const urlParams = new URLSearchParams(window.location.search);
            const vendeurId = urlParams.get('vendeur');
            if (!vendeurId) return;

            setTimeout(function() {
                const selector = `.conversation-item[data-type="vendeur"][data-id="${vendeurId}"]`;
                const item = document.querySelector(selector);
                if (item) {
                    const type = item.dataset.type;
                    const id = item.dataset.id;
                    const name = item.dataset.name;
                    const blocked = item.dataset.blocked === 'true';
                    loadConversation(type, id, name, blocked);
                } else {
                    openCompose({
                        recipient: `vendeur:${vendeurId}`,
                        subject: 'Question sur le produit',
                        body: ''
                    });
                }
                const newUrl = window.location.pathname;
                window.history.replaceState({}, '', newUrl);
            }, 300);
        } catch (e) {
            console.error('Erreur auto-ouverture messagerie vendeur:', e);
        }
    })();

    // Search conversations
    const searchInput = document.getElementById('search-conversations');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const items = document.querySelectorAll('.conversation-item');
            items.forEach(item => {
                const name = item.dataset.name.toLowerCase();
                if (name.includes(query)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
})();
</script>
@else
    <p>Utilisateur introuvable.</p>
@endif
