<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
     <!-- Vue: produits/index.blade.php - Liste des produits -->
    <title>Produits</title>
    <!-- Charge la feuille de style spécifique aux produits -->
    <link rel="stylesheet" href="{{ asset('css/StyleProduit.css') }}">
</head>
<body>
    <h1>Liste des produits</h1>

    <!-- Bouton d'ouverture du modal d'ajout (onclick inline pour fonctionner lors de chargement AJAX) -->
    <button id="openAdd" class="Ajout" onclick="document.getElementById('addModal').style.display='flex';document.getElementById('addModal').setAttribute('aria-hidden','false');">+</button>

    <!-- Modal d'ajout de produit -->
    <div id="addModal" class="modal" aria-hidden="true" style="display:none;" onclick="if(event.target===this){this.style.display='none';this.setAttribute('aria-hidden','true');}">
        <div class="modal-content">
            <button type="button" class="close" id="closeAdd" onclick="document.getElementById('addModal').style.display='none';document.getElementById('addModal').setAttribute('aria-hidden','true');">×</button>
            <h2>Ajouter un Produit</h2>
            <form action="{{ url('AjouterProduit') }}" method="POST" enctype="multipart/form-data">
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
                <!-- Le stock est calculé automatiquement lors de la création -->
                <div>
                    <label for="Categorie">Catégorie du Produit:</label>
                    <select id="Categorie" name="Categorie" required>
                        <option value="" disabled selected>Sélectionner une catégorie</option>
                        <option value="Electronique">Électronique</option>
                        <option value="Vetements">Vêtements</option>
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

    @foreach($produits as $produit)
        <p>{{ $produit->Nom }} - {{ $produit->Prix }} FCFA</p>
    @endforeach    

</body>
</html>