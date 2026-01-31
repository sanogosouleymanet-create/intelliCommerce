<div class="product-edit-partial card p-3">
    <h5>Modifier le produit</h5>
    <form id="editProduitForm" method="POST" action="{{ url('/produits/'.$produit->idProduit) }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_method" value="PUT">
        <div class="mb-2">
            <label class="form-label">Nom</label>
            <input type="text" name="Nom" class="form-control" value="{{ $produit->Nom }}" required>
        </div>
        <div class="mb-2">
            <label class="form-label">Description</label>
            <textarea name="Description" class="form-control" required>{{ $produit->Description }}</textarea>
        </div>
        <div class="row">
            <div class="col-md-4 mb-2"><label class="form-label">Prix</label><input type="number" name="Prix" class="form-control" value="{{ $produit->Prix }}" required></div>
            <div class="col-md-4 mb-2"><label class="form-label">Stock</label><input type="number" name="Stock" class="form-control" value="{{ $produit->Stock ?? 0 }}"></div>
            <div class="col-md-4 mb-2"><label class="form-label">Catégorie</label>
                <select name="Categorie" class="form-select">
                    <option value="">Sélectionner</option>
                    @foreach(['Electronique','Vetements','Chaussures','Aliment','Livres','Autres'] as $cat)
                        <option value="{{ $cat }}" {{ ($produit->Categorie == $cat) ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mb-2">
            <label class="form-label">Image (laisser vide pour conserver)</label>
            <input type="file" name="Image" class="form-control">
            @if($produit->Image)
                <div class="mt-2"><img src="{{ asset('storage/'.$produit->Image) }}" alt="{{ $produit->Nom }}" style="max-width:160px;height:auto;border-radius:6px;border:1px solid #ddd"></div>
            @endif
        </div>
        <div class="d-flex gap-2 justify-content-end">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <button type="button" class="btn btn-outline-secondary" id="cancelEditBtn">Annuler</button>
            <button type="button" class="btn btn-danger" id="deleteProduitBtn">Supprimer</button>
        </div>
    </form>
</div>

<script>
(function(){
    const form = document.getElementById('editProduitForm');
    form?.addEventListener('submit', async function(e){
        e.preventDefault();
        const data = new FormData(form);
        try{
            const res = await fetch(form.action, { method: 'POST', body: data, credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const json = await res.json();
            if(res.ok && json.success){
                // simple feedback - reload list or close partial
                alert(json.message || 'Produit mis à jour');
                // try to refresh the products list via filterForm if present
                const filter = document.getElementById('filterForm');
                if(filter) filter.dispatchEvent(new Event('submit', {cancelable:true}));
            } else {
                alert(json.message || 'Erreur lors de la mise à jour');
            }
        }catch(err){
            console.error('Update error', err);
            // fallback to normal submit
            form.submit();
        }
    });
    document.getElementById('cancelEditBtn')?.addEventListener('click', function(){
        // if within SPA, close partial by reloading products list
        const filter = document.getElementById('filterForm');
        if(filter) filter.dispatchEvent(new Event('submit', {cancelable:true}));
        else window.history.back();
    });

    // Delete handler with confirmation and AJAX fallback
    document.getElementById('deleteProduitBtn')?.addEventListener('click', async function(){
        if(!confirm('Confirmez-vous la suppression de ce produit ? Cette action est irréversible.')) return;
        const url = '{{ url('/produits/'.$produit->idProduit.'/delete') }}';
        try{
            const res = await fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            });
            const json = await res.json();
            if(res.ok && json.success){
                alert(json.message || 'Produit supprimé');
                const filter = document.getElementById('filterForm');
                if(filter){
                    try{ filter.dispatchEvent(new Event('submit', {cancelable:true})); }catch(e){}
                }
                // Après suppression, revenir à la page de la liste des produits
                setTimeout(function(){ window.location.href = '{{ url('/vendeur/produits') }}'; }, 200);
            } else {
                alert(json.message || 'Erreur lors de la suppression');
            }
        }catch(err){
            console.error('Delete error', err);
            // fallback: submit a form to the delete endpoint
            const f = document.createElement('form');
            f.method = 'POST';
            f.action = url;
            const token = document.createElement('input'); token.type = 'hidden'; token.name = '_token'; token.value = document.querySelector('input[name="_token"]').value; f.appendChild(token);
            document.body.appendChild(f);
            f.submit();
        }
    });
})();
</script>
