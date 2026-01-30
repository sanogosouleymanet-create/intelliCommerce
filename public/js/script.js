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
});