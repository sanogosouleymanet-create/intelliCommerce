<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/StyleAcceuil.css') }}">
    <title>Document</title>
</head>
<body class="Welcome">
    <img src="Mon_logo.png" width="300" Alt="Logo de la plateforme" title="LOGO" class="logo">
    <p>Bienvennue Chez IntelliCommerce le leadear du Commerce au MALI</p>
      <fieldset>
        <legend><h2>Choisissez un Rôle</h2></legend>
        <a href="{{('/ConnexionAdmin')}}"><button class="gradient-button"> <span class="gradient-text">Administrateur</span></button></a><br><br>
        <a href="{{('ConnexionVendeur')}}"><button class="gradient-button"><span class="gradient-text">Vendeur</span></button></a><br><br>
        <a href="{{('/ConnexionClient')}}"><button class="gradient-button"><span class="gradient-text">Client</span></button></a></a>
      </fieldset>
</body>
</html>