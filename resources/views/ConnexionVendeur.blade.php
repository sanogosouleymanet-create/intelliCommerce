<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/StyleAcceuil.css') }}">
    <title>Connexion</title>
</head>
<body>
     <header>
            <img src="Mon_logo.png" width="300" Alt="Logo de la plateforme" title="LOGO" class="logo">
            <nav class="navigation">
                <a href="{{('welcome')}}">HOME</a>
                <a href="{{('')}}">ABOUT</a>
                <a href="{{('')}}">SERVICES</a>
                <a href="{{('')}}">CONTACT</a>
            </nav>
            <p><em>Bonjour Cher</em> <strong>Commerçant(e)</strong></p>
            
     </header>
         
         <form action ="">
          <fieldset>
             <legend><h2> SE CONNECTER</h2></legend>
             <br><label>EMAIL :</label><input type="EMAIL" placeholder = "votre mail" required><br>
             <br><label>MOT DE PASSE :</label><input type="password" type = "password" placeholder="password" maxlength ="8" pattern= [A-Z]{2}+[1-9]{4}+[a-z]{2} required=""> <br>
             <br><button type="submit"class="button">Login/Se Connecter</a></button>
          </fieldset>
         </form>  
         <p>don't have an account?</p><boutton class="button"><a href="{{('formulaireVendeur')}}">CREER UN COMPTE</a></button> 
</body>
</html>