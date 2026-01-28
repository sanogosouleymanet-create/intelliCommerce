<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/StyleAcceuil.css') }}">
    <title>Acceuil</title>
</head>
<body>
    <section class="hero">
      <h2 class="section-title">Bienvenue sur IntelliCommerce</h2>
      <p class="lead"><b>Nous facilitons le commerce au Mali : découvrez des produits locaux, vendez en ligne et gérez vos commandes rapidement et en toute sécurité.</b></p>
    </section>

    <h2 class="section-subtitle"><b>Choisissez une option</b></h2>

      <nav>
        <a href="{{('/ConnexionAdmin')}}"><button class="gradient-button"> <span class="gradient-text">Administrateur</span></button></a><br><br>
        <a href="{{('/ConnexionVendeur')}}"><button class="gradient-button"><span class="gradient-text">Vendeur</span></button></a><br><br>
        <a href="{{('/ConnexionClient')}}"><button class="gradient-button"><span class="gradient-text">Client</span></button></a>
      </nav> 
      <div class="clear">
        @include('Footer')
      </div>
            
      </body>
</html>