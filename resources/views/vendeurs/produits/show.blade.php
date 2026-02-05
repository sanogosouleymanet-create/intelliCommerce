@php
// Partial product detail used inside PageVendeur SPA
@endphp

<section class="card">
    <h2>Détails du produit</h2>
    <div class="produit-detail">
        <div class="produit-image-show">
            <img src="{{ $produit->Image ? asset('storage/' . $produit->Image) : asset('images/placeholder.png') }}" alt="Image du produit" >
        </div>
        
        <h3 id="p-nom">{{ $produit->Nom }}</h3>
        <p id="p-desc">{{ $produit->Description }}</p>
        <p><strong>Stock: </strong><span id="p-stock">{{ $produit->Stock }}</span></p>
        <p><strong>Catégorie: </strong><span id="p-categorie">{{ $produit->Categorie }}</span></p>
    </div>

    <div id="controls" style="margin-top:12px;">
        <button id="editBtn" class="filter-btn">Modifier</button>
        <button id="deleteBtn" class="btn-retirer" >Retirer</button>
    </div>

    <form id="editForm" style="display:none;margin-top:12px;" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_method" value="PUT">
        <div>
            <label>Nom</label>
            <input type="text" name="Nom" value="{{ $produit->Nom }}">
        </div>
        <div>
            <label>Description</label>
            <textarea name="Description">{{ $produit->Description }}</textarea>
        </div>
        <div>
            <label>Prix (FCFA)</label>
            <input type="number" name="Prix" value="{{ $produit->Prix }}">
        </div>
        <div>
            <label>Stock</label>
            <input type="number" name="Stock" value="{{ $produit->Stock }}" min="0">
        </div>
        <div>
            <label>Catégorie</label>
            <input type="text" name="Categorie" value="{{ $produit->Categorie }}">
        </div>
        <div>
            <label>Image (laisser vide pour conserver)</label>
            <input type="file" name="Image" accept="image/*">
        </div>
        <div style="margin-top:8px;">
            <button type="button" id="saveEdit" class="filter-btn">Enregistrer</button>
            <button type="button" id="cancelEdit" class="filter-btn">Annuler</button>
        </div>
    </form>
</section>

<script>
    (function(){
        const editBtn = document.getElementById('editBtn');
        const deleteBtn = document.getElementById('deleteBtn');
        const editForm = document.getElementById('editForm');
        const cancelEdit = document.getElementById('cancelEdit');
        const saveEditBtn = document.getElementById('saveEdit');
        const produitId = '{{ $produit->idProduit }}';

        editBtn?.addEventListener('click', function(){
            editForm.style.display = editForm.style.display === 'block' ? 'none' : 'block';
        });

        cancelEdit?.addEventListener('click', function(){
            editForm.style.display = 'none';
        });

        function refreshProductView(){
            const url = '/produits/' + produitId;
            const main = document.querySelector('.main-content');
            if(main){
                fetch(url, { headers: {'X-Requested-With': 'XMLHttpRequest'}, credentials: 'same-origin' })
                    .then(r => {
                        if(!r.ok){ window.location.href = url; return null; }
                        return r.text();
                    })
                    .then(html => {
                        if(!html) return;
                        // Prefer to use the SPA helper if available so scripts are handled
                        if(typeof replaceMainContent === 'function'){
                            replaceMainContent(html);
                        } else {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newMain = doc.querySelector('.main-content') || doc.querySelector('main') || doc.querySelector('body');
                            if (newMain) document.querySelector('.main-content').innerHTML = newMain.innerHTML; else document.querySelector('.main-content').innerHTML = html;
                        }
                        history.replaceState(null,'', url);
                    }).catch(()=> { window.location.href = url; });
            } else {
                window.location.reload();
            }
        }

        saveEditBtn?.addEventListener('click', function(e){
            e.preventDefault();
            const data = new FormData(editForm);
            data.set('_token', document.querySelector('input[name="_token"]')?.value || '');
            data.set('_method', 'PUT');

            fetch('/produits/' + produitId, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                body: data
            }).then(r => r.json()).then(json => {
                if(json.success){
                    alert(json.message || 'Mis à jour');
                    refreshProductView();
                } else {
                    alert(json.message || 'Erreur');
                }
            }).catch(err => {
                console.error(err);
                alert('Erreur réseau');
            });
        });

        deleteBtn?.addEventListener('click', function(){
            if(!confirm('Confirmer la suppression de ce produit ?')) return;
            const data = new FormData();
            data.set('_token', document.querySelector('input[name="_token"]')?.value || '');

            fetch('/produits/' + produitId + '/delete', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: data
            }).then(r => r.json()).then(json => {
                if(json.success){
                    alert(json.message || 'Supprimé');
                    // go back to products list in SPA-friendly way
                    if(window.location.pathname.endsWith('/produits')){
                        location.reload();
                    } else {
                        history.replaceState(null,'', '/vendeur/produits');
                        fetch('/vendeur/produits', {headers:{'X-Requested-With':'XMLHttpRequest'}})
                            .then(r => r.text()).then(html => { if(typeof replaceMainContent==='function'){ replaceMainContent(html); } else document.querySelector('.main-content').innerHTML = html; });
                    }
                } else {
                    alert(json.message || 'Erreur');
                }
            }).catch(err => {
                console.error(err);
                alert('Erreur réseau');
            });
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