@php
    $items = $items ?? collect();
@endphp
<div style="margin-bottom:12px"><h2 style="margin:0 0 8px 0">Les plus recherchés</h2></div>
@if($items->isEmpty())
    <div class="alert alert-info">Aucun produit trouvé.</div>
@else
    <div class="product-grid" style="margin-top:12px;" id="top-items-grid">
        @foreach($items as $produit)
            <div class="product-card card @if($produit->Promotion && isset($produit->Reduction) && $produit->Reduction > 0) product-card-promo @endif">
                <div class="position-relative">
                    @if($produit->Promotion && isset($produit->Reduction) && $produit->Reduction > 0)
                        <span class="badge-promo" style="border-radius:4px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.2);">
                            <span style="background:#ffc107;color:#000;padding:3px 6px;font-size:0.7rem;font-weight:600;">En promotion</span>
                            <span style="background:#e65100;color:#fff;padding:3px 5px;font-size:0.7rem;font-weight:700;">-{{ $produit->Reduction }}%</span>
                        </span>
                    @endif
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
                        @if($produit->Promotion && isset($produit->Reduction) && $produit->Reduction > 0 && isset($produit->PrixOriginal) && $produit->PrixOriginal > $produit->Prix)
                            <div class="product-price" style="display:flex;flex-direction:column;align-items:flex-start;">
                                <span style="color:#e53935;text-decoration:line-through;font-size:0.9em;">{{ number_format($produit->PrixOriginal, 0, ',', ' ') }} FCFA</span>
                                <span style="color:#1e88e5;font-weight:700;font-size:1.1em;">{{ number_format($produit->Prix, 0, ',', ' ') }} FCFA</span>
                            </div>
                        @else
                            <div class="product-price">{{ number_format($produit->Prix ?? 0, 0, ',', ' ') }} FCFA</div>
                        @endif
                        <button type="button" class="btn btn-sm btn-outline-secondary product-open" data-id="{{ $produit->idProduit }}" data-name="{{ $dataName }}" data-desc="{{ $dataDesc }}" data-price="{{ $dataPrice }}" data-img="{{ $dataImg }}" data-vendor-name="{{ $vendorName }}" data-vendor-address="{{ $vendorAddress }}" data-stock="{{ $produit->Stock ?? 0 }}" data-category="{{ $produit->Categorie ?? '' }}" data-similar='@json($similar)'>Voir</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
