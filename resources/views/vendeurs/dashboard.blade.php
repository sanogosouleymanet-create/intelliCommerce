@php
// Dashboard partiel pour injection par défaut dans main
@endphp


<!-- HEADER : titre et affichage du nom du vendeur si disponible -->
<header class="header">
    <h1>Tableau de Bord Vendeur</h1>
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

<!-- Section statistiques rapides -->
<section class="stats">
    <div class="card">
        <h3><i class="fa-solid fa-box"></i> Produits</h3>
        {{-- Affiche le nombre de produits: utilise $produitsCount si fourni, sinon compte via la relation --}}
        <p class="number">{{ $produitsCount ?? $vendeur->produits()->count() }}</p>
        <span class="green"><i class="fa-solid fa-list"></i> Total produits</span>
    </div>

    <div class="card">
        <h3><i class="fa-solid fa-cart-shopping"></i> Commandes</h3>
        {{-- Nombre de commandes récentes, valeur par défaut 0 si non fournie --}}
        <p class="number">{{ $commandesCount ?? 0 }}</p>
        <span class="green"><i class="fa-solid fa-clock"></i> Récentes</span>
    </div>

    <div class="card">
        <h3><i class="fa-solid fa-envelope"></i> Messages non lus</h3>
        {{-- Affiche le nombre de messages non lus --}}
        <p class="number">{{ $messagesNonLus ?? 0 }}</p>
        <span class="green"><i class="fa-solid fa-comments"></i> À lire</span>
    </div>
</section>

<!-- Section commandes récentes affichée sous forme de tableau -->
<section class="orders">
    <h2>Commandes Récentes</h2>
    <table>
        <tr>
            <th>N° Commande</th>
            <th>Date</th>
            <th>Statut</th>
        </tr>
        {{-- Si des commandes récentes existent, les afficher dans une boucle --}}
        @if(isset($commandesRecentes) && $commandesRecentes->count())
            @foreach($commandesRecentes as $commande)
                <tr>
                    {{-- Identifiant de la commande personnalisé --}}
                    <td>#C-{{ $commande->idCommande }}</td>
                    {{-- Date de la commande telle qu'enregistrée --}}
                    <td>{{ $commande->DateCommande }}</td>
                    {{-- Statut ou montant comme fallback --}}
                    <td>{{ $commande->Statut ?? ($commande->MontantTotal ?? '') }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="3">Aucune commande récente.</td>
            </tr>
        @endif
    </table>
</section>

<section class="top-products">
    <h2>Top Produits</h2>

    {{-- Récupère les 3 premiers produits du vendeur pour affichage rapide --}}
    @php $top = $vendeur->produits->take(3); @endphp
    @if($top->count())
        @foreach($top as $produit)
            <div class="product">
                {{-- Nom du produit --}}
                <span><i class="fa-solid fa-box"></i> {{ $produit->Nom }}</span>
                {{-- Stock disponible, affiche un tiret si inconnu --}}
                <small>Stock: {{ $produit->Stock ?? '—' }}</small>
            </div>
        @endforeach
    @else
        <p>Aucun produit disponible.</p>
    @endif
</section>

