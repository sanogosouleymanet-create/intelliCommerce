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

@foreach($produits as $produit)
<a href="{{ url('/produits/' . $produit->idProduit) }}" class="produit-link" style="text-decoration:none;">
        <div class="produit" role="button">
                <img src="{{ $produit->Image ? asset('storage/' . $produit->Image) : asset('images/placeholder.png') }}" alt="Image du produit" class="produit-image">
                <div class="produit-body">
                        <div class="produit-nom">{{ $produit->Nom }}</div>
                        <div><strong>Prix: {{ number_format($produit->Prix, 0, ',', ' ') }} FCFA</strong></div>
                        <div><small>Stock: {{ $produit->Stock ?? '—' }}</small></div>
                </div>
        </div>
</a>
@endforeach