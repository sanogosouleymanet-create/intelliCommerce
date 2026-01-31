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
         
         <form action ="{{('/ConnexionVendeur')}}" method="post">
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
         <p>don't have an account?</p>
         <a class="button" href="{{('formulaireVendeur')}}">CREER UN COMPTE</a>
        @if(isset($message))
            <p>{{$message}}</p>
        @endif

    @include('Footer')
</body>
</html>