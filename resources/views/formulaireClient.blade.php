<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/StyleAcceuil.css') }}">
    <title>Formulaire</title>
</head>
<body>
     <img src="Mon_logo.png" width="250" Alt="Logo de la plateforme" title="LOGO" class="logo">
     <h1>Sigin/INSCRIPTION CLIENT</h1>
     <p> Bienvenue Cher Client(e)</p>
      
    <form action="">
        <fieldset>
            <legend><h2>Infos Personnels</h2></legend> 
        <br> <label for="Name">Nom*:</label> <input type="text" placeholder ="Entrez votre Nom/Name" required=""> <br/>
        <br/> <label>Prénom*:</label> <input type ="text" placeholder = "votre prénom" required=""><br/>
        <br/> <label>Date de Naissance:</label> <input type= "date" placeholder="jj/mm/aaaa"><br/>
        <br/> <label>Mail:</label> <input type ="email" placeholder = "votre mail"  pattern="(^[a-z0-9]+)@([a-z0-9])+(\.)([a-z]{2,4})"><br/>
        <br/> <label>Tel : </label> <input type ="tel" placeholder = "votre numéro de téléphone" maxlength="13" minlength="13"><br/>
        <br> <label for="adresse">Adresse</label> <input type="text" placeholder ="Entrez votrze adresse"> <br/>
        <div id ="sex">
             <input type ="radio" name="sex" value ="Autre" checked="Autre"> Autre <br/>
            <input type ="radio" name="sex" value ="Homme" > Homme <br/>
            <input type ="radio" name="sex" value ="Femme" > Femme <br/>
        </div>
        <br> <label>Mot de Passe*:</label> <input type = "password" maxlength ="8" pattern= [A-Z]{2}+[1-9]{4}+[a-z]{2} required=""><br/>
        </fieldset>
    </form><br/> 
    <button type="submit" value= "Suivant">NEXT</button>
</body>
</html>