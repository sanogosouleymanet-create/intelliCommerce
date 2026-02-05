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
        </style>
        <div class="table-responsive">
        <table class="table table-sm table-hover align-middle">
            <thead>
                <tr>
                    <th class="col-select"><input type="checkbox" id="select-all" title="Tout sélectionner"></th>
                    <th class="col-img"></th>
                    <th>Produit</th>
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
            <strong id="cart-total">Total: {{ number_format($total,0,',',' ') }} FCFA</strong>
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
                        document.querySelectorAll('.item-number').forEach(function(el){ el.textContent = (json.count || 0); });
                        var ct = document.querySelector('.cart-total'); if(ct) ct.textContent = (json.total ? Number(json.total).toLocaleString('fr-FR') + ' FCFA' : '0 FCFA');
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
                        document.querySelectorAll('.item-number').forEach(function(el){ el.textContent = (json.count || 0); });
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
                                .then(function(html){ try{ var tmp=document.createElement('div'); tmp.innerHTML=html; var frag=tmp.querySelector('.mini-cart-fragment'); if(frag){ var cur=document.querySelector('.mini-cart-fragment'); if(cur) cur.innerHTML=frag.innerHTML; } }catch(e){ console.error(e); } });
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
        });
        // clicking individual checkboxes should update the select-all state
        document.addEventListener('change', function(e){
            if(!e.target || !e.target.classList) return;
            if(e.target.classList.contains('select-product')){
                var all = Array.from(document.querySelectorAll('.select-product'));
                if(all.length === 0) return;
                var allChecked = all.every(function(cb){ return cb.checked; });
                selectAll.checked = allChecked;
            }
        }, true);
    }
    initSelectAll();
    var mo2 = new MutationObserver(function(){ initSelectAll(); });
    mo2.observe(document.documentElement || document.body, { childList: true, subtree: true });
})();
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
