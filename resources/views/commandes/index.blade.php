<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Commande</title>
    <link rel="stylesheet" href="{{ asset('css/StyleCommande.css') }}">
</head>
<body>
    <header class="header">
            <h1>Liste des commandes</h1>
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

    @foreach($commandes as $commande)
        <p>
            {{$commande->DateCommande}} - {{$commande->Statut}}
        </p>
    @endforeach

</body>
</html>