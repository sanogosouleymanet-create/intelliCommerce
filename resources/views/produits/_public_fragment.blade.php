<div class="product-detail-fragment">
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
    <div style="margin-top:12px;text-align:right;">
        <button id="closeProductPanel" class="btn btn-sm btn-secondary">Fermer</button>
    </div>
</div>
