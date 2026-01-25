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
    <header class="header">
            <h1>Liste des produits</h1>
            <div class="account">
                <i class="fa-solid fa-user"></i>
                {{-- Affiche le prénom et le nom du vendeur si présents, sinon affiche "Mon Compte" --}}
                @if(isset($vendeur->Prenom) || isset($vendeur->Nom))
                    {{ trim(($vendeur->Prenom ?? '') . ' ' . ($vendeur->Nom ?? '')) }}
                @else
                    Mon Compte
                @endif
            </div>
        </header>
    

    <!-- Bouton d'ouverture du modal d'ajout (onclick inline pour fonctionner lors de chargement AJAX) -->
    <button id="openAdd" class="Ajout" onclick="document.getElementById('addModal').style.display='flex';document.getElementById('addModal').setAttribute('aria-hidden','false');">+</button>

    <!-- Modal d'ajout de produit -->
    <div id="addModal" class="modal" aria-hidden="true" style="display:none;" onclick="if(event.target===this){this.style.display='none';this.setAttribute('aria-hidden','true');}">
        <div class="modal-content">
            <button type="button" class="close" id="closeAdd" onclick="document.getElementById('addModal').style.display='none';document.getElementById('addModal').setAttribute('aria-hidden','true');">×</button>
            <h2>Ajouter un Produit</h2>
            <form id="formProduit" enctype="multipart/form-data">
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
        <form method="GET" action="{{ url('produits') }}" class="filtres">
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
        </form>

    <!-- Affichage de la liste des produits -->
        @foreach($produits as $produit)
        <div class="produit">
                <img src="{{ $produit->Image ? asset('storage/' . $produit->Image) : asset('images/placeholder.png') }}" alt="Image du produit" class="produit-image">
                <br/>
                {{-- Nom du produit --}}
                    {{ $produit->Nom }}
                <br/>
                {{-- Prix du produit --}}
                <strong>Prix: {{ number_format($produit->Prix, 0, ',', ' ') }} FCFA</strong>
                <br/>   
                {{-- Stock disponible, affiche un tiret si inconnu --}}
                <small>Stock: {{ $produit->Stock ?? '—' }}</small>
        </div>
        @endforeach 

        



    </body>
</html>