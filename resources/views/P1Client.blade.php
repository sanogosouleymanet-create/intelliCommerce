<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/StyleAcceuil.css') }}">
    <title>P1Client</title>
</head>
<body class="Welcome">
     <img src="Mon_logo.png" width="300" Alt="Logo de la plateforme" title="LOGO" class="logo">
    <p><em>Bonjour Cher</em> <strong>Client(e) !!!</strong></p> <br/>
       <p>Vous avez un compte :  </p><a href="{{('ConnexionClient')}}"><button class="button">Login/Se connecter</button></a>
    <p>Vous n'avez pas de Compte: </p> <a href="{{('formuClient')}}"><button class="button">Sign up/Creer un Compte</button></a>
</body>
</html>