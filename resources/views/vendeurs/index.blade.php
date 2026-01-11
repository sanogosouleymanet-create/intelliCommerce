<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <h2>Liste des vendeurs</h2>

    @foreach($vendeurs as $vendeur)
        <p>
            {{$vendeur->Nom}} - {{$vendeur->Prenom}} - {{$vendeur->DateCreation}}
        </p>
    @endforeach
</body>
</html>