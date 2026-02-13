@if(!request()->ajax())
    <link rel="stylesheet" href="{{ asset('css/StylePagePrincipale.css') }}">
@endif
@if(request()->is('admin/cart'))
    <style>
        /* Make the cart page look like the main page: use same body background
           and make the cart fragment transparent so the page background shows through */
        body { background-color: #82C8E5 !important; }
        .mini-cart-fragment { background: transparent !important; box-shadow: none !important; }
        .mini-cart-fragment .cart-header { background: transparent !important; }
    </style>
 @endif
<div class="cart-page-header">
    <h2 class="cart-title"> Mon panier</h2>
    <button id="checkout-top-btn" class="btn btn-success shiny-button">Passer la commande</button>
</div>
<div class="mini-cart-fragment container py-3">
    <style>
        /* Modern e-commerce cart styling */
        .mini-cart-fragment {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            padding: 24px;
            border-radius: 16px;
            max-width: 1200px;
            margin: 20px auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid rgba(0,0,0,0.05);
        }
        .cart-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-bottom: 24px;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 16px 20px;
            border-radius: 12px;
        }
        .cart-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .cart-title i {
            font-size: 1.8rem;
        }
        .table-responsive {
            overflow-x: auto;
            border-radius: 12px;
        }
        .table {
            margin-bottom: 0;
            border-radius: 12px;
            overflow: hidden;
            table-layout: fixed;
        }
        .table td, .table th {
            vertical-align: middle !important;
            padding: 16px 12px;
            border: none;
        }
        .table thead th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-weight: 700;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid #f1f3f4;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .cart-thumb {
            width: 120px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            display: block;
            transition: transform 0.3s ease;
        }
        .cart-thumb:hover {
            transform: scale(1.05);
        }
        .col-img { width: 140px; }
        .cart-prod-name {
            font-weight: 600;
            max-width: 400px;
            white-space: normal;
            color: #212529;
            font-size: 1.1rem;
        }
        .cart-prod-price, .cart-subtotal {
            text-align: right;
            white-space: nowrap;
            font-weight: 600;
            color: #1e88e5;
            font-size: 1.1rem;
        }
        .cart-qty-input {
            width: 80px;
            padding: 8px 12px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
            transition: border-color 0.3s ease;
        }
        .cart-qty-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }
        .col-select { width: 60px; text-align: center; }
        .col-action { width: 120px; text-align: center; }
        .shiny-button {
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .shiny-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }
        .btn-danger:hover {
            background: linear-gradient(135deg, #c82333 0%, #a02622 100%);
        }
        /* Floating checkout button */
        #cart-close-floating {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: #fff;
            border: none;
            box-shadow: 0 8px 20px rgba(40,167,69,0.3);
            cursor: pointer;
            z-index: 99999;
            padding: 14px 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        #cart-close-floating:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(40,167,69,0.4);
        }
        #cart-close-floating .label { font-weight: 700; }
        @media (max-width: 992px) {
            .mini-cart-fragment { padding: 20px; margin: 16px; }
            .cart-thumb { width: 100px; height: 80px; }
            .cart-qty-input { width: 70px; }
            .cart-header { padding: 12px 16px; }
            .cart-title { font-size: 1.3rem; }
        }
        @media (max-width: 768px) {
            .col-img { display: none; }
            .cart-thumb { display: none; }
            .cart-prod-name { max-width: 180px; font-size: 1rem; }
            .cart-prod-price, .cart-subtotal { text-align: left; font-size: 1rem; }
            #cart-close-floating { right: 16px; left: 16px; bottom: 16px; justify-content: center; }
            .mini-cart-fragment { padding: 16px; margin: 12px; }
            .table td, .table th { padding: 12px 8px; }
        }
        /* Hide legacy centered summary and add footer styling below table */
        .cart-total-summary{ display:none !important; }
        .cart-footer{ display:flex; justify-content:flex-end; align-items:center; gap:12px; margin-top: 20px; }
        .cart-footer #cart-total{ font-size:1.1rem; font-weight:800; color: #1e88e5; background: rgba(255,255,255,0.0); padding:6px 8px; border-radius:6px }
        /* Cart page header styling */
        .cart-page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; max-width: 1200px; margin-left: auto; margin-right: auto; }

        .cart-total-bar {
    position: fixed;
    bottom: 0;
    left: 20%;
    width: 100%;

    background: transparent;
    padding: 15px 20px;

    /* Effet visuel "glisse en dessous" */
    border-top: 1px solid #ddd;
    box-shadow: 0 -6px 12px rgba(0, 0, 0, 0.08);

    z-index: 1000;
}

/* Important : laisser de la place au contenu */
body {
    padding-bottom: 80px;
}
    </style>
    
       
    
    @if(empty($items))
        <div class="alert alert-info">Votre panier est vide.</div>
    @else
        
        <div class="table-responsive">
        <table class="table table-sm table-hover align-middle">
            <thead>
                <tr>
                    <th class="col-select"><input type="checkbox" id="select-all" title="Tout sélectionner"></th>
                    <th class="col-img"></th>
                    
                    <th class="col-prod">Produit</th>
                    <th class="col-price" style="text-align:center;">Prix</th>
                    <th class="col-qty" style="text-align:center;">Quantité</th>
                    <th class="col-subtotal" style="text-align:center;">Sous-total</th>
                    <th class="col-action">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($items as $it)
                @php
                    $p = $it['produit'];
                    $imgUrl = 'https://via.placeholder.com/80x60?text=No';
                    $img = trim((string)($p->Image ?? ''));
                    if($img !== ''){
                        if(preg_match('/^https?:\/\//i', $img)){
                            $imgUrl = $img;
                        } elseif(\Illuminate\Support\Facades\Storage::exists('public/'. $img)){
                            $imgUrl = asset('storage/'. $img);
                        } elseif(file_exists(public_path($img))){
                            $imgUrl = asset($img);
                        } elseif(file_exists(public_path('images/'.basename($img)))){
                            $imgUrl = asset('images/'.basename($img));
                        }
                    }
                @endphp
                <tr data-id="{{ $p->idProduit }}">
                    <td class="col-select">
                        <input type="checkbox" class="select-product" name="selected_products[]" value="{{ $p->idProduit }}" data-subtotal="{{ $it['subtotal'] }}">
                    </td>
                    <td><img src="{{ $imgUrl }}" alt="{{ $p->Nom }}" class="cart-thumb"></td>
                    <td class="cart-prod-name">{{ $p->Nom }}</td>
                    <td class="cart-prod-price">{{ number_format($p->Prix,0,',',' ') }} FCFA</td>
                    <td>
                        <form class="cart-update-form" method="POST" action="{{ route('admin.cart.update') }}" data-id="{{ $p->idProduit }}">
                            @csrf
                            <input type="hidden" name="id" value="{{ $p->idProduit }}">
                            <div class="cart-qty-row">
                                <input type="number" name="qty" value="{{ $it['qty'] }}" min="0" class="cart-qty-input form-control form-control-sm">
                            </div>
                        </form>
                    </td>
                    <td class="cart-subtotal">{{ number_format($it['subtotal'],0,',',' ') }} FCFA</td>
                    <td>
                        <form class="cart-remove-form" method="POST" action="{{ route('admin.cart.remove') }}" data-id="{{ $p->idProduit }}">
                            @csrf
                            <input type="hidden" name="id" value="{{ $p->idProduit }}">
                            <button class="btn btn-sm btn-danger shiny-button" type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div style="display: flex; margin-top: 10px; align-items: center;">
            
            <div class="cart-total-bar">
    <h3><strong>Total :</strong>
    <strong id="cart-total">0 FCFA</strong></h3>
</div>
           
        </div>

        </div>
    @endif
</div>
<!-- Hidden form used to send selected products to checkout -->
<form id="multi-checkout-form" method="POST" action="{{ route('admin.cart.place-order') }}" style="display:none">
    @csrf
</form>
@if(!request()->ajax())
<script>
try{ if(window && window.adminInitPartials) window.adminInitPartials(); }catch(e){ /* no-op */ }
</script>
@endif
