<section class="card">
    <h2>Paramètres du compte</h2>
    <p class="small-muted">Mettez à jour les informations de votre boutique et de contact.</p>

    <form id="settingsForm">
        @csrf
        <div id="headerFields">
            <div>
                <label>Nom de la boutique</label>
                <input type="text" name="NomBoutique" value="{{ $vendeur->NomBoutique ?? '' }}">
            </div>
            <div>
                <label>Email</label>
                <input type="email" name="email" value="{{ $vendeur->email ?? '' }}">
            </div>
        </div>

        <div id="controls" style="margin-top:10px;">
            <button id="toggleDetails" type="button" class="btn">Voir détails</button>
            <button id="toggleEdit" type="button" class="btn">Modifier</button>
            <button id="saveBtn" class="btn" type="submit" style="display:none;">Enregistrer</button>
            <button id="cancelBtn" class="btn" type="button" style="display:none;">Annuler</button>
        </div>

        <div id="detailsFields" style="display:none;margin-top:16px;">
            <h3>Détails du vendeur</h3>
            <div>
                <label>Nom</label>
                <input type="text" name="Nom" id="detail-Nom" value="{{ $vendeur->Nom ?? '' }}" readonly>
            </div>
            <div>
                <label>Prénom</label>
                <input type="text" name="Prenom" id="detail-Prenom" value="{{ $vendeur->Prenom ?? '' }}" readonly>
            </div>
            <div>
                <label>Nom de la boutique</label>
                <input type="text" name="NomBoutique" value="{{ $vendeur->NomBoutique ?? '' }}">
            </div>
            <div>
                <label>Email</label>
                <input type="email" name="email" value="{{ $vendeur->email ?? '' }}">
            </div>
            <div>
                <label>Adresse</label>
                <input type="text" name="Adresse" id="detail-Adresse" value="{{ $vendeur->Adresse ?? '' }}" readonly>
            </div>
            <div>
                <label>Téléphone</label>
                <input type="text" name="TelVendeur" id="detail-TelVendeur" value="{{ $vendeur->TelVendeur ?? '' }}" readonly>
            </div>
            <div>
                <label>Statut</label>
                <input type="text" name="Statut" id="detail-Statut" value="{{ $vendeur->Statut ?? '' }}" readonly>
            </div>
        </div>
    </form>
</section>

<script>
    (function(){
        const form = document.getElementById('settingsForm');
        const toggleDetails = document.getElementById('toggleDetails');
        const toggleEdit = document.getElementById('toggleEdit');
        const saveBtn = document.getElementById('saveBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const headerFields = document.getElementById('headerFields');
        const detailsFields = document.getElementById('detailsFields');

        // keep original values to allow cancel
        const original = {
            NomBoutique: form.querySelector('input[name="NomBoutique"]').value || '',
            email: form.querySelector('input[name="email"]').value || '',
            Nom: form.querySelector('input[name="Nom"]').value || '',
            Prenom: form.querySelector('input[name="Prenom"]').value || '',
            Adresse: form.querySelector('input[name="Adresse"]').value || '',
            TelVendeur: form.querySelector('input[name="TelVendeur"]').value || '',
            Statut: form.querySelector('input[name="Statut"]').value || '',
        };

        toggleDetails?.addEventListener('click', function(){
            // show details fields as form inputs, hide header fields
            const showing = detailsFields.style.display !== 'block';
            if(showing){
                detailsFields.style.display = 'block';
                headerFields.style.display = 'none';
                // ensure details inputs are readonly initially
                detailsFields.querySelectorAll('input')?.forEach(i => i.readOnly = true);
                saveBtn.style.display = 'none';
                cancelBtn.style.display = 'none';
                toggleEdit.textContent = 'Modifier';
            } else {
                detailsFields.style.display = 'none';
                headerFields.style.display = 'block';
            }
        });

        let editing = false;
        toggleEdit?.addEventListener('click', function(){
            // If details not visible, show details first
            if(detailsFields.style.display !== 'block'){
                toggleDetails.click();
            }
            editing = !editing;
            detailsFields.querySelectorAll('input')?.forEach(i => {
                if (i.name === 'Statut') {
                    i.readOnly = true;
                } else {
                    i.readOnly = !editing;
                }
            });
            saveBtn.style.display = editing ? 'inline-block' : 'none';
            cancelBtn.style.display = editing ? 'inline-block' : 'none';
            // hide the Modifier button while editing to avoid duplicate 'Annuler'
            toggleEdit.style.display = editing ? 'none' : 'inline-block';
            if(!editing){
                // revert values on cancelling via toggleEdit
                Object.keys(original).forEach(k => {
                    const inp = form.querySelector(`[name="${k}"]`);
                    if(inp) inp.value = original[k];
                });
            }
        });

        cancelBtn?.addEventListener('click', function(){
            // revert changes and hide details
            Object.keys(original).forEach(k => {
                const inp = form.querySelector(`[name="${k}"]`);
                if(inp) inp.value = original[k];
            });
            editing = false;
            detailsFields.querySelectorAll('input')?.forEach(i => i.readOnly = true);
            saveBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
            toggleEdit.style.display = 'inline-block';
            detailsFields.style.display = 'none';
            headerFields.style.display = 'block';
        });

        form?.addEventListener('submit', function(e){
            e.preventDefault();
            const data = new FormData(form);
            const token = document.querySelector('input[name="_token"]')?.value;
            if (token) data.set('_token', token);

            fetch('/vendeur/parametres', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: data,
            }).then(r => r.json()).then(json => {
                if (json.success) {
                    // update original and header fields
                    Object.keys(original).forEach(k => {
                        if(json.vendeur && json.vendeur[k] !== undefined){
                            original[k] = json.vendeur[k] ?? '';
                            const inp = form.querySelector(`[name="${k}"]`);
                            if(inp) inp.value = json.vendeur[k] ?? '';
                        }
                    });
                    editing = false;
                    detailsFields.querySelectorAll('input')?.forEach(i => i.readOnly = true);
                    saveBtn.style.display = 'none';
                    cancelBtn.style.display = 'none';
                    toggleEdit.style.display = 'inline-block';
                    // after saving, hide details and show header inputs
                    detailsFields.style.display = 'none';
                    headerFields.style.display = 'block';
                    alert(json.message || 'Enregistré');
                } else {
                    alert(json.message || 'Erreur lors de la sauvegarde');
                }
            }).catch(err => {
                console.error(err);
                alert('Erreur réseau');
            });
        });
    })();
</script>

<style>
    /* Styles spécifiques à la page Paramètres */
@import url('StyleVendeurProduits.css');

form#settingsForm{ 
    max-width:600px; 
    background:rgba(255,255,255,0.03); 
    padding:16px; border-radius:8px; 
}
form#settingsForm label{ 
    display:block; 
    margin-top:8px; 
    color:#dbeafe; 
}
form#settingsForm input{ 
    width:100%; 
    padding:8px; 
    border-radius:6px; 
    border:1px solid rgba(255,255,255,0.06); 
}

.btn {
    padding: 10px 18px;
    border-radius: 8px;
    border: none;
    background-color: #007bff;
    color: #fff;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}
</style>
