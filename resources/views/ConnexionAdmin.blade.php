<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset('css/StyleFormulaire.css') }}">
    <title>ConnexionAministrator</title>
</head>
<body>
    <header>
        @include('Header')
    </header>
    <h1>Authentification</h1>
    <p><em>Bienvennue Cher</em> <strong>Aministrateur</strong></p>
    <form action="{{('/ConnexionAdmin')}}" method="post">
        @csrf
        <fieldset>
             <br/> <label for="identifiant">N° Indentifiant* : </label>
             <input name="identifiant" type="number" placeholder="Votre indentifiant Svp" required><br/>
             <br/> <label>Mot de Passe* :</label>
             <input name="motdepasse" type="password" placeholder="Votre mot de passe" required>
        </fieldset>
        <div class="form-actions">
            <a href="{{('/welcome')}}" class="button retour">Retour</a>
            <input type="submit" value="Se Connecter" class="button envoyer">
        </div>
    </form>
    @include('Footer')
</body>
</html>