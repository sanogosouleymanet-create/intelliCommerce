<h2>Liste des produits enregistrés sur la plateforme.</h2><br>

<!-- Formulaire de recherche, filtre et tri -->
        <form id="filterForm" method="GET" action="{{ url('produits') }}" class="filtres">
            <section class="orders-header">
                <div  class="produits-filtres">
                <!--Recherche par nom-->
                <input type="text" name="recherche" placeholder="Rechercher un produit" class="search-input" value="{{ request('recherche') }}">
                <!-- Periode d'ajout -->
                <select name="periode" class="filter-select">
                    <option value="">Toutes les périodes</option>
                    <option value="24h" {{ request('periode') == '24h' ? 'selected' : '' }}>Dernières 24 heures</option>
                    <option value="7j" {{ request('periode') == '7j' ? 'selected' : '' }}>Derniers 7 jours</option>
                    <option value="30j" {{ request('periode') == '30j' ? 'selected' : '' }}>Derniers 30 jours</option>
                </select>
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

<!--<div style="margin-bottom:18px;display:flex;gap:12px;align-items:center">
    <input type="text" id="searchProduit" placeholder="Rechercher un produit..." style="padding:8px 12px;border-radius:4px;border:1px solid #ccc;min-width:220px">
</div>-->

<section class="products-list">
    <table class="orders-table" style="width:100%;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <thead style="background:#f8f9fb">
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Prix</th>
                <th>Stock</th>
                <th>Vendeur</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="produitsBody">
            @foreach($produits as $produit)
                <tr data-id="{{ $produit->idProduit }}">
                    <td>{{ $produit->Nom }}</td>
                    <td>{{ $produit->Description }}</td>
                    <td>{{ $produit->Prix }}</td>
                    <td>{{ $produit->Stock }}</td>
                    <td>{{ $produit->vendeur->Nom ?? '—' }}</td>
                    <td>
                        <button class="btn secondary btn-view-produit">Voir</button>
                        <button class="btn btn-edit-produit">Modifier</button>
                        <button class="btn danger btn-delete-produit">Supprimer</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</section>

<style>
    h2{
        color: white;
    }
.orders-table th, .orders-table td {
    padding: 12px 10px;
    text-align: left;
    border-bottom: 1px solid #f0f0f0;
}
.orders-table tr:last-child td { 
    border-bottom: none; 
}
.orders-table tr:hover { background: #f1f5fa; }
.orders-table th { font-weight: bold; }
.btn { margin-right: 4px; }
.btn.danger { background: #c0392b; }
.btn.secondary { background: #2b7cff; }
.btn.btn-edit-produit { background: #27ae60; }

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

</style>

</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const tableBody = document.getElementById('produitsBody');
    if(filterForm && tableBody) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData).toString();
            fetch(window.location.pathname + '?' + params, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(resp => resp.text())
            .then(html => {
                // On attend que le contrôleur retourne juste le <tbody> si AJAX
                // ou on extrait le tbody du HTML reçu
                let temp = document.createElement('div');
                temp.innerHTML = html;
                let newTbody = temp.querySelector('tbody');
                if(newTbody) tableBody.innerHTML = newTbody.innerHTML;
            });
        });
    }

    // Suppression dynamique (confirmation)
    tableBody.addEventListener('click', function(e) {
        if(e.target.classList.contains('btn-delete-produit')) {
            const tr = e.target.closest('tr');
            const id = tr.getAttribute('data-id');
            if(confirm('Supprimer ce produit ?')) {
                fetch(`/admin/produits/${id}/delete`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(resp => {
                    if(resp.ok) tr.remove();
                    else alert('Erreur lors de la suppression');
                });
            }
        }
    });
});
</script>