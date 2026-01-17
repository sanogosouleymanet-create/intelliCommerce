<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="Stylesheet" href="{{asset('css/StylePageVendeur.css')}}">
    <title>Ma PageVendeur</title>
</head>
<body>
    @csrf
    <!--<header>
        <img src="Logo-Site.png" width="200" alt="Logo de la plateforme" title="LOGO" class="logo">
    </header>-->
     <!--<nav class="nav">
        <ul>
            <li><a href="{{''}}">Produits</a></li>
            <li><a href="{{''}}">Commandes</a></li>
        </ul>
    </nav>-->
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
</body>
</html>