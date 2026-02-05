
<div class="product-detail-fragment" style="background-color:#305CDE; padding:16px; border-radius:8px; max-width:600px; margin:0 auto; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
    <div style="display:flex;gap:16px;align-items:flex-start;">
        <div style="flex:0 0 180px;">
            <img src="{{ $produit->Image ? asset('storage/' . $produit->Image) : asset('images/placeholder.png') }}" alt="Image du produit" style="width:100%;height:auto;object-fit:cover;border:1px solid #eee;padding:6px;" />
        </div>
        <div style="flex:1;">
            <h3 style="margin-top:0">{{ $produit->Nom }}</h3>
            <p>{{ $produit->Description }}</p>
            <p><strong>Prix: </strong>{{ number_format($produit->Prix,0,',',' ') }} FCFA</p>
            <p><strong>Stock: </strong>{{ $produit->Stock }}</p>
            <p><strong>Catégorie: </strong>{{ $produit->Categorie }}</p>
            @if($vendeur)
                <p><strong>Boutique: </strong>{{ $vendeur->NomBoutique ?? trim(($vendeur->Nom ?? '') . ' ' . ($vendeur->Prenom ?? '')) ?: 'Boutique' }}</p>
            @endif
        </div>
    </div>
    <div style="margin-top:12px;text-align:right;display:flex;gap:8px;justify-content:flex-end;">
        <button class="btn btn-primary add-to-cart-fragment" data-id="{{ $produit->idProduit }}">Ajouter au panier</button>
        <button class="btn btn-sm btn-secondary js-back">Fermer</button>
    </div>
</div>
