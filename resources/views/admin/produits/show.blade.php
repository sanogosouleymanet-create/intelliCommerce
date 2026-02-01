<section class="admin-product-card" data-id="{{ $produit->idProduit }}">
    <style>
        .admin-product-card{max-width:980px;margin:28px auto;padding:22px;background:#fff;border-radius:10px;box-shadow:0 6px 18px rgba(0,0,0,0.08);}
        .admin-product-row{display:flex;gap:24px;align-items:flex-start}
        .admin-product-image{flex:0 0 360px;background:#fafafa;padding:12px;border-radius:8px;border:1px solid #f0f0f0}
        .admin-product-image img{width:100%;height:auto;object-fit:cover;border-radius:6px}
        .admin-product-info{flex:1;padding:6px 2px}
        .admin-product-info h3{margin:0 0 8px;font-size:22px}
        .admin-product-info p{margin:6px 0;color:#333}
        .admin-product-meta{display:grid;grid-template-columns:repeat(2,1fr);gap:6px}
        .admin-product-actions{
            margin-top:14px;
            display:flex;
            gap:10px;
            
        }
        .admin-product-actions a{
            text-decoration:none;
        }
        .btn{padding:8px 12px;border-radius:6px;border:0;cursor:pointer}
        .btn-outline-secondary{
            background: #007bff;
            border:1px solid #7f8c8d;
            color: #fff;
        }
        
        /*.btn-primary{background:#2980b9;color:#fff}
        .btn-danger{background:#c0392b;color:#fff}*/
        @media(max-width:800px){.admin-product-row{flex-direction:column}.admin-product-image{flex:unset;width:100%}}
    </style>

    <h2 style="margin-top:0;margin-bottom:12px;color:#222">Détails du produit</h2>
    <div class="admin-product-row">
        <div class="admin-product-image">
            <img src="{{ $produit->Image ? asset('storage/' . $produit->Image) : asset('images/placeholder.png') }}" alt="Image du produit" />
        </div>
        <div class="admin-product-info">
            <h3>{{ $produit->Nom }}</h3>
            <p>{{ $produit->Description }}</p>
            <div class="admin-product-meta">
                <div><strong>Prix:</strong> <span id="p-prix">{{ number_format($produit->Prix,0,',',' ') }} FCFA</span></div>
                <div><strong>Stock:</strong> {{ $produit->Stock }}</div>
                <div><strong>Catégorie:</strong> {{ $produit->Categorie }}</div>
                @if($vendeur)
                    <div><strong>Boutique:</strong> {{ $vendeur->NomBoutique ?? trim(($vendeur->Nom ?? '') . ' ' . ($vendeur->Prenom ?? '')) ?: 'Boutique' }}</div>
                @endif
            </div>

            <div class="admin-product-actions">
                <a href="{{ route('admin.produits') }}" class="btn btn-outline-secondary" onclick="event.preventDefault(); window.adminFetchAndInject(this.href);">Retour</a>
                
        </div>
    </div>
</section>
