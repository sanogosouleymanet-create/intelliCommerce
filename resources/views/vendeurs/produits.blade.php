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
                                            <div class="product-price fw-bold">{{ number_format($produit->Prix ?? 0, 0, ',', ' ') }} FCFA</div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary product-open" data-id="{{ $produit->idProduit }}" data-name="{{ $dataName }}" data-desc="{{ $dataDesc }}" data-price="{{ $dataPrice }}" data-img="{{ $dataImg }}" data-vendor-name="{{ $vendorName }}" data-vendor-address="{{ $vendorAddress }}" data-stock="{{ $produit->Stock ?? 0 }}" data-category="{{ $produit->Categorie ?? '' }}" data-similar='{{ $dataSimilar }}'>Voir</button>
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
            .then(r => {
                if(r.redirected || /login|connexion/i.test(r.url) || r.status === 401 || r.status === 403){ window.location.href = r.url; throw 'auth'; }
                if(!r.ok){ window.location.href = url; throw 'nav'; }
                return r.text();
            })
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

    // Intercepter l'envoi du formulaire d'ajout pour faire un POST AJAX
    const formProduit = document.getElementById('formProduit');
    formProduit?.addEventListener('submit', async function(e){
        e.preventDefault();
        const submitBtn = formProduit.querySelector('button[type="submit"]');
        if(submitBtn) submitBtn.disabled = true;
        try{
            const data = new FormData(formProduit);
            const res = await fetch(formProduit.action, {
                method: 'POST',
                body: data,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            if(!res.ok){
                const j = await res.json().catch(()=>null);
                alert(j?.message || 'Erreur lors de l\u2019ajout du produit');
                return;
            }
            const json = await res.json().catch(()=>null);
            if(json && json.success){
                // Rafraîchir la liste des produits via l'endpoint partiel
                try{
                    const fetchUrl = '/vendeur/produits?partial=1';
                    const resp = await fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
                    if(resp.ok){
                        const html = await resp.text();
                        const tmp = document.createElement('div'); tmp.innerHTML = html;
                        const inner = tmp.querySelector('#product-list') || tmp;
                        const container = document.getElementById('product-list');
                        if(container && inner) container.innerHTML = inner.innerHTML;
                    }
                }catch(err){ console.error('refresh error', err); }

                // fermer la modal
                const modal = document.getElementById('addModal');
                if(modal){ modal.style.display = 'none'; modal.setAttribute('aria-hidden','true'); }
            } else {
                alert(json?.message || 'Réponse inattendue');
            }
        }catch(err){
            console.error('submit error', err);
            alert('Erreur réseau lors de l\u2019ajout');
        } finally {
            if(submitBtn) submitBtn.disabled = false;
        }
    });
})();

// Product detail view handler
(function(){
    // history stack: each entry { html, scroll }
    let savedStack = [];
    function renderDetail(data){
        // parse similar products if provided as JSON string
        let similar = [];
        try{ if(data.similar) similar = JSON.parse(data.similar); }catch(e){ similar = []; }
        const similarHtml = similar.length ? `<div style="margin-top:12px">
                <h5>Produits similaires</h5>
                <div class="product-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:18px;">${similar.map(s => `
                    <div class="product-card card">
                        <div class="position-relative">
                            <img src="${s.img}" class="card-img-top" alt="${s.name}">
                            <button class="add-to-cart" title="Ajouter au panier" data-id="${s.id}" aria-label="Ajouter ${s.name} au panier">
                                <i class="fa fa-cart-plus"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <h6 class="product-title">
                                <button type="button" class="product-open btn-link" data-id="${s.id}" data-name="${s.name.replace(/\"/g,'')}" data-desc="" data-price="${s.price}" data-img="${s.img}" data-vendor-name="${data.vendorName||''}" data-vendor-address="${data.vendorAddress||''}" data-stock="" data-category="" data-similar="">${s.name}</button>
                            </h6>
                            <p class="product-meta mb-2">${s.name.substring(0,60)}...</p>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <div class="product-price">${s.price}</div>
                                <button type="button" class="btn btn-sm btn-outline-secondary product-open" data-id="${s.id}" data-name="${s.name.replace(/\"/g,'')}" data-desc="" data-price="${s.price}" data-img="${s.img}" data-vendor-name="${data.vendorName||''}" data-vendor-address="${data.vendorAddress||''}" data-stock="" data-category="" data-similar="">Voir</button>
                            </div>
                        </div>
                    </div>`).join('')}</div></div>` : '';

        // details block: prix, stock, catégorie, boutique
        const detailsHtml = `<div style="margin-top:8px;padding:12px;border-radius:6px;background:#86d0df;color:#000">
                <div style="font-weight:700">Prix: <span style="font-weight:400">${data.price||''}</span></div>
                <div style="font-weight:700;margin-top:6px">Stock: <span style="font-weight:400">${data.stock||''}</span></div>
                <div style="font-weight:700;margin-top:6px">Catégorie: <span style="font-weight:400">${data.category||''}</span></div>
                <div style="font-weight:700;margin-top:6px">Boutique: <span style="font-weight:400">${data.vendorName||''}</span></div>
            </div>`;

        const html = `
            <div class="product-detail" style="display:flex;gap:18px;align-items:flex-start;padding:12px;background:#fff;border-radius:8px;">
                <div style="flex:1;max-width:520px;min-width:0">
                    <img src="${data.img || ''}" alt="${data.name||''}" style="width:100%;height:auto;max-height:520px;object-fit:contain;border-radius:8px;display:block;" />
                </div>
                <div style="width:360px;display:flex;flex-direction:column;gap:12px;">
                    <h2 style="margin:0">${data.name||''}</h2>
                    <div style="color:#1e88e5;font-weight:700;font-size:1.1rem">${data.price||''}</div>
                    ${detailsHtml}
                    <p style="color:#444;flex:1;white-space:pre-wrap">${data.desc||''}</p>
                    <div style="display:flex;gap:8px;align-items:center">
                        <button class="btn btn-sm btn-outline-secondary js-back" style="padding:10px 14px;border-radius:8px">← Retour à la liste</button>
                        <button class="btn btn-primary" style="padding:10px 14px;border-radius:8px"><i class="fa fa-cart-plus" aria-hidden="true"></i>&nbsp;Ajouter au panier</button>
                    </div>
                </div>
            </div>
            ${similarHtml ? `<div class="similar-full" style="margin-top:18px;padding:12px;background:transparent;border-radius:6px">${similarHtml}</div>` : ''}
        `;
        const container = document.getElementById('product-list');
        if(!container) return;
        // push current view onto stack so we can return to it
        savedStack.push({ html: container.innerHTML, scroll: window.scrollY || window.pageYOffset || 0 });
        container.innerHTML = html;
        // push history state so refresh/back behavior is preserved
        try{ history.pushState({ produitId: data.id || null }, '', data.id ? ('?produit=' + encodeURIComponent(data.id)) : window.location.pathname); }catch(e){}
    }
    function restoreMain(){
        const container = document.getElementById('product-list');
        if(!container) return;
        if(savedStack.length){
            const entry = savedStack.pop();
            container.innerHTML = entry.html;
            if(typeof entry.scroll === 'number'){
                window.scrollTo({ top: entry.scroll, left: 0, behavior: 'auto' });
            }
        }
    }
    document.addEventListener('click', function(e){
        const btn = e.target.closest('.product-open');
        if(btn){
            e.preventDefault();
            const data = {
                id: btn.dataset.id,
                name: btn.dataset.name || '',
                desc: btn.dataset.desc || '',
                price: btn.dataset.price || '',
                img: btn.dataset.img || '',
                vendorName: btn.dataset.vendorName || '',
                vendorAddress: btn.dataset.vendorAddress || '',
                stock: btn.dataset.stock || '',
                category: btn.dataset.category || '',
                similar: btn.dataset.similar || ''
            };

            // If important details are missing (vendor, stock or similar), fetch server fragment
            const needsAjax = !(data.vendorName || data.vendorAddress) || data.stock === '' || !data.similar;
            if(needsAjax && data.id){
                const url = '/produit/' + encodeURIComponent(data.id);
                const container = document.getElementById('product-list');
                if(!container){ renderDetail(data); return; }
                // push current view so closing the fragment returns here
                savedStack.push({ html: container.innerHTML, scroll: window.scrollY || window.pageYOffset || 0 });
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                    .then(resp => {
                        if(resp.redirected || /login|connexion/i.test(resp.url) || resp.status === 401 || resp.status === 403){ window.location.href = resp.url; throw 'auth'; }
                        if(!resp.ok) throw new Error('non-ok');
                        return resp.text();
                    })
                    .then(html => {
                        container.innerHTML = html;
                        try{ history.pushState({ produitId: data.id || null }, '', '?produit=' + encodeURIComponent(data.id)); }catch(e){}
                    })
                    .catch(err => { console.error('Fetch produit fragment failed', err); renderDetail(data); });
                return;
            }

            renderDetail(data);
            return;
        }
        if(e.target.closest('.js-back')){
            e.preventDefault();
            // navigate back in history; popstate handler will restore the view
            if(history.state && history.state.produitId) history.back(); else restoreMain();
            return;
        }

        // Add to cart on similar product or fragment
        const addBtn = e.target.closest('.add-to-cart-similar, .add-to-cart-fragment');
        if(addBtn){
            e.preventDefault();
            const id = addBtn.dataset.id;
            // dispatch event to add product to cart; do not change button state here
            document.dispatchEvent(new CustomEvent('product-added-to-cart', { detail: { id } }));
            return;
        }
    });
    // handle browser back/forward navigation to restore list/detail state
    window.addEventListener('popstate', function(event){
        try{
            // If we have a savedStack entry, the previous view is the list we saved — restore it immediately.
            if(Array.isArray(savedStack) && savedStack.length){
                restoreMain();
                return;
            }
            const state = event.state;
            if(!state || !state.produitId){
                // no produit in history state -> restore product list view
                if(typeof restoreMain === 'function'){
                    restoreMain();
                } else {
                    window.location.reload();
                }
                return;
            }
            // state contains produitId: keep detail visible (or implement loading if needed)
        }catch(err){ console.error('popstate handler error', err); }
    });
})();
</script>
