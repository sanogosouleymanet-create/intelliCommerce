<!--@if(isset($produits) && count($produits))
    <ul class="product-list">
        @foreach($produits as $produit)
            <li class="product-item">
                <strong>{{ $produit->Nom }}</strong><br>
                <span>{{ $produit->Description }}</span><br>
                <span>Prix : {{ $produit->Prix }} FCFA</span><br>
                <span>Stock : {{ $produit->Stock }}</span>
            </li>
        @endforeach
    </ul>
@else
    <p>Aucun produit trouvé.</p>
@endif-->

<div class="product-grid">
    @foreach($produits as $produit)
        <div class="product-card card">
            <div class="card-body d-flex flex-column">
                <a href="{{ url('/produit/' . $produit->idProduit) }}" class="produit-link mb-2" style="text-decoration:none;color:inherit;">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $produit->Image ? asset('storage/' . $produit->Image) : asset('images/placeholder.png') }}" alt="Image du produit" class="card-img-top" style="width:120px;height:90px;object-fit:cover;border-radius:6px;">
                        <div class="flex-grow-1">
                            <h6 class="product-title mb-1">{{ $produit->Nom }}</h6>
                            <p class="product-meta mb-1">{{ \Illuminate\Support\Str::limit($produit->Description, 80) }}</p>
                        </div>
                    </div>
                </a>

                <div class="mt-auto d-flex justify-content-between align-items-center">
                    <div class="product-price fs-6 fw-bold">{{ number_format($produit->Prix, 0, ',', ' ') }} FCFA</div>
                    <div>
                        <small class="text-muted me-2">Stock: {{ $produit->Stock ?? '—' }}</small>
                        <a href="{{ url('/produit/' . $produit->idProduit) }}" class="btn btn-sm btn-outline-secondary produit-link">Voir</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>