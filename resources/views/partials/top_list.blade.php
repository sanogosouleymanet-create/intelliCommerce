@php
    $items = $items ?? collect();
@endphp
<div style="margin-bottom:12px"><h2 style="margin:0 0 8px 0">Les plus recherchés</h2></div>
@if($items->isEmpty())
    <div class="alert alert-info">Aucun produit trouvé.</div>
@else
    <div class="product-grid" style="margin-top:12px;" id="top-items-grid">
        @foreach($items as $produit)
            <div class="product-card card">
                <div class="position-relative">
                    @php
                        $imgUrl = 'https://via.placeholder.com/400x300?text=No+Image';
                        $img = trim((string)($produit->Image ?? ''));
                        if($img !== ''){
                            if(preg_match('/^https?:\/\//i', $img)){
                                $imgUrl = $img;
                            } elseif(\Illuminate\Support\Facades\Storage::exists('public/'.$img)){
                                $imgUrl = asset('storage/'.$img);
                            } elseif(file_exists(public_path($img))){
                                $imgUrl = asset($img);
                            } elseif(file_exists(public_path('images/'.basename($img)))){
                                $imgUrl = asset('images/'.basename($img));
                            }
                        }
                    @endphp
                    <img src="{{ $imgUrl }}" class="card-img-top" alt="{{ $produit->Nom }}">
                    <button class="add-to-cart" title="Ajouter au panier" data-id="{{ $produit->idProduit }}" aria-label="Ajouter {{ $produit->Nom }} au panier">
                        <i class="fa fa-cart-plus"></i>
                    </button>
                </div>
                <div class="card-body">
                    @php
                        $dataName = e($produit->Nom);
                        $dataDesc = e($produit->Description ?? '');
                        $dataPrice = number_format($produit->Prix, 0, ',', ' ') . ' FCFA';
                        $dataImg = $imgUrl;
                        $vendeur = $produit->vendeur ?? null;
                        $vendorName = e($vendeur->NomBoutique ?? ($vendeur->Nom . ' ' . ($vendeur->Prenom ?? '')));
                        $vendorAddress = e($vendeur->Adresse ?? '');
                        $similar = \App\Models\Produit::where('Categorie', $produit->Categorie)
                            ->where('idProduit', '!=', $produit->idProduit)
                            ->limit(4)
                            ->get(['idProduit','Nom','Prix','Image'])
                            ->map(function($s){
                                $img = trim((string)($s->Image ?? ''));
                                $imgUrl = 'https://via.placeholder.com/120x90?text=No';
                                if($img !== ''){
                                    if(preg_match('/^https?:\/\//i', $img)){
                                        $imgUrl = $img;
                                    } elseif(\Illuminate\Support\Facades\Storage::exists('public/'.$img)){
                                        $imgUrl = asset('storage/'.$img);
                                    } elseif(file_exists(public_path($img))){
                                        $imgUrl = asset($img);
                                    } elseif(file_exists(public_path('images/'.basename($img)))){
                                        $imgUrl = asset('images/'.basename($img));
                                    }
                                }
                                return ['id' => $s->idProduit, 'name' => $s->Nom, 'price' => number_format($s->Prix,0,',',' ') . ' FCFA', 'img' => $imgUrl];
                            })->toArray();
                    @endphp
                    <h6 class="product-title"><a href="#" class="product-open" data-id="{{ $produit->idProduit }}" data-name="{{ $dataName }}" data-desc="{{ $dataDesc }}" data-price="{{ $dataPrice }}" data-img="{{ $dataImg }}" data-vendor-name="{{ $vendorName }}" data-vendor-address="{{ $vendorAddress }}" data-stock="{{ $produit->Stock ?? 0 }}" data-category="{{ $produit->Categorie ?? '' }}" data-similar='@json($similar)'>{{ $produit->Nom }}</a></h6>
                    <p class="product-meta mb-2">{{ \Illuminate\Support\Str::limit($produit->Description, 60) }}</p>
                    <div class="mt-auto d-flex justify-content-between align-items-center">
                        <div class="product-price">{{ number_format($produit->Prix, 0, ',', ' ') }} FCFA</div>
                        <button type="button" class="btn btn-sm btn-outline-secondary product-open" data-id="{{ $produit->idProduit }}" data-name="{{ $dataName }}" data-desc="{{ $dataDesc }}" data-price="{{ $dataPrice }}" data-img="{{ $dataImg }}" data-vendor-name="{{ $vendorName }}" data-vendor-address="{{ $vendorAddress }}" data-stock="{{ $produit->Stock ?? 0 }}" data-category="{{ $produit->Categorie ?? '' }}" data-similar='@json($similar)'>Voir</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
