<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset('css/StyleFormulaireVendeur.css') }}">
    <title>Formulaire</title>
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
      <h2>Veuillez renseigner les champs suivants</h2>
    <form action="{{('/formulaireVendeur')}}" method="post">
        @csrf
        <fieldset>
        <div>
            <label for="Nom*">Nom* :</label> 
            <input type="text" id="Nom" class="champ-input" name="nom" placeholder ="Entrez votre Nom/Name" required=""><br>
        </div>
        <div>
            <label for="Prenom*">Prénom* :</label> 
            <input type ="text" id="Prenom" class="champ-input" name="prenom" placeholder = "votre prénom" required=""><br/>
        </div>
        <div>
            <label for="datenaissance">Date de Naissance :</label> 
            <input type= "date" id="datenaissance" class="champ-input" name="datenaissance" placeholder="jj/mm/aaaa"><br/>
        </div>
        <div>
            <label for="mail">Mail:</label> 
            <input type ="email" id="mail" class="champ-input" name="mail" placeholder = "votre mail"><br/>
        </div>
        <div>
            <label for="TelVendeur">Tel:</label> 
            <input type ="text" id="TelVendeur" class="champ-input" name="tel" pattern="[0-9]+" maxlength="8" placeholder = "votre numéro de téléphone">
        </div>
        <div>
            <label for="NomBoutique">Nom de la Boutique :</label> 
            <input type="text" id="NomBoutique" class="champ-input" name="nomboutique" placeholder="Nom Boutique" required="">
        </div>
        <div>
            <label for="Adresse">Adresse :</label> 
            <input type="text" id="Adresse" class="champ-input" name="adresse" placeholder ="Entrez votre adresse">
        </div>
        <div>
            <label for="MotDePasse">Mot de Passe* :</label> 
            <input type = "password" id="MotDePasse" class="champ-input" name="motdepasse" placeholder="Entrer un mot de passe" maxlength ="8" pattern= [A-Z]{2}+[1-9]{4}+[a-z]{2} required="">
        </div>
        </fieldset>
        <div class="form-actions">
            <a href="{{('/ConnexionVendeur')}}" class="button retour">Retour</a>
            <input type="submit" value="Envoyer" class="button envoyer">
        </div>
    </form>
   
    @if(isset($message))
        <p>{{$message}}</p>
    @endif
    <footer>
            <p>&copy; 2024 IntelliCommerce. All rights reserved.</p>
        </footer>
    </body>
</html>