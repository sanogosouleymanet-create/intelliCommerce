<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="Stylesheet" href="{{asset('css/StylePageVendeur.css')}}"/>
    <link rel="Stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <title>Ma PageVendeur</title>
</head>
<body>
    @csrf
    <!--<header>
        <img src="Logo-Site.png" width="200" alt="Logo de la plateforme" title="LOGO" class="logo">
    </header>-->
    <button id="menuBtn">☰</button>
    <nav id="sidebar">
        <ul>
            <li><a href="{{''}}">Produits</a></li>
            <li><a href="{{''}}">Commandes</a></li>
        </ul>
    </nav>
    <script>
        const menuBtn = document.getElementById("menuBtn");
        const sidebar = document.getElementById("sidebar");

        menuBtn.addEventListener("click", () => 
        {
            sidebar.classList.toggle("active")
        });
    </script>

    <div class="top-right" role="region" aria-label="Actions utilisateur">
        <button class="icon notification" id="notifBtn" aria-label="Notifications">
            <i class="fa-solid fa-bell"></i>
            <span class="badge" id="notifBadge">3</span>
        </button>

        <div class="profile-wrapper">
            <button class="icon profil" id="profileBtn" aria-haspopup="true" aria-expanded="false">
                <img src="{{ asset('css/avatar-placeholder.png') }}" alt="Avatar" class="profile-img"/>
            </button>
            <div class="profile-menu" id="profileMenu" aria-hidden="true">
                <a href="{{ url('/profil') }}">Mon profil</a>
                <a href="{{ url('/parametres') }}">Paramètres</a>
                <a href="{{ url('/deconnexion') }}">Déconnexion</a>
            </div>
        </div>
    </div>

    <script>
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

            // fermer menus au clic extérieur
            document.addEventListener('click', function(e){
                if (!profileMenu.contains(e.target) && !profileBtn.contains(e.target)){
                    profileMenu.classList.remove('open');
                    profileBtn.setAttribute('aria-expanded', 'false');
                    profileMenu.setAttribute('aria-hidden', 'true');
                }
            });

            // action simple pour la notif (ex: vider le badge)
            notifBtn.addEventListener('click', function(e){
                e.stopPropagation();
                const badge = document.getElementById('notifBadge');
                badge.style.display = 'none';
            });
        });
    </script>
</body>
</html>