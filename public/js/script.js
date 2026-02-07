// Constructeur du menu mobile/off-canvas (extraction groupée propre des méga-colonnes)
document.addEventListener('DOMContentLoaded', function () {
    // Construire la liste des départements pour mobile et copier la navigation / les liens supérieurs
    function copyMenu() {
        var dptSource = document.querySelector('.dpt-cat .dpt-menu > ul');
        var dptPlace = document.querySelector('.department');
        if (dptSource && dptPlace) {
            var mobileUl = document.createElement('ul');
            mobileUl.className = 'mobile-departments';

            Array.from(dptSource.children).forEach(function(li){
                if (li.tagName !== 'LI') return;
                var newLi = document.createElement('li');
                newLi.className = 'mobile-item';

                var icon = li.querySelector('.icon-large');
                var iconHtml = icon ? icon.innerHTML : '';

                // texte de l'ancre de niveau supérieur (nom de la catégorie)
                var topA = li.querySelector('a');
                var label = topA ? topA.textContent.trim() : li.textContent.trim();

                var hasSub = !!li.querySelector('ul') || !!li.querySelector('.mega');

                var link = document.createElement('a');
                link.href = topA ? (topA.getAttribute('href') || '#') : '#';
                link.className = 'mobile-link';
                link.innerHTML = '<span class="mobile-icon">' + iconHtml + '</span>' +
                                 '<span class="mobile-label">' + label + '</span>' +
                                 '<span class="mobile-chevron">' + (hasSub ? '<i class="ri-arrow-right-s-line"></i>' : '') + '</span>';

                newLi.appendChild(link);

                // construire le conteneur du sous-menu
                var found = false;
                var subUl = document.createElement('ul');
                subUl.className = 'mobile-sub';

                // sous-liste simple (ul direct)
                var sub = li.querySelector('ul');
                if (sub) {
                    Array.from(sub.querySelectorAll('li')).forEach(function(sli){
                        if (sli.tagName !== 'LI') return;
                        var subLi = document.createElement('li');
                        var subA = sli.querySelector('a');
                        var subLink = document.createElement('a');
                        subLink.href = subA ? (subA.getAttribute('href') || '#') : '#';
                        subLink.textContent = subA ? subA.textContent.trim() : sli.textContent.trim();
                        subLi.appendChild(subLink);
                        subUl.appendChild(subLi);
                        found = true;
                    });
                }

                // méga : grouper les colonnes en sous-groupes repliables
                var mega = li.querySelector('.mega');
                if (mega) {
                    Array.from(mega.querySelectorAll('ul')).forEach(function(colUl){
                        // trouver le titre dans la même colonne (h4 > a)
                        var row = null;
                        try { row = colUl.closest('.row'); } catch (err) { row = null; }
                        var headingLink = row ? row.querySelector('h4 a') : null;

                        var groupLi = document.createElement('li');
                        groupLi.className = 'mobile-sub-group';

                        if (headingLink) {
                            var groupLink = document.createElement('a');
                            groupLink.className = 'mobile-group-link';
                            groupLink.href = headingLink.getAttribute('href') || '#';
                            groupLink.innerHTML = '<span class="group-title">' + headingLink.textContent.trim() + '</span>' +
                                                   '<span class="group-chevron"><i class="ri-arrow-right-s-line"></i></span>';
                            groupLi.appendChild(groupLink);
                        }

                        var innerUl = document.createElement('ul');
                        innerUl.className = 'mobile-sub-inner';

                        Array.from(colUl.querySelectorAll('li')).forEach(function(sli){
                            if (sli.tagName !== 'LI') return;
                            var subLi = document.createElement('li');
                            var subA = sli.querySelector('a');
                            var subLink = document.createElement('a');
                            subLink.href = subA ? (subA.getAttribute('href') || '#') : '#';
                            subLink.textContent = subA ? subA.textContent.trim() : sli.textContent.trim();
                            subLi.appendChild(subLink);
                            innerUl.appendChild(subLi);
                            found = true;
                        });

                        groupLi.appendChild(innerUl);
                        subUl.appendChild(groupLi);
                    });
                }

                if (found) newLi.appendChild(subUl);
                mobileUl.appendChild(newLi);
            });

            dptPlace.innerHTML = '';
            dptPlace.appendChild(mobileUl);
        }

        // copier la navigation principale (sans méga) et les liens supérieurs
        var mainNavUl = document.querySelector('.header-nav nav > ul');
        var navPlace = document.querySelector('.off-canvas .nav');
        if (mainNavUl && navPlace) {
            var navCopy = mainNavUl.cloneNode(true);
            navCopy.querySelectorAll('.mega').forEach(function(n){ n.remove(); });
            navPlace.innerHTML = '';
            navPlace.appendChild(navCopy);
        }

        var topLinks = document.querySelector('.header-top .main-links');
        var topPlace = document.querySelector('.off-canvas .thetop-nav');
        if (topLinks && topPlace) {
            topPlace.innerHTML = '';
            topPlace.appendChild(topLinks.cloneNode(true));
        }
    }

    copyMenu();

    // attacher les bascules de sous-menus en toute sécurité dans les conteneurs (header-nav et off-canvas)
    function attachToggle(container) {
        if (!container) return;
        var toggles = container.querySelectorAll('.has-child .icon-small');
        toggles.forEach(function(menu){
            menu.addEventListener('click', function(e){
                e.preventDefault();
                var li = menu.closest('.has-child');
                if (!li) return;
                container.querySelectorAll('.has-child').forEach(function(item){
                    if (item !== li) item.classList.remove('expand');
                });
                li.classList.toggle('expand');
            });
        });
    }

    attachToggle(document.querySelector('.header-nav'));
    attachToggle(document.querySelector('.off-canvas'));

    // Close header nav menu when clicking outside
    document.addEventListener('click', function(e){
        var headerNav = document.querySelector('.header-nav');
        if (!headerNav) return;
        var inside = e.target.closest('.header-nav');
        if (!inside) {
            headerNav.querySelectorAll('.has-child.expand').forEach(function(item){
                item.classList.remove('expand');
            });
        }
    });

    // Bascules spécifiques au mobile pour la liste générée par le script (catégories de premier niveau)
    function attachMobileToggles() {
        var mobileItems = document.querySelectorAll('.mobile-departments .mobile-item');
        mobileItems.forEach(function(item){
            var link = item.querySelector('.mobile-link');
            var sub = item.querySelector('.mobile-sub');
            if (!link) return;
            if (sub) {
                link.addEventListener('click', function(e){
                    e.preventDefault();
                    mobileItems.forEach(function(sib){ if (sib !== item) sib.classList.remove('expand'); });
                    item.classList.toggle('expand');
                });
            }
        });
    }

    // Bascules pour les sous-sections groupées à l'intérieur de .mega (mobile)
    function attachMobileGroupToggles() {
        var groupLinks = document.querySelectorAll('.mobile-sub .mobile-group-link');
        groupLinks.forEach(function(gl){
            gl.addEventListener('click', function(e){
                e.preventDefault();
                var group = gl.closest('.mobile-sub-group');
                if (!group) return;
                var parent = group.parentElement;
                if (parent) {
                    parent.querySelectorAll('.mobile-sub-group').forEach(function(s){ if (s !== group) s.classList.remove('expand'); });
                }
                group.classList.toggle('expand');
            });
        });
    }

    attachMobileToggles();
    attachMobileGroupToggles();

    // Gestionnaires d'ouverture / fermeture de l'off-canvas
    var siteOff = document.querySelector('.site-off');
    var triggerBtn = document.querySelector('.trigger');
    var offCloseBtn = document.querySelector('.off-close');

    function openOffCanvas() {
        if (siteOff) siteOff.classList.add('open');
        var overlay = document.querySelector('.off-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'off-overlay';
            document.body.appendChild(overlay);
        }
        overlay.classList.add('open');
    }
    function closeOffCanvas() {
        if (siteOff) siteOff.classList.remove('open');
        document.querySelectorAll('.mobile-departments .mobile-item.expand').forEach(function(i){ i.classList.remove('expand'); });
        var overlay = document.querySelector('.off-overlay');
        if (overlay) overlay.classList.remove('open');
    }

    if (triggerBtn) {
        triggerBtn.addEventListener('click', function(e){
            e.preventDefault();
            openOffCanvas();
        });
    }
    if (offCloseBtn) {
        offCloseBtn.addEventListener('click', function(e){
            e.preventDefault();
            closeOffCanvas();
        });
    }

    // fermer lorsque l'on clique en dehors du panneau off-canvas
    document.addEventListener('click', function(e){
        if (!siteOff || !siteOff.classList.contains('open')) return;
        var inside = e.target.closest && e.target.closest('.site-off');
        var isTrigger = e.target.closest && e.target.closest('.trigger');
        var isOverlay = e.target.closest && e.target.closest('.off-overlay');
        if (!inside && !isTrigger && !isOverlay) closeOffCanvas();
    });

    // fermer lorsque l'on clique sur l'overlay
    document.addEventListener('click', function(e){
        var overlay = e.target.closest && e.target.closest('.off-overlay');
        if (overlay) closeOffCanvas();
    });

        // Intercept links with ?categorie= and perform a quick search by name instead
        document.addEventListener('click', function(e){
            var a = e.target.closest && e.target.closest('a');
            if (!a || !a.href) return;
            try{
                var u = new URL(a.href);
                var cat = u.searchParams.get('categorie');
                if(cat){
                    e.preventDefault();
                    // Redirect to homepage search by name (use same origin)
                    window.location.href = '/' + '?recherche=' + encodeURIComponent(cat);
                }
            }catch(err){ /* ignore non-URL hrefs */ }
        });

        // Cart: helper to add product via AJAX and update header counters
        function getCsrf(){
            var m = document.querySelector('meta[name="csrf-token"]');
            return m ? m.getAttribute('content') : null;
        }

        function updateHeaderCart(count, total){
            document.querySelectorAll('.item-number').forEach(function(el){ el.textContent = count; });
            var cartTotal = document.querySelector('.cart-total');
            if(cartTotal) cartTotal.textContent = (total ? (Number(total).toLocaleString('fr-FR') + ' FCFA') : '0 FCFA');
        }

        function addToCartRequest(id, qty, btn){
            // If user is not authenticated at all, redirect to Connexion page
            try{
                if(typeof window.isAuthenticated !== 'undefined' && !window.isAuthenticated){
                    var dest = '/Connexion?redirect=' + encodeURIComponent(window.location.href);
                    window.location.href = dest;
                    return Promise.resolve({ success: false, redirected: true });
                }
            }catch(err){ /* ignore */ }
            var token = getCsrf();
            var fd = new FormData(); fd.append('id', id); fd.append('qty', qty || 1);
            var opts = { method: 'POST', headers: {'X-Requested-With':'XMLHttpRequest'} , body: fd, credentials: 'same-origin' };
            if(token) opts.headers['X-CSRF-TOKEN'] = token;
            return fetch('/cart/add', opts).then(function(r){ return r.json(); })
                .then(function(json){
                    if(json && json.success){
                            updateHeaderCart(json.count || 0, json.total || 0);
                            if(typeof showToast === 'function') showToast('Produit ajouté au panier');
                        } else {
                            console.error('Add to cart failed', json);
                            alert(json && json.message ? json.message : 'Erreur ajout panier');
                        }
                    }).catch(function(err){ console.error(err); alert('Erreur réseau'); });
        }

        // delegated clicks for add-to-cart buttons
        document.addEventListener('click', function(e){
            var btn = e.target.closest && e.target.closest('.add-to-cart, .add-to-cart-similar, .add-to-cart-fragment');
            if(!btn) return;
            e.preventDefault();
            var id = btn.dataset.id || btn.getAttribute('data-id');
            if(!id) return alert('Produit introuvable');
            // If not authenticated, redirect to Connexion instead of attempting AJAX
            if(typeof window.isAuthenticated !== 'undefined' && !window.isAuthenticated){
                window.location.href = '/Connexion?redirect=' + encodeURIComponent(window.location.href);
                return;
            }
            // fire request without changing the button state
            addToCartRequest(id, 1, btn);
        });

        // also listen to custom event (for components that dispatch it)
        document.addEventListener('product-added-to-cart', function(ev){
            var id = ev && ev.detail && ev.detail.id;
            if(!id) return;
            addToCartRequest(id, 1, null).then(function(){
                // refresh mini-cart if open
                if(document.getElementById('mini-cart-overlay') && document.getElementById('mini-cart-overlay').style.display !== 'none') refreshMiniCart();
            });
        });

        // toast helper
        function showToast(text, timeout){
            timeout = timeout || 3000;
            var container = document.getElementById('toast-container');
            if(!container){
                container = document.createElement('div'); container.id = 'toast-container';
                container.style = 'position:fixed;right:16px;bottom:16px;z-index:2000;display:flex;flex-direction:column;gap:8px';
                document.body.appendChild(container);
            }
            var el = document.createElement('div');
            el.style = 'background:#222;color:#fff;padding:10px 12px;border-radius:6px;box-shadow:0 4px 12px rgba(0,0,0,0.12);opacity:0.95';
            el.textContent = text;
            container.appendChild(el);
            setTimeout(function(){ el.remove(); }, timeout);
        }

        // intercept cart update/remove forms to use AJAX
        document.addEventListener('submit', function(e){
            var form = e.target;
            if(form.classList.contains('cart-update-form') || form.classList.contains('cart-remove-form')){
                e.preventDefault();
                var url = form.getAttribute('action');
                var fd = new FormData(form);
                var tokenMeta = document.querySelector('meta[name="csrf-token"]');
                var headers = { 'X-Requested-With': 'XMLHttpRequest' };
                if(tokenMeta) headers['X-CSRF-TOKEN'] = tokenMeta.getAttribute('content');
                fetch(url, { method: 'POST', headers: headers, body: fd, credentials: 'same-origin' })
                    .then(function(r){ return r.json(); })
                    .then(function(json){
                        if(!json || !json.success) { showToast(json && json.message ? json.message : 'Erreur panier'); return; }
                        // update header counters
                        updateHeaderCart(json.count || 0, json.total || 0);
                        showToast('Panier mis à jour');
                        // if remove form, remove row
                        if(form.classList.contains('cart-remove-form')){
                            var id = form.dataset.id || fd.get('id');
                            var row = document.querySelector('tr[data-id="'+id+'"]'); if(row) row.remove();
                        }
                        // if update form, update subtotal and cart total
                        if(form.classList.contains('cart-update-form')){
                            var id = form.dataset.id || fd.get('id');
                            var qty = Number(fd.get('qty')) || 0;
                            // fetch product price from row
                            var row = document.querySelector('tr[data-id="'+id+'"]');
                            if(row){
                                var priceText = row.querySelector('.cart-prod-price').textContent.replace(/[^0-9,]/g,'').replace(',','');
                                var price = Number(priceText) || 0;
                                var subtotal = Math.round(price * qty);
                                var subEl = row.querySelector('.cart-subtotal');
                                if(subEl) subEl.textContent = subtotal.toLocaleString('fr-FR') + ' FCFA';
                                // update data-subtotal on checkbox
                                var cb = row.querySelector('.select-product');
                                if(cb) cb.setAttribute('data-subtotal', subtotal);
                            }
                            if(typeof updateCartTotal === 'function') updateCartTotal(); // update total after quantity change
                        }
                        // update page total element
                        var totalEl = document.getElementById('cart-total');
                        if(totalEl) totalEl.textContent = 'Total: ' + (json.total ? Number(json.total).toLocaleString('fr-FR') + ' FCFA' : '0 FCFA');

                        // If the cart UI is not the modal (i.e. full page), reload to reflect server state
                        var overlay = document.getElementById('mini-cart-overlay');
                        var isModalOpen = overlay && overlay.style.display && overlay.style.display !== 'none';
                        if(!isModalOpen){
                            // small delay so toast is visible
                            setTimeout(function(){ location.reload(); }, 300);
                        }
                    }).catch(function(err){ console.error(err); showToast('Erreur réseau'); });
                // if modal open, refresh mini-cart
                if(document.getElementById('mini-cart-overlay') && document.getElementById('mini-cart-overlay').style.display !== 'none') refreshMiniCart();
            }
        });

    // open mini-cart and load contents
    function refreshMiniCart(){
        var overlay = document.getElementById('mini-cart-overlay');
        var body = document.getElementById('mini-cart-body');
        var footerTotal = document.getElementById('mini-cart-footer-total');
        if(!overlay || !body) return;
        body.innerHTML = '<div style="text-align:center;color:#666">Chargement…</div>';
        fetch('/cart', { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
            .then(function(r){ return r.text(); })
            .then(function(html){
            console.log('Mini-cart HTML response:', html);
                // replace body with server returned markup
                body.innerHTML = html;
                // try to extract total displayed in returned HTML
                var totalEl = document.querySelector('#cart-total');
                if(totalEl) footerTotal.textContent = totalEl.textContent.replace('Total: ','');
            }).catch(function(err){ body.innerHTML = '<div class="text-danger">Impossible de charger le panier.</div>'; });
    }

    // click handler for cart icon
    document.addEventListener('click', function(e){
        var cartBtn = e.target.closest && e.target.closest('.iscart');
        if(!cartBtn) return;
        e.preventDefault();
        var overlay = document.getElementById('mini-cart-overlay');
        if(!overlay) return;
        // use flex to center and show the overlay
        overlay.style.display = 'flex';
        refreshMiniCart();
    });

    // close mini-cart (header close)
    document.addEventListener('click', function(e){
        if(e.target && e.target.id === 'mini-cart-close'){
            var overlay = document.getElementById('mini-cart-overlay');
            if(!overlay) return;
            // If a product detail is displayed (history state contains produitId), go back
            try{
                if(history.state && history.state.produitId){
                    history.back();
                    return;
                }
            }catch(err){ /* ignore */ }
            overlay.style.display = 'none';
            return;
        }
        var overlay = e.target.closest && e.target.closest('#mini-cart-overlay');
        if(overlay && e.target === overlay){ overlay.style.display = 'none'; }
    });
});