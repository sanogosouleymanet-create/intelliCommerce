<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Commande</title>
</head>
<body>
    <h2>Liste des commandes</h2>

    @foreach($commandes as $commande)
        <p>
            {{$commande->DateCommande}} - {{$commande->Statut}}
        </p>
    @endforeach

</body>
</html>