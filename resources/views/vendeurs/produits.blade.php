@php
    // Page e‑commerce pour l'espace Vendeur
    // Variables attendues : $vendeur, $produits
@endphp



<section class="container-fluid pt-0 pb-3 vendeurs-shop">
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <!-- Filters bar placed above all sections -->
    <div class="filters-bar card p-3 mb-3">
        <form id="filterForm" method="GET" action="{{ url('/vendeur/produits') }}" class="d-flex align-items-center gap-2" style="width:100%;overflow-x:auto;">
            <input type="text" name="recherche" value="{{ request('recherche') }}" class="form-control" placeholder="Nom, description..." style="min-width:220px;max-width:420px;">
            <select name="categorie" class="form-select" style="min-width:160px;max-width:260px;">
                <option value="">Toutes les categories</option>
                <option value="Electronique" {{ request('categorie') == 'Electronique' ? 'selected' : '' }}>Électronique</option>
                <option value="Vetements" {{ request('categorie') == 'Vetements' ? 'selected' : '' }}>Vêtements</option>
                <option value="Chaussures" {{ request('categorie') == 'Chaussures' ? 'selected' : '' }}>Chaussures</option>
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
                <button type="submit" class="btn btn-primary">Appliquer</button>
                <a href="{{ url('/vendeur/produits') }}" class="btn btn-outline-secondary">Réinitialiser</a>
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
                                <div class="product-card card h-100">
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
                                        <div class="mt-auto d-flex justify-content-between align-items-center">
                                            <div class="product-price fw-bold">{{ number_format($produit->Prix ?? 0, 0, ',', ' ') }} FCFA</div>
                                            <div class="d-flex gap-2">
                                                <a href="/produit/{{ $produit->idProduit ?? $produit->id }}" class="btn btn-sm btn-outline-secondary produit-link">Voir</a>
                                                <a href="/produits/{{ $produit->idProduit ?? $produit->id }}/edit" class="btn btn-sm btn-outline-primary">Modifier</a>
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
<a href="#openAdd" id="fabAdd" class="fab-add btn btn-primary">+ Ajouter un produit</a>

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
                        <option value="Chaussures">Chaussures</option>
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

<script>
(function(){
    // AJAX filter submission to update product-list in place
    const filter = document.getElementById('filterForm');
    filter?.addEventListener('submit', async function(e){
        e.preventDefault();
        console.log('filterForm submit', { action: filter?.action });
        const params = new URLSearchParams(new FormData(filter));
        const fetchUrl = filter.action + (params.toString() ? ('?' + params.toString() + '&partial=1') : '?partial=1');
        try{
            const res = await fetch(fetchUrl, { headers: {'X-Requested-With': 'XMLHttpRequest'}, credentials: 'same-origin' });
            if(!res.ok){
                console.warn('Filter fetch returned not ok', res.status);
                // fallback to normal GET submission
                filter.submit();
                return;
            }
            const html = await res.text();
            const tmp = document.createElement('div'); tmp.innerHTML = html;
            const inner = tmp.querySelector('#product-list') || tmp;
            const container = document.getElementById('product-list');
            if(container && inner){
                container.innerHTML = inner.innerHTML;
                console.log('Filter: updated product-list via AJAX');
            } else {
                console.warn('Filter: response did not contain #product-list, falling back');
                filter.submit();
                return;
            }
            // update visible URL without partial flag
            const publicUrl = filter.action + (params.toString() ? ('?' + params.toString()) : ''); history.replaceState(null,'', publicUrl);
        }catch(err){
            console.error('Filter fetch error', err);
            // fallback to standard submit so filtering still works
            filter.submit();
        }
    });

    // product link interception to fetch details via AJAX when available
    document.getElementById('product-list')?.addEventListener('click', function(e){
        const a = e.target.closest && e.target.closest('a.produit-link'); if(!a) return; e.preventDefault();
        const url = a.href;
        fetch(url, { headers: {'X-Requested-With': 'XMLHttpRequest'}, credentials: 'same-origin' })
            .then(r => { if(!r.ok){ window.location.href = url; throw 'nav'; } return r.text(); })
            .then(html => {
                // Replace main content if PageVendeur expects it
                try{ if(typeof replaceMainContent === 'function'){ replaceMainContent(html); return; } }catch(e){}
                const main = document.querySelector('#main-content'); if(main){ main.innerHTML = html; history.pushState(null,'', url); }
            }).catch(()=>{});
    });

    // modal controls
    document.getElementById('openAddBtn')?.addEventListener('click', function(e){ e.preventDefault(); document.getElementById('addModal').style.display='flex'; document.getElementById('addModal').setAttribute('aria-hidden','false'); });
    document.getElementById('fabAdd')?.addEventListener('click', function(e){ e.preventDefault(); document.getElementById('addModal').style.display='flex'; document.getElementById('addModal').setAttribute('aria-hidden','false'); });
    document.getElementById('closeAdd')?.addEventListener('click', function(){ document.getElementById('addModal').style.display='none'; document.getElementById('addModal').setAttribute('aria-hidden','true'); });
})();
</script>
