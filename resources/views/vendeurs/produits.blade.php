

<!-- Vue partielle pour la liste des produits dans la page vendeur (PageVendeur.blade.php)-->

<section class="container-fluid pt-0 pb-3 vendeurs-shop position-relative">
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <!-- Filters bar placed above all sections -->
    <div class="filters-bar card p-3 mb-3 position-relative">
        <form id="filterForm" method="GET" action="{{ url('/vendeur/produits') }}" class="d-flex align-items-center gap-2" style="width:100%;overflow-x:auto;">
            <input type="text" name="recherche" value="{{ request('recherche') }}" class="form-control" placeholder="Nom, description..." style="min-width:220px;max-width:420px;">
            <select name="categorie" class="form-select" style="min-width:160px;max-width:260px;">
                <option value="">Toutes les categories</option>
                <option value="Electronique" {{ request('categorie') == 'Electronique' ? 'selected' : '' }}>Électronique</option>
                <option value="Vetements" {{ request('categorie') == 'Vetements' ? 'selected' : '' }}>Vêtements</option>
                <option value="Chaussures-Femme" {{ request('categorie') == 'Chaussures-Femme' ? 'selected' : '' }}>Chaussures Femme</option>
                <option value="Chaussures-Homme" {{ request('categorie') == 'Chaussures-Homme' ? 'selected' : '' }}>Chaussures Homme</option>
                <option value="Mode-Homme" {{ request('categorie') == 'Mode-Homme' ? 'selected' : '' }}>Mode Homme</option>
                <option value="Mode-Femme" {{ request('categorie') == 'Mode-Femme' ? 'selected' : '' }}>Mode Femme</option>
                <option value="Beauté" {{ request('categorie') == 'Beauté' ? 'selected' : '' }}>Beauté</option>
                <option value="Mode-Fille" {{ request('categorie') == 'Mode-Fille' ? 'selected' : '' }}>Mode Fille</option>
                <option value="Mode-Garçon" {{ request('categorie') == 'Mode-Garçon' ? 'selected' : '' }}>Mode Garçon</option>
                <option value="Cuisine&Maison" {{ request('categorie') == 'Cuisine&Maison' ? 'selected' : '' }}>Cuisine & Maison</option>
                <option value="Sports" {{ request('categorie') == 'Sports' ? 'selected' : '' }}>Sports</option>
                 <option value="Aliment" {{ request('categorie') == 'Aliment' ? 'selected' : '' }}>Aliment</option>
                <option value="Livres" {{ request('categorie') == 'Livres' ? 'selected' : '' }}>Livres</option>
                <option value="Autres" {{ request('categorie') == 'Autres' ? 'selected' : '' }}>Autres</option>
            </select>
            <select name="tri_prix" class="form-select" style="min-width:160px;max-width:220px;">
                <option value="">Prix</option>
                <option value="asc" {{ request('tri_prix') == 'asc' ? 'selected' : '' }}>Prix croissant</option>
                <option value="desc" {{ request('tri_prix') == 'desc' ? 'selected' : '' }}>Prix décroissant</option>
                <option value="recente" {{ request('tri_prix') == 'recente' ? 'selected' : '' }}>Produits récents</option>
            </select>
                <div class="d-flex gap-2 ms-auto filters-actions">
                    <button type="button" id="promotionBtn" class="btn btn-warning">Mettre en Promotion</button>
                    <button type="button" id="removePromotionBtn" class="btn btn-outline-secondary">Retirer la Promotion</button>
                </div>
        </form>
    </div>
    

    <div class="row">

        <main class="col-md-9">
            <div id="product-list" class="product-list">
                @if($produits && $produits->count())
                    <div class="product-grid row g-0">

                        @foreach($produits as $produit)
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                <div class="product-card card h-100 @if($produit->Promotion && isset($produit->Reduction) && $produit->Reduction > 0) product-card-promo @endif" style="position:relative;">
                                    <div class="form-check position-absolute" style="top:8px;right:8px;z-index:2;">
                                        <input class="form-check-input promotion-checkbox" type="checkbox" value="{{ $produit->idProduit }}" id="promotionCheck{{ $produit->idProduit }}">
                                    </div>
                                    @if($produit->Promotion && isset($produit->Reduction) && $produit->Reduction > 0)
                                        <span class="badge-promo position-absolute" style="top:8px;left:8px;z-index:2;display:inline-flex;border-radius:4px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.2);">
                                            <span style="background:#ffc107;color:#000;padding:4px 8px;font-size:0.75rem;font-weight:600;">En promotion</span>
                                            <span style="background:#e65100;color:#fff;padding:4px 6px;font-size:0.75rem;font-weight:700;">-{{ $produit->Reduction }}%</span>
                                        </span>
                                    @endif
                                    <div class="position-relative">
                                        @php
                                            $img = trim((string)($produit->Image ?? ''));
                                            $imgUrl = 'https://via.placeholder.com/400x300?text=No+Image';
                                            if($img !== ''){
                                                if(preg_match('/^https?:\/\//i', $img)) $imgUrl = $img;
                                                elseif(\Illuminate\Support\Facades\Storage::exists('public/'.$img)) $imgUrl = asset('storage/'.$img);
                                                elseif(file_exists(public_path($img))) $imgUrl = asset($img);
                                                elseif(file_exists(public_path('images/'.basename($img)))) $imgUrl = asset('images/'.basename($img));
                                            }
                                        @endphp
                                            <img src="{{ $imgUrl }}" class="card-img-top" alt="{{ $produit->Nom }}" style="height: 140px; object-fit: cover; padding: 2px 2px 2px 4px;">
                                    </div>
                                    <div class="card-body d-flex flex-column" style="padding-right:6px;padding-left:6px;">
                                        <h6 class="product-title">{{ $produit->Nom }}</h6>
                                        <p class="product-meta small text-muted mb-2">{{ \Illuminate\Support\Str::limit($produit->Description, 80) }}</p>
                                        @php
                                            $dataName = e($produit->Nom);
                                            $dataDesc = e($produit->Description ?? '');
                                            $dataPrice = number_format($produit->Prix, 0, ',', ' ') . ' FCFA';
                                            $dataImg = $imgUrl;
                                            $vendorName = e($vendeur->NomBoutique ?? ($vendeur->Nom . ' ' . ($vendeur->Prenom ?? '')));
                                            $vendorAddress = e($vendeur->Adresse ?? '');
                                            // produits similaires (même catégorie)
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
                                            $dataSimilar = e(json_encode($similar));
                                        @endphp
                                        <div class="mt-auto d-flex justify-content-between align-items-center">
                                            @if($produit->Promotion && isset($produit->Reduction) && $produit->Reduction > 0 && isset($produit->PrixOriginal) && $produit->PrixOriginal > 0)
                                                <div class="product-price fw-bold" style="display:flex;flex-direction:column;align-items:flex-start;">
                                                    <span style="color:#e53935;text-decoration:line-through;font-size:0.98em;">{{ number_format($produit->PrixOriginal, 0, ',', ' ') }} FCFA</span>
                                                    <span style="color:#1e88e5;font-weight:700;font-size:1.1em;">{{ number_format($produit->Prix, 0, ',', ' ') }} FCFA</span>
                                                </div>
                                            @else
                                                <div class="product-price fw-bold">{{ number_format($produit->Prix ?? 0, 0, ',', ' ') }} FCFA</div>
                                            @endif
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary product-open" data-id="{{ $produit->idProduit }}" data-name="{{ $dataName }}" data-desc="{{ $dataDesc }}" data-price="{{ $dataPrice }}" data-img="{{ $dataImg }}" data-vendor-name="{{ $vendorName }}" data-vendor-address="{{ $vendorAddress }}" data-stock="{{ $produit->Stock ?? 0 }}" data-category="{{ $produit->Categorie ?? '' }}" data-similar='{{ $dataSimilar }} ' style="color: black !important;">Voir</button>
                                                <a href="/produits/{{ $produit->idProduit ?? $produit->id }}/edit" class="btn btn-sm btn-outline-primary" style="color: black !important;">Modifier</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">Aucun produit à afficher.</div>
                @endif
            </div>

        </main>
    </div>
</section>

<!-- Floating add button -->
<a href="#openAdd" id="fabAdd" class="fab-add btn btn-primary" style="color: white !important;">+ Ajouter un produit</a>

<!-- Modal d'ajout (simple) -->
<div id="addModal" class="modal" aria-hidden="true" style="display:none;position:fixed;left:0;top:0;width:100%;height:100%;align-items:center;justify-content:center;background:rgba(0,0,0,0.45);z-index:9999;">
    <div class="modal-content card p-3" style="width:100%;max-width:640px;">
        <button type="button" class="close btn btn-sm btn-outline-secondary" id="closeAdd">×</button>
        <h5>Ajouter un produit</h5>
        <form id="formProduit" enctype="multipart/form-data" method="POST" action="{{ route('produits.AjouterProduit') }}">
            @csrf
            <div class="mb-2">
                <label class="form-label">Nom</label>
                <input type="text" name="Nom" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Description</label>
                <textarea name="Description" class="form-control" required></textarea>
            </div>
            <div class="row">
                <div class="col-md-4 mb-2"><label class="form-label">Prix</label><input type="number" name="Prix" class="form-control" required></div>
                <div class="col-md-4 mb-2"><label class="form-label">Stock</label><input type="number" name="Stock" class="form-control" value="0"></div>
                <div class="col-md-4 mb-2"><label class="form-label">Catégorie</label>
                    <select name="Categorie" class="form-select">
                        <option value="">Sélectionner</option>
                        <option value="Electronique">Électronique</option>
                        <option value="Vetements">Vêtements</option>
                        <option value="Chaussures-Femme">Chaussures Femme</option>
                        <option value="Chaussures-Homme">Chaussures Homme</option>
                        <option value="Mode-Homme">Mode Homme</option>
                        <option value="Mode-Femme">Mode Femme</option>
                        <option value="Beauté">Beauté</option>
                        <option value="Mode-Fille">Mode Fille</option>
                        <option value="Mode-Garçon">Mode Garçon</option>
                        <option value="Cuisine&Maison">Cuisine & Maison</option>
                        <option value="Sports">Sports</option>
                        <option value="Aliment">Aliment</option>
                        <option value="Livres">Livres</option>
                        <option value="Autres">Autres</option>
                    </select>
                </div>
            </div>
            <div class="mb-2"><label class="form-label">Image</label><input type="file" name="Image" class="form-control"></div>
            <div class="d-flex gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </div>
        </form>
    </div>
</div>

