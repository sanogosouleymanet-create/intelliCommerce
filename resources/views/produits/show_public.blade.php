<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du produit</title>
    <link rel="stylesheet" href="{{ asset('css/StyleProduit.css') }}">
</head>
<body>
<section class="card" style="max-width:900px;margin:24px auto;padding:20px;">
    <h2>Détails du produit</h2>
    <div style="display:flex;gap:20px;align-items:flex-start;">
        <div style="flex:0 0 320px;">
            <img src="{{ $produit->Image ? asset('storage/' . $produit->Image) : asset('images/placeholder.png') }}" alt="Image du produit" style="width:100%;height:auto;object-fit:cover;border:1px solid #eee;padding:6px;" />
        </div>
        <div style="flex:1;">
            <h3>{{ $produit->Nom }}</h3>
            <p>{{ $produit->Description }}</p>
            <p><strong>Prix: </strong>{{ number_format($produit->Prix,0,',',' ') }} FCFA</p>
            <p><strong>Stock: </strong>{{ $produit->Stock }}</p>
            <p><strong>Catégorie: </strong>{{ $produit->Categorie }}</p>
            @if($vendeur)
                <p><strong>Boutique: </strong>{{ $vendeur->NomBoutique ?? trim(($vendeur->Nom ?? '') . ' ' . ($vendeur->Prenom ?? '')) ?: 'Boutique' }}</p>
            @endif
            <div style="margin-top:12px;">
                <a href="/" class="btn btn-outline-secondary">Retour</a>
            </div>
        </div>
    </div>
</section>
</body>
</html>
