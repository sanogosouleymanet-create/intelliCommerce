<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset('css/StyleProduit.css') }}">
    <title>Produits</title>
</head>
<body>
    <h1>Liste des produits</h1>

    @foreach($produits as $produit)
    <p>
        {{ $produit->Nom }} - {{ $produit->Prix }} FCFA</p>
    @endforeach
</body>
</html>