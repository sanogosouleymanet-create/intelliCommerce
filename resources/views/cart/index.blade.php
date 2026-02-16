@if(!request()->ajax())
    <link rel="stylesheet" href="{{ asset('css/StylePagePrincipale.css') }}">
@endif
@if(request()->is('cart'))
    <style>
        /* Make the cart page look like the main page: use same body background
           and make the cart fragment transparent so the page background shows through */
        body { background-color: #82C8E5 !important; }
        .mini-cart-fragment { background: transparent !important; box-shadow: none !important; }
        .mini-cart-fragment .cart-header { background: transparent !important; }
    </style>
@endif
<div class="mini-cart-fragment container py-3">
    <div class="cart-header">
        <h3 class="cart-title"><i class="ri-shopping-cart-line"></i> Mon panier</h3>
    </div>
    @if(empty($items))
        <div class="alert alert-info">Votre panier est vide.</div>
    @else
        <style>
            /* Ensure select column has fixed width so header checkbox doesn't overlap rows */
            .col-select{ width:48px; }
            .select-product{ margin-left:6px; }

            /* Table-specific styles only: rows, columns, cells and small responsive tweaks */
            /* Compact table: reduce max width, paddings and image sizes to avoid scrollbars */
            /* Make the cart fragment span the page and align to the left (remove left gutter) */
            .mini-cart-fragment.container { max-width: none !important; width: calc(100% - 16px) !important; box-sizing: border-box; padding-left: 8px !important; padding-right: 8px !important; margin-left: 0 !important; margin-right: 0 !important; position: relative; }
                /* Make table scrollable within the cart fragment and keep the header visible */
            .table-responsive {
                overflow-x: hidden;
                overflow-y: auto;
                max-width: 100%;
                -webkit-overflow-scrolling: touch;
                /* leave room for header: adjust if needed */
                max-height: calc(100vh - 180px);
            }
                /* Shrink table slightly from the right to create inner gutter
                    without affecting button/total field. Keeps full width on small screens. */
                /* Full-width table: use all available space inside the fragment */
                .table { margin-bottom: 0; table-layout: auto; width: 100% !important; max-width: 100% !important; }
            .table td, .table th {
                vertical-align: middle !important;
                padding: 6px 6px;
                border-bottom: 1px solid #f1f3f4;
                white-space: normal;
                word-break: break-word;
                line-height: 1.2;
            }
            .table thead th {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                font-weight: 700;
                color: #495057;
                border-bottom: 2px solid #dee2e6;
                font-size: 0.9rem;
                text-transform: uppercase;
                letter-spacing: 0.4px;
                padding: 8px 6px;
                position: sticky;
                top: 0;
                z-index: 6;
            }
            .table tbody tr { transition: background 0.18s ease, transform 0.15s ease; }
            .table tbody tr:hover { background-color: #f8f9fa; transform: translateY(-1px); box-shadow: 0 3px 6px rgba(0,0,0,0.04); }
                /* Product thumbnail sizing — match the provided sample: narrow portrait thumbnails.
                    Use fixed width, auto height, and contain to show full image without stretching. */
                .cart-thumb { width: 96px; height: auto; max-height: 140px; object-fit: contain; border-radius: 8px; display: block; }
                .col-img { width: 110px; }
            /* Ensure mini-cart modal uses same large thumbnails (override global/minified CSS if present) */
            .mini-cart-fragment .cart-thumb { width: 96px !important; height: auto !important; max-height: 140px !important; object-fit: contain !important; }
            .mini-cart-fragment .col-img { width: 110px !important; }
            .cart-prod-name { font-weight: 600; max-width: 260px; color: #212529; font-size: 1rem; }
            .cart-prod-price, .cart-subtotal { text-align: right; white-space: nowrap; font-weight: 600; color: #1e88e5; font-size: 0.98rem; }
                /* Column sizing and alignment for product/price/qty/subtotal */
                .col-prod { text-align: left; padding-left: 8px; }
                .col-price { width: 140px; text-align: center; }
                .col-qty { width: 110px; text-align: center; }
                .col-subtotal { width: 140px; text-align: center; }
            .cart-qty-input { width: 56px; padding: 4px 6px; border: 1px solid #dee2e6; border-radius: 6px; text-align: center; }
            .cart-qty-input:focus { border-color: #007bff; box-shadow: 0 0 0 0.08rem rgba(0,123,255,0.08); }
            .col-action { width: 72px; text-align: center; }
                /* Strong, broad override: force horizontal layout for all elements inside the cart fragment.
                   This undoes any global rotations, writing-mode changes, pseudo-element transforms,
                   and prevents letter-stacking. Scoped strictly to the cart fragment so global styles stay intact. */
                .mini-cart-fragment,
                .mini-cart-fragment *,
                .mini-cart-fragment *::before,
                .mini-cart-fragment *::after,
                .mini-cart-fragment th,
                .mini-cart-fragment td {
                    -webkit-transform: none !important;
                            transform: none !important;
                    -webkit-writing-mode: horizontal-tb !important;
                            writing-mode: horizontal-tb !important;
                    -webkit-text-orientation: mixed !important;
                            text-orientation: mixed !important;
                    white-space: normal !important;
                    word-break: normal !important;
                    text-align: left !important;
                    line-height: 1.2 !important;
                }

                /* Keep action buttons on one line and center them */
                .mini-cart-fragment .btn,
                .mini-cart-fragment .shiny-button,
                .mini-cart-fragment .group-close {
                    white-space: nowrap !important;
                    display: inline-block !important;
                    text-align: center !important;
                }

                /* Ensure headers and product names are horizontal and not letter-stacked */
                .mini-cart-fragment .table thead th,
                .mini-cart-fragment .cart-prod-name,
                .mini-cart-fragment .cart-title {
                    writing-mode: horizontal-tb !important;
                    text-orientation: mixed !important;
                    white-space: nowrap !important;
                    overflow: visible !important;
                }
                
                /* Floating total removed; total will be displayed inline at the bottom */
            @media (max-width: 768px) {
                .col-img, .cart-thumb { display: none; }
                .cart-prod-name { max-width: 160px; font-size: 0.98rem; }
                .cart-prod-price, .cart-subtotal { text-align: left; }
                .table td, .table th { padding: 8px 6px; }
                .table { width: 100% !important; max-width: 100% !important; }
            }
        </style>
        <div class="table-responsive">
        <table class="table table-sm table-hover align-middle">
            <thead>
                <tr>
                    <th class="col-select"><input type="checkbox" id="select-all" title="Tout sélectionner"></th>
                    <th class="col-img"></th>
                    <th class="col-prod">Produit</th>
                    <th class="col-price">Prix</th>
                    <th class="col-qty">Quantité</th>
                    <th class="col-subtotal">Sous-total</th>
                    <th class="col-action"></th>
                </tr>
            </thead>
            <tbody>
            @foreach($items as $it)
                @php
                    $p = $it['produit'];
                    // Use a thumbnail-size placeholder by default
                    $imgUrl = 'https://via.placeholder.com/140x100?text=No';
                    $img = trim((string)($p->Image ?? ''));
                    if ($img !== '') {
                        // Absolute URL stored in DB
                        if (preg_match('/^https?:\/\//i', $img)) {
                            $imgUrl = $img;
                        } else {
                            try {
                                // Prefer the public disk (storage/app/public)
                                if (\Illuminate\Support\Facades\Storage::disk('public')->exists(ltrim($img, '/'))) {
                                    $imgUrl = asset('storage/' . ltrim($img, '/'));
                                }
                                // direct public path (e.g., 'images/foo.jpg' or '/images/foo.jpg')
                                elseif (file_exists(public_path(ltrim($img, '/')))) {
                                    $imgUrl = asset(ltrim($img, '/'));
                                }
                                // file published to public/storage
                                elseif (file_exists(public_path('storage/' . ltrim($img, '/')))) {
                                    $imgUrl = asset('storage/' . ltrim($img, '/'));
                                }
                                // common images folder fallback
                                elseif (file_exists(public_path('images/' . basename($img)))) {
                                    $imgUrl = asset('images/' . basename($img));
                                }
                                // other common upload folders
                                elseif (file_exists(public_path('uploads/' . ltrim($img, '/')))) {
                                    $imgUrl = asset('uploads/' . ltrim($img, '/'));
                                }
                                // last-resort: try storage/images
                                elseif (file_exists(public_path('storage/images/' . basename($img)))) {
                                    $imgUrl = asset('storage/images/' . basename($img));
                                }
                            } catch (\Throwable $e) {
                                // keep placeholder on any error
                                $imgUrl = 'https://via.placeholder.com/140x100?text=No';
                            }
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
                        <form class="cart-update-form" method="POST" action="{{ route('cart.update') }}" data-id="{{ $p->idProduit }}">
                            @csrf
                            <input type="hidden" name="id" value="{{ $p->idProduit }}">
                            <div class="cart-qty-row">
                                <input type="number" name="qty" value="{{ $it['qty'] }}" min="0" class="cart-qty-input form-control form-control-sm">
                            </div>
                        </form>
                    </td>
                    <td class="cart-subtotal">{{ number_format($it['subtotal'],0,',',' ') }} FCFA</td>
                    <td>
                        <form class="cart-remove-form" method="POST" action="{{ route('cart.remove') }}" data-id="{{ $p->idProduit }}">
                            @csrf
                            <input type="hidden" name="id" value="{{ $p->idProduit }}">
                            <button class="btn btn-sm btn-danger shiny-button" type="submit">Retirer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>

        <div class="text-end mt-2">
            <strong id="cart-total">Total: 0 FCFA</strong>
        </div>
        
    @endif
</div>
<!-- Hidden form used to send selected products to checkout -->
<form id="multi-checkout-form" method="GET" action="/commande" style="display:none"></form>
<script>
// Ensure cart forms submit via AJAX when this fragment is loaded standalone
(function(){
    function initCartForms(){
        document.querySelectorAll('.cart-update-form, .cart-remove-form').forEach(function(form){
            // avoid duplicate binding
            if(form.__ajax_bound) return; form.__ajax_bound = true;
            form.addEventListener('submit', function(e){
                e.preventDefault();
                var url = form.getAttribute('action') || window.location.href;
                var fd = new FormData(form);
                // include X-Requested-With and send credentials so session is used
                var headers = { 'X-Requested-With': 'XMLHttpRequest' };
                var opts = { method: 'POST', headers: headers, body: fd, credentials: 'same-origin' };
                // show loader while request is in-flight
                if(window.showCartLoader) window.showCartLoader();
                fetch(url, opts).then(function(r){ return r.json(); }).then(function(json){
                    if(!json || !json.success){
                        alert(json && json.message ? json.message : 'Erreur lors de la mise à jour du panier');
                        if(window.hideCartLoader) window.hideCartLoader();
                        return;
                    }
                    // update header counters if helper exists, otherwise update DOM directly
                    if(window.updateHeaderCart) updateHeaderCart(json.count || 0, json.total || 0);
                    else {
                        document.querySelectorAll('.iscart .item-number').forEach(function(el){ el.textContent = (json.count || 0); });
                        var ct = document.getElementById('cart-total'); if(ct) ct.textContent = (json.total ? 'Total: ' + Number(json.total).toLocaleString('fr-FR') + ' FCFA' : 'Total: 0 FCFA');
                    }

                    // If currently shown inside the mini-cart modal, refresh its content without navigating
                    var overlay = document.getElementById('mini-cart-overlay');
                    var modalOpen = overlay && overlay.style.display && overlay.style.display !== 'none';
                    if(modalOpen){
                        if(window.refreshMiniCart) { refreshMiniCart(); }
                        else { location.reload(); }
                        return;
                    }

                    // Otherwise refresh the cart fragment via AJAX (avoid full page reload)
                    // Update header counters first
                    if(window.updateHeaderCart) updateHeaderCart(json.count || 0, json.total || 0);
                    else {
                        document.querySelectorAll('.iscart .item-number').forEach(function(el){ el.textContent = (json.count || 0); });
                    }
                    fetch('/cart', { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                        .then(function(r){ return r.text(); })
                        .then(function(html){
                            try{
                                var tmp = document.createElement('div');
                                tmp.innerHTML = html;
                                var frag = tmp.querySelector('.mini-cart-fragment');
                                if(frag){
                                    var current = document.querySelector('.mini-cart-fragment');
                                    if(current){ current.innerHTML = frag.innerHTML; }
                                    try{ if(typeof window.restoreCartSelection === 'function') window.restoreCartSelection(); }catch(e){}
                                    try{ if(typeof updateCartTotal === 'function') updateCartTotal(); }catch(e){}
                                }
                            }catch(e){ console.error('Failed to refresh cart fragment', e); }
                        }).catch(function(err){ console.error('Refresh cart fragment failed', err); });
                }).catch(function(err){ console.error(err); alert('Erreur réseau lors de la requête panier'); }).finally(function(){ if(window.hideCartLoader) window.hideCartLoader(); });
            });
        });
    }
    // init immediately and also when DOM changes (for AJAX-inserted content)
    initCartForms();
    var mo = new MutationObserver(function(){ initCartForms(); });
    mo.observe(document.documentElement || document.body, { childList: true, subtree: true });
    })();
</script>

<script>
// Inline checkout button behavior (same as floating checkout)
document.addEventListener('click', function(e){
    var btn = e.target.closest && e.target.closest('#checkout-inline-btn');
    if(!btn) return;
    e.preventDefault();
    try{
        var checked = Array.from(document.querySelectorAll('.select-product:checked')).map(function(i){ return i.value; });
        if(!checked.length){ alert('Sélectionnez au moins un produit à commander'); return; }
        var tokenEl = document.querySelector('meta[name="csrf-token"]');
        var token = tokenEl ? tokenEl.getAttribute('content') : null;
        var fd = new FormData();
        checked.forEach(function(id){ fd.append('selected_products[]', id); });
        if(token) fd.append('_token', token);
        var headers = { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' };
        if(token) headers['X-CSRF-TOKEN'] = token;
        try{ var xsrf = document.cookie.replace(/(?:(?:^|.*;\s*)XSRF-TOKEN\s*\=\s*([^;]*).*$)|^.*$/, "$1"); if(xsrf) headers['X-XSRF-TOKEN'] = decodeURIComponent(xsrf); }catch(e){}
        fetch('/passer-commande', { method: 'POST', headers: headers, body: fd, credentials: 'same-origin' })
            .then(function(r){ var ct = r.headers.get('content-type') || ''; if (ct.indexOf('application/json') === -1) { return r.text().then(function(text){ throw new Error('Réponse inattendue du serveur: ' + text); }); } return r.json().then(function(json){ if(!r.ok) throw new Error(json.message || 'Erreur serveur'); return json; }); })
            .then(function(json){ if(!json || !json.success){ alert(json && json.message ? json.message : 'Erreur lors de la commande'); return; } var toast = document.createElement('div'); toast.className = 'order-toast alert alert-success'; toast.style.position = 'fixed'; toast.style.top = '20px'; toast.style.left = '50%'; toast.style.transform = 'translateX(-50%)'; toast.style.zIndex = 99999; toast.style.minWidth = '240px'; toast.style.textAlign = 'center'; toast.textContent = json.message || 'Commande passée'; document.body.appendChild(toast); setTimeout(function(){ toast.remove(); }, 3500);
                // refresh fragment
                fetch('/cart', { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                    .then(function(r){ return r.text(); })
                    .then(function(html){ try{ var tmp=document.createElement('div'); tmp.innerHTML=html; var frag=tmp.querySelector('.mini-cart-fragment'); if(frag){ var cur=document.querySelector('.mini-cart-fragment'); if(cur) cur.innerHTML=frag.innerHTML; try{ if(typeof window.restoreCartSelection === 'function') window.restoreCartSelection(); }catch(e){} try{ if(typeof updateCartTotal === 'function') updateCartTotal(); }catch(e){} } }catch(e){ console.error(e); } });
            })
            .catch(function(err){ console.error('checkout error', err); alert(err.message || 'Erreur lors de la commande'); });
    }catch(err){ console.error(err); alert('Erreur interne: ' + (err.message || err)); }
});
</script>
<script>
// Add a floating close button when viewing the full cart page (/cart)
    (function(){
    try{
        var path = window.location.pathname || '/';
        if(path.indexOf('/cart') === 0){
            // create a fixed 'Passer la commande' button instead of a close button
            var checkoutBtn = document.createElement('button');
            checkoutBtn.id = 'cart-close-floating';
            checkoutBtn.type = 'button';
            checkoutBtn.className = 'group-close shiny-button';
            checkoutBtn.innerHTML = '<div class="label">Passer la commande</div><div class="icon-wrap" aria-hidden="true"><svg viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M8.14645 3.14645C8.34171 2.95118 8.65829 2.95118 8.85355 3.14645L12.8536 7.14645C13.0488 7.34171 13.0488 7.65829 12.8536 7.85355L8.85355 11.8536C8.65829 12.0488 8.34171 12.0488 8.14645 11.8536C7.95118 11.6583 7.95118 11.3417 8.14645 11.1464L11.2929 8H2.5C2.22386 8 2 7.77614 2 7.5C2 7.22386 2.22386 7 2.5 7H11.2929L8.14645 3.85355C7.95118 3.65829 7.95118 3.34171 8.14645 3.14645Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path></svg></div>';
            document.body.appendChild(checkoutBtn);
            checkoutBtn.addEventListener('click', function(e){
                e.preventDefault();
                try{
                    var checked = Array.from(document.querySelectorAll('.select-product:checked')).map(function(i){ return i.value; });
                    console.log('checkout clicked, selected:', checked);
                    if(!checked.length){ alert('Sélectionnez au moins un produit à commander'); return; }
                    // send AJAX POST to /passer-commande
                    var tokenEl = document.querySelector('meta[name="csrf-token"]');
                    var token = tokenEl ? tokenEl.getAttribute('content') : null;
                    var fd = new FormData();
                    checked.forEach(function(id){ fd.append('selected_products[]', id); });
                    if(token) fd.append('_token', token);
                    var headers = { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' };
                    if(token) headers['X-CSRF-TOKEN'] = token;
                    // also include X-XSRF-TOKEN header from cookie (Laravel expects this for SPA requests)
                    try{
                        var xsrf = document.cookie.replace(/(?:(?:^|.*;\s*)XSRF-TOKEN\s*\=\s*([^;]*).*$)|^.*$/, "$1");
                        if(xsrf) headers['X-XSRF-TOKEN'] = decodeURIComponent(xsrf);
                    }catch(e){ /* ignore */ }
                    console.log('fetch /passer-commande headers:', headers);
                    fetch('/passer-commande', { method: 'POST', headers: headers, body: fd, credentials: 'same-origin' })
                        .then(function(r){ console.log('response status', r.status); var ct = r.headers.get('content-type') || ''; if (ct.indexOf('application/json') === -1) { return r.text().then(function(text){ throw new Error('Réponse inattendue du serveur: ' + text); }); } return r.json().then(function(json){ if(!r.ok) throw new Error(json.message || 'Erreur serveur'); return json; }); })
                        .then(function(json){ console.log('response json', json); if(!json || !json.success){ alert(json && json.message ? json.message : 'Erreur lors de la commande'); return; } var toast = document.createElement('div'); toast.className = 'order-toast alert alert-success'; toast.style.position = 'fixed'; toast.style.top = '20px'; toast.style.left = '50%'; toast.style.transform = 'translateX(-50%)'; toast.style.zIndex = 99999; toast.style.minWidth = '240px'; toast.style.textAlign = 'center'; toast.textContent = json.message || 'Commande passée'; document.body.appendChild(toast); setTimeout(function(){ toast.remove(); }, 3500); // update mini-cart fragment
                            fetch('/cart', { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                                .then(function(r){ return r.text(); })
                                .then(function(html){
                                    try{
                                        var tmp = document.createElement('div');
                                        tmp.innerHTML = html;
                                        var frag = tmp.querySelector('.mini-cart-fragment');
                                        if(frag){
                                            var cur = document.querySelector('.mini-cart-fragment');
                                            if(cur){ cur.innerHTML = frag.innerHTML; }
                                            try{ if(typeof window.restoreCartSelection === 'function') window.restoreCartSelection(); }catch(e){}
                                            try{ if(typeof updateCartTotal === 'function') updateCartTotal(); }catch(e){}
                                        }
                                    }catch(e){ console.error(e); }
                                });
                        })
                        .catch(function(err){ console.error('fetch error', err); alert(err.message || 'Erreur lors de la commande'); });
                }catch(err){ console.error('checkout handler error', err); alert('Erreur interne: ' + (err.message || err)); }
            });
        }
    }catch(err){ console.error('Floating checkout button init failed', err); }
})();
</script>
<script>
// Select-all checkbox behavior and keep selection after AJAX refreshes
(function(){
    // helper to get CSRF token: meta tag preferred, fallback to XSRF-TOKEN cookie
    function getCsrfToken(){
        var m = document.querySelector('meta[name="csrf-token"]');
        if(m) return m.getAttribute('content');
        var match = document.cookie.replace(/(?:(?:^|.*;\s*)XSRF-TOKEN\s*\=\s*([^;]*).*$)|^.*$/, "$1");
        try{ return match ? decodeURIComponent(match) : null; }catch(e){ return null; }
    }

    function initSelectAll(){
        var selectAll = document.getElementById('select-all');
        if(!selectAll) return;
        selectAll.addEventListener('change', function(){
            var list = document.querySelectorAll('.select-product');
            list.forEach(function(cb){ cb.checked = selectAll.checked; });
            updateCartTotal(); // update total when select-all changes
            // persist selection
            try{ if(typeof window.saveCartSelection === 'function') window.saveCartSelection(); }catch(e){}
        });
        // clicking individual checkboxes should update the select-all state
        document.addEventListener('change', function(e){
            if(!e.target || !e.target.classList) return;
            if(e.target.classList.contains('select-product')){
                var all = Array.from(document.querySelectorAll('.select-product'));
                if(all.length === 0) return;
                var allChecked = all.every(function(cb){ return cb.checked; });
                selectAll.checked = allChecked;
                updateCartTotal(); // update total when individual checkbox changes
                // persist selection
                try{ if(typeof window.saveCartSelection === 'function') window.saveCartSelection(); }catch(e){}
            }
        }, true);
    }
    initSelectAll();
    var mo2 = new MutationObserver(function(){ initSelectAll(); });
    mo2.observe(document.documentElement || document.body, { childList: true, subtree: true });
    updateCartTotal(); // initialize total on load
})();
</script>
<script>
// Persist selected products across fragment refreshes using localStorage
window.saveCartSelection = function(){
    try{
        var vals = Array.from(document.querySelectorAll('.select-product:checked')).map(function(cb){ return cb.value; });
        localStorage.setItem('selected_cart_items', JSON.stringify(vals));
    }catch(e){ console.warn('saveCartSelection failed', e); }
};

window.restoreCartSelection = function(){
    try{
        var s = localStorage.getItem('selected_cart_items');
        var arr = s ? JSON.parse(s) : [];
        document.querySelectorAll('.select-product').forEach(function(cb){ cb.checked = arr.indexOf(cb.value) !== -1; });
        // ensure select-all is synced
        var all = Array.from(document.querySelectorAll('.select-product'));
        var selectAll = document.getElementById('select-all');
        if(selectAll && all.length) selectAll.checked = all.every(function(cb){ return cb.checked; });
    }catch(e){ console.warn('restoreCartSelection failed', e); }
};
</script>
<script>
// Function to update the cart total based on selected products
function updateCartTotal(){
    var selected = document.querySelectorAll('.select-product:checked');
    var total = 0;
    if(selected.length === 0){
        // No selections: set total to zero
        total = 0;
    } else {
        // Sum subtotals of selected items
        selected.forEach(function(cb){
            total += parseFloat(cb.getAttribute('data-subtotal')) || 0;
        });
    }
    var totalEl = document.getElementById('cart-total');
    if(totalEl){ totalEl.innerHTML = 'Total: ' + total.toLocaleString('fr-FR') + ' FCFA'; }
}
</script>
<script>
// (removed visible checkout button handler; using floating checkout button instead)
</script>
<script>
// loader helpers
(function(){
    window.showCartLoader = function(){
        try{
            var frag = document.querySelector('.mini-cart-fragment');
            if(!frag) return;
            if(frag.querySelector('.loading-overlay')) return;
            var o = document.createElement('div'); o.className = 'loading-overlay';
            var s = document.createElement('div'); s.className = 'spinner'; o.appendChild(s);
            frag.appendChild(o);
        }catch(e){ console.error(e); }
    };
    window.hideCartLoader = function(){
        try{ var frag = document.querySelector('.mini-cart-fragment'); if(!frag) return; var o = frag.querySelector('.loading-overlay'); if(o) o.remove(); }catch(e){ console.error(e); }
    };
})();
</script>
