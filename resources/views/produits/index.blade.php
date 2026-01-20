<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
     <!-- Vue: produits/index.blade.php - Liste des produits -->
    <title>Produits</title>
    <!-- Charge la feuille de style spécifique aux produits -->
    <link rel="stylesheet" href="{{ asset('css/StyleProduit.css') }}">
</head>
<body>
    <h1>Liste des produits</h1>
    <a class="Ajout" href="#">+</a>

    @foreach($produits as $produit)
    <p>
        {{ $produit->Nom }} - {{ $produit->Prix }} FCFA</p>
    @endforeach
</body>
</html>