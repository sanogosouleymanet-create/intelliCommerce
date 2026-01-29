<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du produit</title>
    <link rel="stylesheet" href="{{ asset('css/StyleProduit.css') }}">
</head>
<body>
<section class="card">
    <h2>Détails du produit</h2>
    <div class="produit-detail">
        <img src="{{ $produit->Image ? asset('storage/' . $produit->Image) : asset('images/placeholder.png') }}" alt="Image du produit" class="produit-image">
        <h3 id="p-nom">{{ $produit->Nom }}</h3>
        <p id="p-desc">{{ $produit->Description }}</p>
        <p><strong>Prix: </strong><span id="p-prix">{{ number_format($produit->Prix,0,',',' ') }} FCFA</span></p>
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
            <button type="submit" class="btn">Enregistrer</button>
            <button type="button" id="cancelEdit" class="btn">Annuler</button>
        </div>
    </form>
</section>

<script>
    (function(){
        const editBtn = document.getElementById('editBtn');
        const deleteBtn = document.getElementById('deleteBtn');
        const editForm = document.getElementById('editForm');
        const cancelEdit = document.getElementById('cancelEdit');
        const produitId = '{{ $produit->idProduit }}';

        editBtn?.addEventListener('click', function(){
            editForm.style.display = editForm.style.display === 'block' ? 'none' : 'block';
        });

        cancelEdit?.addEventListener('click', function(){
            editForm.style.display = 'none';
        });

        editForm?.addEventListener('submit', function(e){
            e.preventDefault();
            const data = new FormData(editForm);
            data.set('_token', document.querySelector('input[name="_token"]').value);
            data.set('_method', 'PUT');

            fetch('/produits/' + produitId, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: data
            }).then(r => r.json()).then(json => {
                if(json.success){
                    alert(json.message || 'Mis à jour');
                    // refresh page to reflect changes
                    window.location.reload();
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
            data.set('_token', document.querySelector('input[name="_token"]').value);

            fetch('/produits/' + produitId + '/delete', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: data
            }).then(r => r.json()).then(json => {
                if(json.success){
                    alert(json.message || 'Supprimé');
                    // go back to products list
                    window.location.href = '/produits';
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
</body>
</html>
