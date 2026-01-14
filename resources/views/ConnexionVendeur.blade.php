<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/StyleConnexion.css') }}">
    <title>Connexion</title>
</head>
<body>
     <header>
            <img src="Logo-Site.png" width="500" alt="Logo de la plateforme" title="LOGO" class="logo">
            <nav>
                <ul>
                    <li><a href="{{('welcome')}}">HOME</a></li>
                    <li><a href="{{('')}}">ABOUT</a></li>
                    <li><a href="{{('')}}">SERVICES</a></li>
                    <li><a href="{{('')}}">CONTACT</a></li>
                </ul>  
            </nav>
            
     </header>
         
         <form action ="">
          <fieldset>
            <div>
                <label>EMAIL :</label>
                <input type="EMAIL" placeholder = "votre mail" required><br>        
            </div>
            <div>
                <label>MOT DE PASSE :</label>
                <input type="password" type = "password" placeholder="password" maxlength ="8" pattern= [A-Z]{2}+[1-9]{4}+[a-z]{2} required="">
            </div>
            <div>
                <input type="submit" class="button" value="Se Connecter"><br>
            </div>
             </fieldset>
         </form>  
         <p>don't have an account?</p>
         <button class="button"><a href="{{('formulaireVendeur')}}">CREER UN COMPTE</a></button> 
        
         <footer>
            <p>&copy; 2024 IntelliCommerce. All rights reserved.</p>
        </footer>
</body>
</html>