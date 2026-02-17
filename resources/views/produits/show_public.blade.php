<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>window.isClientAuthenticated = @json(auth()->guard('client')->check());</script>
    <script>window.isAuthenticated = @json(auth()->guard('client')->check() || auth()->guard('vendeur')->check() || auth()->guard('administrateur')->check());</script>
    <title>{{ $produit->Nom }} - Détails du produit</title>
    <link rel="stylesheet" href="{{ asset('css/StylePagePrincipale.css') }}">
    <link rel="stylesheet" href="{{ asset('css/StyleProduit.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Additional styles for improved product detail page */
        .product-detail-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .product-main {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 50px;
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .product-image-container {
            position: relative;
        }
        
        .product-main-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
        }
        
        .product-info {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .product-category {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            width: fit-content;
        }
        
        .product-title {
            font-size: 32px;
            font-weight: 700;
            color: #1a1a2e;
            margin: 10px 0;
            line-height: 1.2;
        }
        
        .product-description {
            color: #666;
            font-size: 15px;
            line-height: 1.7;
        }
        
        .product-price {
            font-size: 36px;
            font-weight: 800;
            color: #e94560;
            margin: 10px 0;
        }
        
        .product-price span {
            font-size: 18px;
            color: #666;
            font-weight: 400;
        }
        
        .product-meta {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            padding: 15px 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            font-size: 14px;
        }
        
        .meta-item i {
            color: #667eea;
        }
        
        .stock-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .stock-available {
            background: #d4edda;
            color: #155724;
        }
        
        .stock-low {
            background: #fff3cd;
            color: #856404;
        }
        
        .stock-out {
            background: #f8d7da;
            color: #721c24;
        }
        
        .vendor-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 12px;
            margin: 10px 0;
        }
        
        .vendor-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: 600;
        }
        
        .vendor-details h4 {
            margin: 0;
            font-size: 16px;
            color: #1a1a2e;
        }
        
        .vendor-details p {
            margin: 4px 0 0;
            font-size: 13px;
            color: #666;
        }
        
        .product-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: nowrap;
            align-items: center;
        }
        
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #f8f9fa;
            color: #666;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            background: #e9ecef;
            color: #333;
        }
        
        .btn-add-cart {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-add-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }
        
        .btn-add-cart:active {
            transform: translateY(0);
        }
        
        /* Similar Products Section */
        .similar-products-section {
            margin-top: 50px;
        }
        
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a2e;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .section-title i {
            color: #667eea;
        }
        
        .similar-products-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
        }
        
        .similar-product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
        }
        
        .similar-product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .similar-product-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        
        .similar-product-info {
            padding: 15px;
        }
        
        .similar-product-name {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a2e;
            margin: 0 0 8px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .similar-product-price {
            font-size: 18px;
            font-weight: 700;
            color: #e94560;
        }
        
        .similar-product-price span {
            font-size: 12px;
            color: #666;
            font-weight: 400;
        }
        
        /* Toast notification */
        .toast-notification {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 24px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            z-index: 10000;
            display: none;
            animation: slideIn 0.3s ease;
        }
        
        .toast-notification.show {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .product-main {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .similar-products-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 576px) {
            .product-title {
                font-size: 24px;
            }
            
            .product-price {
                font-size: 28px;
            }
            
            .similar-products-grid {
                grid-template-columns: 1fr;
            }
            
            .product-actions {
                flex-direction: column;
                gap: 10px;
            }
            .btn-add-cart, .btn-back {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="product-detail-container">
        <!-- Product Main Section -->
        <div class="product-main">
            <div class="product-image-container">
                <img src="{{ $produit->Image ? asset('storage/' . $produit->Image) : asset('images/placeholder.png') }}" 
                     alt="{{ $produit->Nom }}" 
                     class="product-main-image">
            </div>
            <div class="product-info">
                <span class="product-category">{{ $produit->Categorie ?? 'Non catégorisé' }}</span>
                <h1 class="product-title">{{ $produit->Nom }}</h1>
                <p class="product-description">{{ $produit->Description }}</p>
                <div class="product-price">
                    {{ number_format($produit->Prix, 0, ',', ' ') }} <span>FCFA</span>
                </div>
                <div class="product-meta">
                    <div class="meta-item">
                        <i class="fas fa-box"></i>
                        <span class="stock-badge @if($produit->Stock > 10) stock-available @elseif($produit->Stock > 0) stock-low @else stock-out @endif">
                            @if($produit->Stock > 10)
                                <i class="fas fa-check-circle"></i> En stock ({{ $produit->Stock }})
                            @elseif($produit->Stock > 0)
                                <i class="fas fa-exclamation-triangle"></i> Stock faible ({{ $produit->Stock }})
                            @else
                                <i class="fas fa-times-circle"></i> Rupture de stock
                            @endif
                        </span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Ajouté le {{ \Carbon\Carbon::parse($produit->DateAjout)->format('d/m/Y') }}</span>
                    </div>
                </div>
                @if($vendeur)
                <div class="vendor-info">
                    <div class="vendor-avatar">
                        {{ strtoupper(substr($vendeur->Nom ?? 'V', 0, 1)) }}
                    </div>
                    <div class="vendor-details">
                        <h4>{{ $vendeur->NomBoutique ?? trim(($vendeur->Nom ?? '') . ' ' . ($vendeur->Prenom ?? '')) ?: 'Boutique' }}</h4>
                        <p>Vendeur vérifié</p>
                    </div>
                </div>
                @endif
                <div class="product-actions">
                    <a href="/" class="btn-back">
                        <i class="fas fa-arrow-left"></i>
                        Retour
                    </a>
                    @if($produit->Stock > 0)
                    <button type="button" class="btn-back" data-id="{{ $produit->idProduit }}">
                        <i class="fas fa-shopping-cart"></i>
                        Ajouter au panier
                    </button>
                    @else
                    <button type="button" class="btn-add-cart" disabled>
                        <i class="fas fa-times-circle"></i>
                        Rupture de stock
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Produits similaires EN DESSOUS -->
        @if(isset($produitsSimilaires) && $produitsSimilaires->count() > 0)
        <div class="similar-products-section" style="margin-top: 48px;">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-th-large"></i>
                    Produits similaires
                </h2>
            </div>
            <div class="similar-products-grid">
                @foreach($produitsSimilaires as $produitSimilaire)
                <a href="{{ route('produit.public', $produitSimilaire->idProduit) }}" class="similar-product-card">
                    <img src="{{ $produitSimilaire->Image ? asset('storage/' . $produitSimilaire->Image) : asset('images/placeholder.png') }}" 
                         alt="{{ $produitSimilaire->Nom }}" 
                         class="similar-product-image">
                    <div class="similar-product-info">
                        <h3 class="similar-product-name">{{ $produitSimilaire->Nom }}</h3>
                        <div class="similar-product-price">
                            {{ number_format($produitSimilaire->Prix, 0, ',', ' ') }} <span>FCFA</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    
    <!-- Toast Notification -->
    <div class="toast-notification" id="toast">
        <i class="fas fa-check-circle"></i>
        <span id="toast-message">Produit ajouté au panier!</span>
    </div>
    
    <script src="{{ asset('js/script.js') }}"></script>
    <script>
    // Toast notification function (reste inchangé)
    function showToast(message) {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');
        toastMessage.textContent = message;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // Gestionnaire robuste pour AJAX ET injection dynamique
    function bindAddToCartBtn() {
        // On évite de dupliquer les handlers
        document.querySelectorAll('.btn-back[data-id]').forEach(function(btn) {
            if (btn._addToCartBound) return;
            btn._addToCartBound = true;
            btn.addEventListener('click', function(e) {
                // Si déjà géré par le script global (add-to-cart), on laisse faire
                if (btn.classList.contains('add-to-cart')) return;
                e.preventDefault();
                const produitId = btn.getAttribute('data-id');
                fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ id: produitId, qty: 1 })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Produit ajouté au panier !');
                    } else {
                        showToast(data.message || 'Erreur lors de l\'ajout au panier');
                    }
                })
                .catch(() => {
                    showToast('Erreur lors de l\'ajout au panier');
                });
            });
        });
    }

    // Appel initial et après chaque injection AJAX (mutation observer)
    bindAddToCartBtn();
    // Surveille les changements dans #mainContent (injection AJAX)
    if (window.MutationObserver && document.getElementById('mainContent')) {
        const observer = new MutationObserver(function() {
            bindAddToCartBtn();
        });
        observer.observe(document.getElementById('mainContent'), { childList: true, subtree: true });
    }
    </script>
</body>
</html>
