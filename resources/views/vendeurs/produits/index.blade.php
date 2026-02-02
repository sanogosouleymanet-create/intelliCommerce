<header class="header">
    <h1>Liste des produits</h1>
    <div class="account">
        <i class="fa-solid fa-user"></i>
        @if(isset($vendeur->Prenom) || isset($vendeur->Nom))
            {{ trim(($vendeur->Prenom ?? '') . ' ' . ($vendeur->Nom ?? '')) }}
        @else
            Mon Compte
        @endif
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function(){
    // Si cette vue est rendue dans le contexte de PageVendeur (URL initiale = PageVendeur),
    // on répare l'URL de la barre d'adresse pour pointer vers /vendeur/produits afin que
    // le rafraîchissement du navigateur recharge la bonne route et que les handlers AJAX fonctionnent.
    try{
        const pageVendeurPath = '{{ route("PageVendeur") }}';
        const produitsPath = '{{ url("/vendeur/produits") }}';
        if(window.location.pathname === pageVendeurPath){
            history.replaceState(null, '', produitsPath + window.location.search);
        }
    }catch(e){
        // ignore si les helpers Blade ne sont pas disponibles côté client
        console.debug('No route repair needed', e);
    }
});
</script>

<!-- Bouton d'ouverture du modal d'ajout (onclick inline pour fonctionner lors de chargement AJAX) -->
<button id="openAdd" class="Ajout" onclick="document.getElementById('addModal').style.display='flex';document.getElementById('addModal').setAttribute('aria-hidden','false');">+</button>

<!-- Modal d'ajout de produit -->
<div id="addModal" class="modal" aria-hidden="true" style="display:none;" onclick="if(event.target===this){this.style.display='none';this.setAttribute('aria-hidden','true');}">
    <div class="modal-content">
        <button type="button" class="close" id="closeAdd" onclick="document.getElementById('addModal').style.display='none';document.getElementById('addModal').setAttribute('aria-hidden','true');">×</button>
        <h2>Ajouter un Produit</h2>
        <form id="formProduit" enctype="multipart/form-data" method="POST" action="{{ route('produits.AjouterProduit') }}">
            @csrf
            <div>
                <label for="Nom">Nom du Produit:</label>
                <input type="text" id="Nom" name="Nom" required>
            </div>
            <div>
                <label for="Description">Description du Produit:</label>
                    <textarea id="Description" name="Description" placeholder="Donnez une description à votre produit" required></textarea>
                </div>
                <div>
                    <label for="Prix">Prix du Produit (FCFA):</label>
                    <input type="number" id="Prix" name="Prix" required>
                </div>
                <div>
                    <label for="Stock">Stock initial:</label>
                    <input type="number" id="Stock" name="Stock" value="0" min="0" required>
                </div>
                <!-- Le stock est calculé automatiquement lors de la création -->
                <div>
                    <label for="Categorie">Catégorie du Produit:</label>
                    <select id="Categorie" name="Categorie" required>
                        <option value="" disabled selected>Sélectionner une catégorie</option>
                        <option value="Electronique">Électronique</option>
                        <option value="Vetements">Vêtements</option>
                        <option value="Chaussures">Chaussures</option>
                        <option value="Aliment">Aliment</option>
                        <option value="Livres">Livres</option>
                        <option value="Autres">Autres</option>
                    </select>
                </div>
                <div>
                    <label for="Image">URL de l'Image du Produit:</label>
                    <input type="file" id="Image" name="Image" required>
                </div>
                <div style="margin-top:10px;">
                    <button type="submit">Ajouter le Produit</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Formulaire de recherche, filtre et tri -->
        <form id="filterForm" method="GET" action="{{ url()->current() }}" class="filtres">
            <section class="orders-header">
                <div  class="produits-filtres">
                <!--Recherche par nom-->
                <input type="text" name="recherche" placeholder="Rechercher un produit" class="search-input" value="{{ request('recherche') }}">
                <!--Filtre par catégorie-->
                <select name="categorie" class="filter-select">
                    <option value="">Toutes les catégories</option>
                    <option value="Electronique" {{ request('categorie') == 'Electronique' ? 'selected' : '' }}>Électronique</option>
                    <option value="Vetements" {{ request('categorie') == 'Vetements' ? 'selected' : '' }}>Vêtements</option>
                    <option value="Chaussures" {{ request('categorie') == 'Chaussures' ? 'selected' : '' }}>Chaussures</option>
                    <option value="Aliment" {{ request('categorie') == 'Aliment' ? 'selected' : '' }}>Aliment</option>
                    <option value="Livres" {{ request('categorie') == 'Livres' ? 'selected' : '' }}>Livres</option>
                    <option value="Autres" {{ request('categorie') == 'Autres' ? 'selected' : '' }}>Autres</option>
                </select>

                <!---Trier par prix-->
                <select name="tri_prix" class="filter-select">
                    <option value="">Trier par prix</option>
                    <option value="asc" {{ request('tri_prix') == 'asc' ? 'selected' : '' }}>Prix croissant</option>
                    <option value="desc" {{ request('tri_prix') == 'desc' ? 'selected' : '' }}>Prix décroissant</option>
                    <option value="recente" {{ request('tri_prix') == 'recente' ? 'selected' : '' }}>Produits récents</option>
                </select>
                <button type="submit" class="filter-btn">Filtrer</button>
            </div>
            </section>
            
        </form>

    <!-- Affichage de la liste des produits -->
    <div id="product-list" style="position:relative;">
        <div id="loading" class="loading-overlay" style="display:none;">
            <div class="spinner"></div>
        </div>
        @include('vendeurs.produits._list')
    </div>

    <script>
        (function(){
            const form = document.getElementById('filterForm');
            if(!form) return;
            form.addEventListener('submit', async function(e){
                e.preventDefault();
                const params = new URLSearchParams(new FormData(form));
                const basePath = window.location.pathname;
                // URL shown in the browser (without partial param) - preserve current path
                const publicUrl = basePath + (params.toString() ? ('?' + params.toString()) : '');
                // URL requested to the server to receive only the partial HTML (same path)
                const fetchUrl = basePath + (params.toString() ? ('?' + params.toString() + '&partial=1') : '?partial=1');
                try{
                    const container = document.getElementById('product-list');
                    const loader = container ? container.querySelector('#loading') : null;
                    if(loader) loader.style.display = 'flex';
                    const res = await fetch(fetchUrl, {headers: {'X-Requested-With': 'XMLHttpRequest'}});
                    if(res.ok){
                        const html = await res.text();
                        if(container){
                            const tmp = document.createElement('div');
                            tmp.innerHTML = html;
                            // Prefer a nested #product-list if the server accidentally returned the full page
                            const inner = tmp.querySelector('#product-list') || tmp;
                            // Remove an accidental header if present to avoid duplication
                            const hdr = inner.querySelector('header.header');
                            if(hdr && hdr.parentNode) hdr.parentNode.removeChild(hdr);
                            container.innerHTML = inner.innerHTML;
                        }
                        // update URL (visible) without reload and without the partial flag
                        history.replaceState(null, '', publicUrl);
                    } else {
                        // fallback to full navigation (loads full view)
                        window.location.href = publicUrl;
                    }
                } catch(err){
                    // on network error fallback to full page load
                    window.location.href = publicUrl;
                } finally{
                    const container2 = document.getElementById('product-list');
                    const loader2 = container2 ? container2.querySelector('#loading') : null;
                    if(loader2) loader2.style.display = 'none';
                }
            });

            // Handle browser navigation (back / forward) by reloading partial list when on /produits
            window.addEventListener('popstate', function(event){
                const path = window.location.pathname;
                if(path.endsWith('/produits') || path === '/produits'){
                    const qs = window.location.search || '';
                    const base = window.location.pathname;
                    const fetchUrl = base + (qs ? (qs + '&partial=1') : '?partial=1');
                    (async function(){
                        try{
                            const res = await fetch(fetchUrl, {headers: {'X-Requested-With': 'XMLHttpRequest'}});
                            if(res.ok){
                                const html = await res.text();
                                const container = document.getElementById('product-list');
                                if(container){
                                    const tmp = document.createElement('div');
                                    tmp.innerHTML = html;
                                    const inner = tmp.querySelector('#product-list') || tmp;
                                    const hdr = inner.querySelector('header.header');
                                    if(hdr && hdr.parentNode) hdr.parentNode.removeChild(hdr);
                                    container.innerHTML = inner.innerHTML;
                                }
                            }
                        } catch(e){
                            // ignore errors, let browser handle navigation
                        }
                    })();
                }
            });

            // Helper to refresh current filters via AJAX (used on reload or key interception)
            async function ajaxRefreshCurrentList(){
                const formEl = document.getElementById('filterForm');
                if(!formEl) return;
                const params = new URLSearchParams(new FormData(formEl));
                const fetchUrl = formEl.action + (params.toString() ? ('?' + params.toString() + '&partial=1') : '?partial=1');
                const container = document.getElementById('product-list');
                const loader = container ? container.querySelector('#loading') : null;
                if(loader) loader.style.display = 'flex';
                try{
                    const res = await fetch(fetchUrl, {headers: {'X-Requested-With': 'XMLHttpRequest'}});
                    if(res.ok){
                        const html = await res.text();
                        if(container){
                            const tmp = document.createElement('div');
                            tmp.innerHTML = html;
                            const inner = tmp.querySelector('#product-list') || tmp;
                            const hdr = inner.querySelector('header.header');
                            if(hdr && hdr.parentNode) hdr.parentNode.removeChild(hdr);
                            container.innerHTML = inner.innerHTML;
                        }
                        // keep URL in sync (without partial)
                        const publicUrl = formEl.action + (params.toString() ? ('?' + params.toString()) : '');
                        history.replaceState(null, '', publicUrl);
                    } else {
                        // fallback to full navigation
                        window.location.href = formEl.action + (params.toString() ? ('?' + params.toString()) : '');
                    }
                } catch(err){
                    // fallback to full navigation on error
                    window.location.href = formEl.action + (params.toString() ? ('?' + params.toString()) : '');
                } finally{
                    if(loader) loader.style.display = 'none';
                }
            }

            // Intercept common reload keys (F5, Ctrl/Cmd+R) to refresh the partial list instead of full reload
            window.addEventListener('keydown', function(e){
                const path = window.location.pathname;
                if(!(path.endsWith('/produits') || path === '/produits')) return;
                const isF5 = e.key === 'F5';
                const isCtrlR = (e.ctrlKey || e.metaKey) && (e.key === 'r' || e.key === 'R');
                if(isF5 || isCtrlR){
                    e.preventDefault();
                    ajaxRefreshCurrentList();
                }
            });

            // On reload navigation, also fetch partial list so browser refresh behaves like partial update
            try{
                const nav = performance.getEntriesByType('navigation')[0];
                const isReload = nav ? nav.type === 'reload' : (performance.navigation && performance.navigation.type === 1);
                if(isReload){
                    const path = window.location.pathname;
                    if(path.endsWith('/produits') || path === '/produits'){
                        // run async refresh but don't block page load
                        setTimeout(ajaxRefreshCurrentList, 0);
                    }
                }
            }catch(e){
                // ignore errors from performance API
            }

            // Intercept clicks on product links inside the product-list to load details via AJAX into the SPA
            document.getElementById('product-list')?.addEventListener('click', function(e){
                const anchor = e.target.closest && e.target.closest('a.produit-link');
                if(!anchor) return;
                e.preventDefault();
                const url = anchor.href;
                fetch(url, { headers: {'X-Requested-With': 'XMLHttpRequest'} })
                    .then(res => { if(!res.ok){ window.location.href = url; throw new Error('nav'); } return res.text(); })
                    .then(html => {
                        // If PageVendeur.replaceMainContent exists, use it so scripts are executed
                        if(typeof replaceMainContent === 'function'){
                            replaceMainContent(html);
                        } else {
                            const main = document.querySelector('.main-content');
                            if(main){
                                const tmp = document.createElement('div'); tmp.innerHTML = html;
                                const inner = tmp.querySelector('#product-list') ? tmp.querySelector('#product-list') : tmp;
                                const hdr = inner.querySelector('header.header'); if(hdr && hdr.parentNode) hdr.parentNode.removeChild(hdr);
                                main.innerHTML = inner.innerHTML;
                            } else {
                                window.location.href = url;
                            }
                        }
                        history.pushState(null, '', url);
                    }).catch(()=>{});
            });
        })();
    </script>
    <script>
        (function(){
            const addForm = document.getElementById('formProduit');
            if(!addForm) return;
            addForm.addEventListener('submit', async function(e){
                e.preventDefault();
                const submitBtn = addForm.querySelector('button[type="submit"]');
                if(submitBtn){ submitBtn.disabled = true; submitBtn.textContent = 'Envoi...'; }
                const fd = new FormData(addForm);
                try{
                    const res = await fetch(addForm.action || '/produits', {
                        method: 'POST',
                        body: fd,
                        headers: {'X-Requested-With': 'XMLHttpRequest'}
                    });
                    if(res.ok){
                        const data = await res.json();
                        // close modal
                        const modal = document.getElementById('addModal');
                        if(modal){ modal.style.display='none'; modal.setAttribute('aria-hidden','true'); }
                        addForm.reset();
                        // refresh partial list
                        const listRes = await fetch(window.location.pathname + (window.location.search ? (window.location.search + '&partial=1') : '?partial=1'), {headers:{'X-Requested-With':'XMLHttpRequest'}});
                        if(listRes.ok){
                            const html = await listRes.text();
                            const container = document.getElementById('product-list');
                            if(container){
                                const tmp = document.createElement('div');
                                tmp.innerHTML = html;
                                const inner = tmp.querySelector('#product-list') || tmp;
                                const hdr = inner.querySelector('header.header');
                                if(hdr && hdr.parentNode) hdr.parentNode.removeChild(hdr);
                                container.innerHTML = inner.innerHTML;
                            }
                        } else {
                            window.location.reload();
                        }
                    } else if(res.status === 422){
                        const json = await res.json();
                        const errors = json.errors || {};
                        const first = Object.values(errors)[0];
                        alert(first ? first[0] : 'Validation failed');
                    } else {
                        const text = await res.text();
                        alert('Erreur: ' + text.substring(0,200));
                    }
                } catch(err){
                    alert('Erreur réseau: ' + err.message);
                } finally {
                    if(submitBtn){ submitBtn.disabled = false; submitBtn.textContent = 'Ajouter le Produit'; }
                }
            });
        })();
    </script>
 <style>
    .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}
.account {
    background: white;
    padding: 10px 15px;
    border-radius: 8px;
    cursor: pointer;
}
h1 {
    color: white;
}
.Ajout {
    font-size: 24px;
    text-decoration: none;
    background-color: #030c25;
    color: white;
    padding: 10px 15px;
    border-radius: 50%;
    position: fixed;
    bottom: 20px;
    right: 20px;
    text-align: center;
    z-index: 1000;
}
.modal{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.5);
    display:flex;
    align-items:center;
    justify-content:center;
    z-index:1000;
}

/*Modal styles (minimal)*/
.modal-content{
    background:#fff;
    padding:20px;
    border-radius:8px;
    max-width:600px;
    width:90%;
    position:relative;
}
.modal-content h2
{
    margin-top:0
}
.close{
    position:absolute;
    top:8px;
    right:8px;
    border:none;
    background:transparent;
    font-size:22px;
    cursor:pointer;
}
.Ajout{
    font-size:20px;
    padding:6px 12px;
    border-radius:6px;
    cursor:pointer;
    width:60px;
    height:60px;
    border-radius:50%;
}
.modal-content label{
    display:block;
    margin-top:8px;
}
.modal-content input,.modal-content textarea,.modal-content select{
    width:100%;
    padding:6px;
    margin-top:4px;
}

.bouton-rond{
    
    background-color:#030c25;
    color:white;
    font-size:24px;
    text-align:center;
    line-height:40px;
    position:fixed;
    bottom:20px;
    right:20px;
    cursor:pointer;
}
.produit{
    background-color: white;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1) ;
    display: inline-block;
    width: 300px;
    height: 250px;
    text-align: center;
    
    border-radius: 8px;
    padding: 15px;
    margin: 15px;
    color: black;
}   
.produit-image {
    position: center;
    width: 200px;
    height: 120px;
    /*object-fit: cover;*/
    margin-bottom: 10px;
}
.produit-image-show{
    position: center;
    width: 225px;
    height: 225px;
    /*object-fit: cover;*/
    margin-bottom: 10px;
}

/* Conteneur principal */
.produits-filtres {
    display: flex;
    gap: 12px;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;

    background-color: #ffffff;
    padding: 15px 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}

.orders-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
}

/* Champ de recherche */
.search-input {
    padding: 10px 14px;
    width: 250px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    outline: none;
    transition: border-color 0.3s ease;
}

.search-input:focus {
    border-color: #007bff;
}

/* Select (catégorie, prix, etc.) */
.filter-select {
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    cursor: pointer;
    background-color: #fff;
    transition: border-color 0.3s ease;
}

.filter-select:hover,
.filter-select:focus {
    border-color: #007bff;
}

/* Bouton */
.filter-btn {
    padding: 10px 18px;
    border-radius: 8px;
    border: none;
    background-color: #007bff;
    color: #fff;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.filter-btn:hover {
    background-color: #0056b3;
    transform: translateY(-1px);
}

.btn-retirer {
    padding: 10px 18px;
    border-radius: 8px;
    border: none;
    
    background-color: rgba(223, 21, 21, 0.897);
    color: #fff;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}
.btn-retirer:hover {
    background-color: darkred;
    transform: translateY(-1px);
}


@media (max-width: 768px) {
    .produits-filtres {
        flex-direction: column;
        align-items: stretch;
    }

    .search-input,
    .filter-select,
    .filter-btn {
        width: 100%;
    }
}

/* Loading overlay and spinner */
.loading-overlay{
    position:absolute;
    inset:0;
    display:flex;
    align-items:center;
    justify-content:center;
    background: rgba(0,0,0,0.35);
    z-index: 1000;
}
.loading-overlay .spinner{
    width:40px;
    height:40px;
    border-radius:50%;
    border:4px solid rgba(255,255,255,0.2);
    border-top-color: #ffffff;
    animation: spin 1s linear infinite;
}
@keyframes spin{
    from{ transform: rotate(0deg); }
    to{ transform: rotate(360deg); }
}



form#editForm{ 
    max-width:600px; 
    background:rgba(255,255,255,0.03); 
    padding:16px; border-radius:8px; 
}
form#editForm label{ 
    display:block; 
    margin-top:8px; 
    color:#dbeafe; 
}
form#editForm input{ 
    width:100%; 
    padding:8px; 
    border-radius:6px; 
    border:1px solid rgba(255,255,255,0.06); 
}
 </style>