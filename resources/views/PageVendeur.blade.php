
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="Stylesheet" href="{{asset('css/StylePageVendeur.css')}}"/>
    <link rel="Stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <title>Ma PageVendeur</title>
</head>
<body>
    <button id="menuBtn">☰</button>
    <nav id="sidebar">
        <ul>
            <li><a href="{{ url('/produits') }}">Produits ({{ $produitsCount ?? 0 }})</a></li>
            <li><a href="{{ url('/commandes') }}">Commandes ({{ $commandesCount ?? 0 }})</a></li>
        </ul>
    </nav>

    <div class="top-right" role="region" aria-label="Actions utilisateur">
        <button class="icon notification" id="notifBtn" aria-label="Notifications">
            <i class="fa-solid fa-bell"></i>
            <span class="badge" id="notifBadge">{{ $messagesNonLus ?? 0 }}</span>
        </button>

        <div class="profile-wrapper">
            <button class="icon profil" id="profileBtn" aria-haspopup="true" aria-expanded="false">
                <img src="{{ asset('css/avatar-placeholder.png') }}" alt="Avatar" class="profile-img"/>
                <span class="vendeur-name">{{ $vendeur->Nom ?? '' }} {{ $vendeur->Prenom ?? '' }}</span>
            </button>
            <div class="profile-menu" id="profileMenu" aria-hidden="true">
                <a href="{{ url('/profil') }}">Mon profil</a>
                <a href="{{ url('/parametres') }}">Paramètres</a>

                <form action="{{ url('/deconnexion') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" style="background:none;border:none;padding:0;color:#007bff;cursor:pointer;">Déconnexion</button>
                </form>
            </div>
        </div>
    </div>

    <main>
        <section>
            <h2>Alertes récentes</h2>
            <ul>
                @forelse($alertes as $alerte)
                    <li>{{ $alerte->titre ?? ($alerte->message ?? 'Alerte') }} — <small>{{ $alerte->created_at ?? '' }}</small></li>
                @empty
                    <li>Aucune alerte.</li>
                @endforelse
            </ul>
        </section>

        <section>
            <h2>Commandes récentes</h2>
            <ul>
                @forelse($commandesRecentes as $commande)
                    <li>Commande {{ $commande->idCommande ?? $commande->id ?? 'N/A' }} — {{ $commande->Statut ?? '' }}</li>
                @empty
                    <li>Aucune commande récente.</li>
                @endforelse
            </ul>
        </section>

        <section>
            <h2>Messages récents</h2>
            <ul>
                @forelse($messagesRecents as $message)
                    <li>{{ $message->Sujet ?? Str::limit($message->Corps ?? '', 60) }} — {{ $message->created_at ?? '' }}</li>
                @empty
                    <li>Aucun message récent.</li>
                @endforelse
            </ul>
        </section>
    </main>

    <script>
        const menuBtn = document.getElementById("menuBtn");
        const sidebar = document.getElementById("sidebar");
        menuBtn.addEventListener("click", () => { sidebar.classList.toggle("active") });

        document.addEventListener('DOMContentLoaded', function(){
            const profileBtn = document.getElementById('profileBtn');
            const profileMenu = document.getElementById('profileMenu');
            const notifBtn = document.getElementById('notifBtn');

            profileBtn.addEventListener('click', function(e){
                e.stopPropagation();
                const open = profileMenu.classList.toggle('open');
                profileBtn.setAttribute('aria-expanded', open);
                profileMenu.setAttribute('aria-hidden', !open);
            });

            document.addEventListener('click', function(e){
                if (!profileMenu.contains(e.target) && !profileBtn.contains(e.target)){
                    profileMenu.classList.remove('open');
                    profileBtn.setAttribute('aria-expanded', 'false');
                    profileMenu.setAttribute('aria-hidden', 'true');
                }
            });

            notifBtn.addEventListener('click', function(e){
                e.stopPropagation();
                const badge = document.getElementById('notifBadge');
                badge.style.display = 'none';
            });
        });
    </script>
</body>
</html>
