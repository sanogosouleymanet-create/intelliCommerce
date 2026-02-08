@php
// Partial: détails d'une commande pour un client
@endphp

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Détails de la Commande #C-{{ $commande->idCommande }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Informations de la Commande</h5>
                            <p><strong>Date de Commande:</strong> {{ $commande->DateCommande }}</p>
                            <p><strong>Statut:</strong> {{ $commande->Statut ?? '—' }}</p>
                            <p><strong>Montant Total:</strong> {{ $commande->MontantTotal ?? '—' }} FCFA</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Informations du Client</h5>
                            <p><strong>Nom:</strong> {{ $commande->Client->Nom }} {{ $commande->Client->Prenom }}</p>
                            <p><strong>Email:</strong> {{ $commande->Client->email }}</p>
                            <p><strong>Téléphone:</strong> {{ $commande->Client->TelClient ?? '—' }}</p>
                            <p><strong>Adresse:</strong> {{ $commande->Client->Adresse ?? '—' }}</p>
                        </div>
                    </div>

                    <hr>

                    <h5>Produits Commandés</h5>
                    @if($commande->Produit && $commande->Produit->count())
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Produit</th>
                                        <th>Description</th>
                                        <th>Quantité</th>
                                        <th>Prix Unitaire</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($commande->Produit as $produit)
                                        <tr>
                                            <td><img src="{{ $produit->Image ? asset('storage/' . $produit->Image) : asset('images/placeholder.png') }}" alt="Image du produit" style="width:60px;height:60px;object-fit:cover;border-radius:4px;" /></td>
                                            <td>{{ $produit->Nom }}</td>
                                            <td>{{ $produit->Description ?? '—' }}</td>
                                            <td>{{ $produit->pivot->Quantite }}</td>
                                            <td>{{ $produit->pivot->PrixUnitaire }} FCFA</td>
                                            <td>{{ $produit->pivot->Quantite * $produit->pivot->PrixUnitaire }} FCFA</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>Aucun produit trouvé pour cette commande.</p>
                    @endif

                    <hr>

                    <div class="text-center">
                        <a href="/commandes" class="btn btn-secondary" data-client-nav style="color: white !important;">Retour aux Commandes</a>
                        <form action="{{ route('client.commande.destroy', $commande->idCommande) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger ml-2">Supprimer la Commande</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
