<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/StyleAcceuil.css') }}">
    <title>ConnexionAministrator</title>
</head>
<body>
     <img src="Mon_logo.png" width="300" Alt="Logo de la plateforme" title="LOGO" class="logo">
      <nav class="navigation">
                <a href="{{('welcome')}}">HOME</a>
                <a href="{{('')}}">ABOUT</a>
                <a href="{{('')}}">SERVICES</a>
                <a href="{{('')}}">CONTACT</a>
      </nav>
    <h1>Connexion Admin</h1>
    <p><em>Bienvennue Cher</em> <strong>Aministrateur</strong></p>
    <form action="" method="">
        <fieldset>
            <legend>Authentification</legend>
             <br/> <label for="identifiant">N° Indentifiant* : </label> <input type="number" placeholder="Votre indentifiant Svp" required=""><br/>
             <br/> <label>Mot de Passe* :</label> <input type="password" placeholder="Votre mot de passe" required="">
        </fieldset>
       
    </form>
    
</body>
</html>