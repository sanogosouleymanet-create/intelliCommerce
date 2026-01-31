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
    @include('Header')
    </header>
     
      <p><em>Bonjour Cher</em>  <strong>Client(e) !!</strong>  </p>
        <form action="{{ url('/Connexion') }}" method="post">
            @csrf
            <fieldset>
        <div>
                <label>EMAIL :</label>
                <input name="email" type="email" placeholder="votre mail" required><br>
            </div>
            <div>
                <label>MOT DE PASSE :</label>
                <input name="motdepasse" type="password" placeholder="password" maxlength="8" required>
            </div>
            <div>
                <input type="submit" class="button" value="Se Connecter"><br>
            </div>
             </fieldset>
         </form>
         <p>Vous n'avez pas de compte ?</p>
         <button id="createAccount" class="button">CREER UN COMPTE</button>

         <div id="createOptions" style="display:none;margin-top:12px">
            <p>Choisissez le type de compte :</p>
            <a class="button" href="{{ url('/formulaireClient') }}">Client</a>
            <a class="button" href="{{ url('/formulaireVendeur') }}">Vendeur</a>
            <button id="cancelCreate" class="button">Annuler</button>
         </div>

         @if($errors->any())
            <p style="color:#c0392b">{{ $errors->first() }}</p>
         @endif

         <script>
            document.getElementById('createAccount')?.addEventListener('click', function(){
                document.getElementById('createOptions').style.display = 'block';
            });
            document.getElementById('cancelCreate')?.addEventListener('click', function(){
                document.getElementById('createOptions').style.display = 'none';
            });
         </script>
    @include('Footer')
</body>
</html>