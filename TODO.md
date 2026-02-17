# TODO - Correction du bug d'ajout multiple au panier

## Problème identifié
Lorsqu'on clique sur le bouton "Ajouter au panier", le produit est ajouté plusieurs fois à cause de gestionnaires d'événements redondants:

1. **script.js** - Gestionnaire principal qui appelle `addToCartRequest`
2. **PagePrincipale.blade.php** - Dispatches un custom event `product-added-to-cart`
3. **script.js** - Écoute le custom event et appelle `addToCartRequest` une deuxième fois

## Tâches à effectuer

- [ ] 1. Supprimer le gestionnaire d'événements redondant dans PagePrincipale.blade.php
- [ ] 2. Vérifier qu'il n'y a pas d'autres gestionnaires d'événements similaires

## Solution
Supprimer le code qui dispatch le custom event dans PagePrincipale.blade.php car le gestionnaire direct dans script.js est suffisant.
