<div class="product-detail-fragment" style="background-color: white; padding: 20px; border-radius: 12px; max-width: 800px; margin: 0 auto; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
	<!-- Product Main -->
	<div style="display: flex; gap: 24px; align-items: flex-start; margin-bottom: 30px;">
		<div style="flex: 0 0 250px;">
			<img src="{{ $produit->Image ? asset('storage/' . $produit->Image) : asset('images/placeholder.png') }}" 
				 alt="{{ $produit->Nom }}" 
				 style="width: 100%; height: 250px; object-fit: cover; border-radius: 8px; border: 1px solid #eee;" />
		</div>
		<div style="flex: 1;">
			<span style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">
				{{ $produit->Categorie ?? 'Non catégorisé' }}
			</span>
			<h3 style="margin: 8px 0; font-size: 24px; color: #1a1a2e;">{{ $produit->Nom }}</h3>
			<p style="color: #666; line-height: 1.6;">{{ $produit->Description }}</p>
			<p style="font-size: 28px; font-weight: 800; color: #e94560; margin: 12px 0;">
				{{ number_format($produit->Prix, 0, ',', ' ') }} <span style="font-size: 14px; color: #666; font-weight: 400;">FCFA</span>
			</p>
			<p style="margin: 8px 0;">
				<strong>Stock: </strong>
				<span style="@if($produit->Stock > 10) background: #d4edda; color: #155724; @elseif($produit->Stock > 0) background: #fff3cd; color: #856404; @else background: #f8d7da; color: #721c24; @endif padding: 4px 10px; border-radius: 6px; font-weight: 600;">
					@if($produit->Stock > 10)
						En stock ({{ $produit->Stock }})
					@elseif($produit->Stock > 0)
						Stock faible ({{ $produit->Stock }})
					@else
						Rupture de stock
					@endif
				</span>
			</p>
			@if($vendeur)
				<p style="margin: 8px 0;">
					<strong>Boutique: </strong>{{ $vendeur->NomBoutique ?? trim(($vendeur->Nom ?? '') . ' ' . ($vendeur->Prenom ?? '')) ?: 'Boutique' }}
				</p>
			@endif
		</div>
	</div>
    
	<!-- Actions -->
	<div style="margin-top: 16px; text-align: right; display: flex; gap: 12px; justify-content: flex-end;">
		<button class="btn btn-sm btn-secondary js-back" style="padding: 10px 20px; border-radius: 8px;">Fermer</button>
		<button class="add-to-cart-fragment" 
				data-id="{{ $produit->idProduit }}" 
				style="padding: 10px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
			<i class="fas fa-shopping-cart"></i> Ajouter au panier
		</button>
	</div>
    
	<!-- Similar Products (AJAX Fragment) -->
	@if(isset($produitsSimilaires) && $produitsSimilaires->count() > 0)
	<div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
		<h4 style="margin: 0 0 16px; color: #1a1a2e; display: flex; align-items: center; gap: 8px;">
			<i class="fas fa-th-large" style="color: #667eea;"></i>
			Produits similaires
		</h4>
		<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
			@foreach($produitsSimilaires as $produitSimilaire)
			<a href="{{ route('produit.public', $produitSimilaire->idProduit) }}" 
			   style="display: block; background: #f8f9fa; border-radius: 8px; overflow: hidden; text-decoration: none; transition: transform 0.3s ease;"
			   onmouseover="this.style.transform='translateY(-3px)'"
			   onmouseout="this.style.transform='translateY(0)'">
				<img src="{{ $produitSimilaire->Image ? asset('storage/' . $produitSimilaire->Image) : asset('images/placeholder.png') }}" 
					 alt="{{ $produitSimilaire->Nom }}" 
					 style="width: 100%; height: 120px; object-fit: cover;">
				<div style="padding: 12px;">
					<p style="margin: 0 0 6px; font-size: 13px; font-weight: 600; color: #1a1a2e; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
						{{ $produitSimilaire->Nom }}
					</p>
					<p style="margin: 0; font-size: 16px; font-weight: 700; color: #e94560;">
						{{ number_format($produitSimilaire->Prix, 0, ',', ' ') }} <span style="font-size: 11px; color: #666; font-weight: 400;">FCFA</span>
					</p>
				</div>
			</a>
			@endforeach
		</div>
	</div>
	@endif
</div>
