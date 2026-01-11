<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset('css/StyleAcceuil.css') }}">
    <title>Formulaire</title>
</head>
<body>
     <img src="Mon_logo.png" width="250" Alt="Logo de la plateforme" title="LOGO" class="logo">
    <h1>Sigin/INSCRIPTION VENDEUR</h1>
    <p>Bienvennue Cher Commerçant !!</p>
    <form action="{{('/formulaireVendeur')}}" method="post">
        @csrf
        <fieldset>
            <legend><h2>Infos Personnels</h2></legend> 
        <br> <label for="Nom*">Nom* :</label> 
        <input type="text" id="Nom" name="nom" placeholder ="Entrez votre Nom/Name" required=""><br>
        <br/> <label for="Prenom">Prénom* :</label> 
        <input type ="text" id="Prenom" name="prenom" placeholder = "votre prénom" required=""><br/>
        <br/> <label for="datenaissance">Date de Naissance :</label> 
        <input type= "date" id="datenaissance" name="datenaissance" placeholder="jj/mm/aaaa"><br/>
        <br/> <label for="mail">Mail:</label> 
        <input type ="email" id="mail" name="mail" placeholder = "votre mail"><br/>

        <br/> <label for="TelVendeur">Tel:</label> 
        <input type ="text" id="TelVendeur" name="tel" pattern="[0-9]+" maxlength="8" placeholder = "votre numéro de téléphone"><br/>

        <br/> <label for="NomBoutique">Nom de la Boutique :</label> 
        <input type="text" id="NomBoutique" name="nomboutique" placeholder="Nom Boutique" required=""><br>
        <br> <label for="Adresse">Adresse :</label> 
        <input type="text" id="Adresse" name="adresse" placeholder ="Entrez votre adresse"><br>
        <br> <label for="MotDePasse">Mot de Passe* :</label> 
        <input type = "password" id="MotDePasse" name="motdepasse" maxlength ="8" pattern= [A-Z]{2}+[1-9]{4}+[a-z]{2} required=""><br/>
        </fieldset>
        <br><input type="submit" value= "Envoyer" class="button"><br>
    </form> 
    @if(isset($message))
        <p>{{$message}}</p>
    @endif

    <br><boutton type="retour" value= "Retour" class=button><a href="{{('/ConnexionVendeur')}}">Retour</a></boutton>
</body>
</html>