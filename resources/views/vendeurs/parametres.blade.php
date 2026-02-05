@php
    // Espace Vendeur — Paramètres
    // Variables attendues : $vendeur
@endphp

<style>
    /* Force des bordures noires par défaut pour les champs */
    .vendeurs-parametres .form-control, .vendeurs-parametres input.form-control, .vendeurs-parametres textarea.form-control {
        border: 1px solid #000 !important;
        box-shadow: none !important;
        background-color: #fff;
    }
    /* Afficher aussi une bordure noire lorsque le champ est en lecture seule (form-control-plaintext) */
    .vendeurs-parametres .form-control-plaintext {
        border: 1px solid #000;
        padding: .375rem .75rem;
        border-radius: .25rem;
        background-color: transparent;
        color: #000;
    }
    .vendeurs-parametres .form-control:focus {
        box-shadow: 0 0 0 .2rem rgba(43,124,255,.15) !important;
    }
</style>

<section class="container vendeurs-parametres">
    <div class="card p-3 mb-3">
        <h4>Paramètres du compte</h4>
        <p class="text-muted small">Mettre à jour les informations de votre boutique et vos coordonnées.</p>
    </div>

    <div class="card p-3">
        <form id="formParametres" method="POST" action="/vendeur/parametres">
            @csrf
            <div id="paramStatus"></div>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Nom Boutique</label>
                    <input name="NomBoutique" class="form-control form-control-plaintext" readonly value="{{ $vendeur->NomBoutique ?? '' }}">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Email</label>
                    <input name="email" class="form-control form-control-plaintext" readonly value="{{ $vendeur->email ?? '' }}">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Nom</label>
                    <input name="Nom" class="form-control form-control-plaintext" readonly value="{{ $vendeur->Nom ?? '' }}">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Prénom</label>
                    <input name="Prenom" class="form-control form-control-plaintext" readonly value="{{ $vendeur->Prenom ?? '' }}">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Téléphone</label>
                    <input name="TelVendeur" class="form-control form-control-plaintext" readonly value="{{ $vendeur->TelVendeur ?? '' }}">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Adresse</label>
                    <input name="Adresse" class="form-control form-control-plaintext" readonly value="{{ $vendeur->Adresse ?? '' }}">
                </div>
            </div>
            <div class="text-end mt-3">
                <button type="button" id="btnEditParam" class="btn btn-outline-primary me-2">Modifier</button>
                <button id="btnSaveParam" class="btn btn-primary" type="submit" disabled>Enregistrer</button>
            </div>
        </form>
    </div>
</section>

<script>
window.CSRF_TOKEN = '{{ csrf_token() }}';

(function(){
    const form = document.getElementById('formParametres');
    if(!form || form.dataset.inited) return; form.dataset.inited = '1';

    // include all input fields except hidden inputs and buttons
    const inputs = Array.from(form.querySelectorAll('input, textarea')).filter(i => i.type !== 'hidden' && i.tagName.toLowerCase() !== 'button');
    const btnEdit = document.getElementById('btnEditParam');
    const btnSave = document.getElementById('btnSaveParam');
    const status = document.getElementById('paramStatus');

    const initial = inputs.map(i => i.value);

    function setReadOnly(state){
        inputs.forEach(i => { i.readOnly = state; i.classList.toggle('form-control-plaintext', state); i.classList.toggle('form-control', !state); });
        if(btnSave) btnSave.disabled = state;
        if(btnEdit) { btnEdit.textContent = state ? 'Modifier' : 'Annuler'; btnEdit.classList.toggle('btn-outline-danger', !state); }
    }

    setReadOnly(true);

    btnEdit?.addEventListener('click', function(){
        const editing = btnSave.disabled;
        if(editing){ setReadOnly(false); inputs[0]?.focus(); }
        else { inputs.forEach((i, idx) => i.value = initial[idx]); setReadOnly(true); }
    });

    form.addEventListener('submit', async function(e){
        e.preventDefault();
        const data = new FormData(form);
        try{
            const res = await fetch(form.action, { method: 'POST', headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN }, body: data, credentials: 'same-origin' });
            if(res.ok){
                // update initial values and reflect saved state
                inputs.forEach((i, idx) => initial[idx] = i.value);
                setReadOnly(true);
                if(status) status.innerHTML = '<div class="alert alert-success">Paramètres enregistrés</div>';
                // dispatch event for parent SPA to react
                form.dispatchEvent(new CustomEvent('saved', { detail: { success: true } }));
                setTimeout(()=>{ if(status) status.innerHTML = ''; }, 2500);
            } else {
                const j = await res.json().catch(()=>({}));
                if(j.message && status) status.innerHTML = '<div class="alert alert-danger">'+(j.message||'Erreur')+'</div>';
                else alert(j.message || 'Erreur');
            }
        }catch(e){ if(status) status.innerHTML = '<div class="alert alert-danger">Erreur de requête</div>'; }
    });
})();
</script>
