<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css">
<header class="header" style="display:flex;justify-content:space-between;align-items:center;gap:12px">
            <h1>Espace Administrateur</h1>
            <div style="display:flex;align-items:center;gap:12px">
                <a href="{{ route('admin.parametres') }}" class="account-card" style="display:inline-flex;color:#000 !important;align-items:center;gap:8px;padding:6px 16px;border-radius:10px;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,0.08);border:1px solid #b3d8ea;transition:box-shadow .2s,border .2s;cursor:pointer;text-decoration:none;min-width:160px;">
                    <i class="fa-solid fa-user fa-xl" style="font-size:1.5em"></i>
                    <span style="font-size:1.1em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{-- Affiche le prénom et le nom de l'administrateur s'il est connecté, sinon celui du vendeur, sinon "Mon Compte" --}}
                        @if(isset($admin) && (isset($admin->Prenom) || isset($admin->Nom)))
                            {{ trim(($admin->Prenom ?? '') . ' ' . ($admin->Nom ?? '')) }}
                        @elseif(isset($vendeur) && (isset($vendeur->Prenom) || isset($vendeur->Nom)))
                            {{ trim(($vendeur->Prenom ?? '') . ' ' . ($vendeur->Nom ?? '')) }}
                        @else
                            Mon Compte
                        @endif
                    </span>
                </a>
                <style>
                .account-card:hover {
                    box-shadow:0 2px 8px rgba(0,0,0,0.16);
                    border:1.5px solid #298fcf;
                    background:#e3f4fc;
                }
                </style>
                <script>
                (function(){
                    var acc = document.querySelector('.account-card');
                    if(acc){
                        acc.addEventListener('click', function(ev){
                            var url = acc.getAttribute('href');
                            if(window.adminFetchAndInject){
                                ev.preventDefault();
                                window.adminFetchAndInject(url);
                                // Optionnel : activer l'onglet paramètres dans la sidebar si présent
                                const sidebar = document.querySelector('.sidebar');
                                if(sidebar){
                                    sidebar.querySelectorAll('li').forEach(li => li.classList.remove('active'));
                                    const targetLi = sidebar.querySelector('li[data-view="parametres"]');
                                    if(targetLi) targetLi.classList.add('active');
                                    location.hash = 'parametres';
                                }
                                return;
                            } else {
                                // Forcer la navigation même en SPA/partiel
                                window.location.href = url;
                            }
                        });
                    }
                })();
                </script>
                
                <a href="{{ route('admin.ia_alertes') }}" class="ia-notif" title="Alertes IA" style="position:relative;display:inline-flex;align-items:center; text-decoration:none">
                    <i class="ri-notification-2-line ri-xl" style="font-size:1.5em; color: #000"></i>
                    @if(($counts['ia_alertes'] ?? 0) > 0)
                        <span style="position:absolute;top:-6px;right:-6px;background: #c0392b;color: #fff;border-radius:999px;padding:1px 4px;font-size:10px;min-width:16px;text-align:center">{{ $counts['ia_alertes'] ?? 0 }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.messages') }}" id="header-messages" title="Messages" style="position:relative;display:inline-flex;align-items:center;margin-left:8px; text-decoration:none">
                    <i class="ri-mail-line fa-xl" style="font-size:1.5em; color: #000 "></i>
                    @if(($counts['messages_unread'] ?? 0) > 0)
                        <span id="header-messages-count" style="position:absolute;top:-6px;right:-6px;background: #c0392b;color:#fff;border-radius:999px;padding:1px 4px;font-size:10px;min-width:16px;text-align:center">{{ $counts['messages_unread'] ?? 0 }}</span>
                    @endif
                </a>
                <script>
                    (function(){
                        var el = document.getElementById('header-messages');
                        if(!el) return;
                        el.addEventListener('click', function(ev){
                            ev.preventDefault();
                            var url = '{{ route('admin.messages') }}';
                            if(window.adminFetchAndInject){
                                window.adminFetchAndInject(url);
                                // Update sidebar active state and hash like sidebar click does
                                const sidebar = document.querySelector('.sidebar');
                                if(sidebar){
                                    sidebar.querySelectorAll('li').forEach(li => li.classList.remove('active'));
                                    const targetLi = sidebar.querySelector('li[data-view="messages"]');
                                    if(targetLi) targetLi.classList.add('active');
                                    location.hash = 'messages';
                                }
                                return;
                            }
                            window.location = url;
                        });
                    })();
                </script>
            </div>
        </header>