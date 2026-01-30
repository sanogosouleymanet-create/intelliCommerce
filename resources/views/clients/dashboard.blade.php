@php
// Partial: dashboard client (overview)
@endphp

<div class="mb-3 d-flex justify-content-between align-items-center">
    <h2 class="m-0">Mon tableau de bord</h2>
    <div class="small text-muted">Dernières activités</div>
</div>

<div class="row">
    <div class="col-12 mb-3">
        <div class="card p-3">
            <h5>Dernières commandes</h5>
            <div class="orders-list">
                @if(isset($client) && $client->commandes && $client->commandes->count())
                    @foreach($client->commandes->sortByDesc('DateCommande')->take(5) as $commande)
                        <div class="order">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>Commande #{{ $commande->idCommande }}</strong>
                                    <div class="text-muted">Le {{ \Carbon\Carbon::parse($commande->DateCommande)->format('d/m/Y H:i') }}</div>
                                </div>
                                <div>
                                    <div class="text-end">Total: <strong>{{ number_format($commande->Montant,0,',',' ') }} FCFA</strong></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-muted">Aucune commande récente.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12 mb-3">
        <div class="card p-3">
            <h5>Messages</h5>
            @if(isset($client) && $client->message && $client->message->count())
                <ul>
                    @foreach($client->message->sortByDesc('DateEnvoi')->take(5) as $msg)
                        <li>{{ \Illuminate\Support\Str::limit($msg->Contenu, 120) }} <small class="text-muted">— {{ \Carbon\Carbon::parse($msg->DateEnvoi)->diffForHumans() }}</small></li>
                    @endforeach
                </ul>
            @else
                <div class="text-muted">Aucun message pour le moment.</div>
            @endif
        </div>
    </div>

    <div class="col-12">
        <h5>Produits recommandés</h5>
        @php
            $reco = App\Models\Produit::orderBy('DateAjout','desc')->take(8)->get();
        @endphp
        @if($reco->count())
            <div class="product-grid">
                @foreach($reco as $produit)
                    <div class="product-card card">
                        <div class="position-relative">
                            @php
                                $imgUrl = 'https://via.placeholder.com/400x300?text=No+Image';
                                $img = trim((string)($produit->Image ?? ''));
                                if($img !== ''){
                                    if(preg_match('/^https?:\/\//i', $img)){
                                        $imgUrl = $img;
                                    } elseif(\Illuminate\Support\Facades\Storage::exists('public/'.$img)){
                                        $imgUrl = asset('storage/'.$img);
                                    } elseif(file_exists(public_path($img))){
                                        $imgUrl = asset($img);
                                    } elseif(file_exists(public_path('images/'.basename($img)))){
                                        $imgUrl = asset('images/'.basename($img));
                                    }
                                }
                            @endphp
                            <img src="{{ $imgUrl }}" class="card-img-top" alt="{{ $produit->Nom }}">
                        </div>
                        <div class="card-body">
                            <h6 class="product-title">{{ $produit->Nom }}</h6>
                            <p class="product-meta mb-2">{{ \Illuminate\Support\Str::limit($produit->Description, 60) }}</p>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <div class="product-price">{{ number_format($produit->Prix, 0, ',', ' ') }} FCFA</div>
                                <a href="/produit/{{ $produit->idProduit }}" class="btn btn-sm btn-outline-secondary">Voir</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-muted">Aucun produit à recommander pour le moment.</div>
        @endif
    </div>
</div>