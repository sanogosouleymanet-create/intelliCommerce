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
                                    @php
                                        $total = $commande->Produit->sum(function($produit) {
                                            $prix = $produit->pivot->PrixUnitaire ?? $produit->Prix ?? 0;
                                            $qty = $produit->pivot->Quantite ?? 0;
                                            return (float)$prix * (float)$qty;
                                        });
                                    @endphp
                                    <div class="text-end">Total: <strong>{{ number_format($total ?? 0, 0, ',', ' ') }} FCFA</strong></div>
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
            @php
                $allMessages = $client->message()->orderBy('DateEnvoi', 'desc')->get();
                $recentMessages = $allMessages->take(3);
                $unreadInRecent = $recentMessages->where('Statut', 'non lu')->count();
                $totalUnread = $allMessages->where('Statut', 'non lu')->count();
                $hasMoreUnread = $totalUnread > $unreadInRecent;
            @endphp
            <h5>Messages @if($hasMoreUnread) <i class="fas fa-envelope text-warning"></i> @endif</h5>
            @if($recentMessages->count())
                <ul>
                    @foreach($recentMessages as $msg)
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
                    <div class="product-card card @if($produit->Promotion && isset($produit->Reduction) && $produit->Reduction > 0) product-card-promo @endif">
                        <div class="position-relative">
                            @if($produit->Promotion && isset($produit->Reduction) && $produit->Reduction > 0)
                                <span class="badge-promo position-absolute" style="top:8px;left:8px;z-index:2;display:inline-flex;border-radius:4px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.2);">
                                    <span style="background:#ffc107;color:#000;padding:4px 8px;font-size:0.75rem;font-weight:600;">En promotion</span>
                                    <span style="background:#e65100;color:#fff;padding:4px 6px;font-size:0.75rem;font-weight:700;">-{{ $produit->Reduction }}%</span>
                                </span>
                            @endif
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
                                @if($produit->Promotion && isset($produit->Reduction) && $produit->Reduction > 0 && isset($produit->PrixOriginal) && $produit->PrixOriginal > $produit->Prix)
                                    <div class="product-price" style="display:flex;flex-direction:column;align-items:flex-start;">
                                        <span style="color:#e53935;text-decoration:line-through;font-size:0.9em;">{{ number_format($produit->PrixOriginal, 0, ',', ' ') }} FCFA</span>
                                        <span style="color:#1e88e5;font-weight:700;font-size:1.1em;">{{ number_format($produit->Prix, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                @else
                                    <div class="product-price">{{ number_format($produit->Prix ?? 0, 0, ',', ' ') }} FCFA</div>
                                @endif
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