<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/StyleAcceuil.css') }}">
    <title>Connexion</title>
</head>
<body>
     <img src="Mon_logo.png" width="300" Alt="Logo de la plateforme" title="LOGO" class="logo">
    <h1>Connexion Client</h1>
    <p><em>Bonjour Cher</em>  <strong>Client(e) !!</strong>  </p>
    <form action="">
        <fieldset>
             <legend><h2>Authentification</h2></legend>
             <br/> <label>identifiant</label> <input type ="text" placeholder = "votre Identifiant"><br/>
             <br> <label>Mot de Passe*:</label> <input type = "password" maxlength ="8" pattern= [A-Z]{2}+[1-9]{4}+[a-z]{2} required=""><br/>
        </fieldset>
    </form><br/>
    <button type="submit" value= "Suivant">NEXT</button>
</body>
</html>