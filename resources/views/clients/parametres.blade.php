@php
// Partial: paramètres du client (form + profil affiché)
// NOTE: 'Mon profil' a été déplacé ici
@endphp

<div class="client-settings">
    <div class="card client-settings-card mb-3 p-3">
        <div class="d-flex justify-content-between align-items-start">
            <h3 class="m-0">Profil</h3>
            <div class="btn-group" role="group" aria-label="profil-actions">
                <button id="toggleDetails" type="button" class="btn btn-sm btn-outline-secondary">Voir détail</button>
                <button id="toggleEdit" type="button" class="btn btn-sm btn-outline-primary">Modifier</button>
                <button id="saveBtn" type="button" class="btn btn-sm btn-outline-primary" style="display:none;">Enregistrer</button>
                <button id="cancelBtn" type="button" class="btn btn-sm btn-outline-secondary" style="display:none;">Annuler</button>
            </div>
        </div>

        <div id="profileView" class="mt-3">
            <div class="field"><strong>Nom:</strong> <span id="displayNom">{{ $client->Nom }}</span> <span id="displayPrenom">{{ $client->Prenom ?? '' }}</span></div>
            <div class="field"><strong>Email:</strong> <span id="displayEmail">{{ $client->email }}</span></div>

            <div id="profileExtra" style="display:none; margin-top:8px;">
                <div class="field"><strong>Téléphone:</strong> <span id="displayTel">{{ $client->TelClient ?? '-' }}</span></div>
                <div class="field"><strong>Adresse:</strong> <span id="displayAdresse">{{ $client->Adresse ?? '-' }}</span></div>
                <div class="field"><strong>Date de naissance:</strong> <span id="displayNaissance">@if(!empty($client->DateDeNaissance)){{ \Carbon\Carbon::parse($client->DateDeNaissance)->format('d/m/Y') }}@else-@endif</span></div>
            </div>
        </div> 

        <form id="clientSettingsForm" style="display:none; margin-top:12px;" method="POST" action="/parametres">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label>Nom</label>
                    <input type="text" name="Nom" id="inputNom" class="form-control" value="{{ $client->Nom ?? '' }}" />
                </div>
                <div class="col-md-6 mb-2">
                    <label>Prénom</label>
                    <input type="text" name="Prenom" id="inputPrenom" class="form-control" value="{{ $client->Prenom ?? '' }}" />
                </div>
                <div class="col-md-6 mb-2">
                    <label>Email</label>
                    <input type="email" name="email" id="inputEmail" class="form-control" value="{{ $client->email ?? '' }}" />
                </div>
                <div class="col-md-6 mb-2">
                    <label>Téléphone</label>
                    <input type="text" name="TelClient" id="inputTel" class="form-control" value="{{ $client->TelClient ?? '' }}" />
                </div>
            </div>

            <hr />
            <div class="mb-2">
                <label>
                    <input type="checkbox" id="toggleChangePassword"> Modifier le mot de passe
                </label>
            </div>

            <div id="passwordFields" style="display:none;">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <label>Mot de passe actuel</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" />
                    </div>
                    <div class="col-md-6 mb-2">
                        <label>Nouveau mot de passe</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" autocomplete="new-password" />
                    </div>
                    <div class="col-md-6 mb-2">
                        <label>Confirmer le nouveau mot de passe</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" autocomplete="new-password" />
                    </div>
                </div>
            </div>
        </form>    

        <div id="settingsMessage" class="mt-3" style="display:none;"></div>
    </div>
</div>

<script>
(function(){
    const toggleDetails = document.getElementById('toggleDetails');
    const toggleEdit = document.getElementById('toggleEdit');
    const saveBtn = document.getElementById('saveBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const profileView = document.getElementById('profileView');
    const form = document.getElementById('clientSettingsForm');
    const messageBox = document.getElementById('settingsMessage');

    // keep original values to allow cancel
    const original = {
        Nom: document.getElementById('inputNom')?.value || '{{ $client->Nom ?? '' }}',
        Prenom: document.getElementById('inputPrenom')?.value || '{{ $client->Prenom ?? '' }}',
        email: document.getElementById('inputEmail')?.value || '{{ $client->email ?? '' }}',
        TelClient: document.getElementById('inputTel')?.value || '{{ $client->TelClient ?? '' }}',
    };

    toggleDetails?.addEventListener('click', function(){
        const extra = document.getElementById('profileExtra');
        if(!extra) return;
        const isVisible = extra.style.display === 'block';
        if(isVisible){
            extra.style.display = 'none';
            this.textContent = 'Voir détail';
            this.setAttribute('aria-expanded', 'false');
        } else {
            extra.style.display = 'block';
            this.textContent = 'Masquer détail';
            this.setAttribute('aria-expanded', 'true');
        }
    });

    toggleEdit?.addEventListener('click', function(){
        // show edit form and allow editing
        form.style.display = 'block';
        profileView.style.display = 'none';
        saveBtn.style.display = 'inline-block';
        cancelBtn.style.display = 'inline-block';
        toggleEdit.style.display = 'none';
        toggleDetails.style.display = 'none';
    });

    cancelBtn?.addEventListener('click', function(){
        // revert fields
        Object.keys(original).forEach(k => {
            const el = document.querySelector('#clientSettingsForm [name="'+k+'"]');
            if(el) el.value = original[k] || '';
        });
        // also clear password fields
        document.getElementById('current_password').value = '';
        document.getElementById('new_password').value = '';
        document.getElementById('new_password_confirmation').value = '';
        document.getElementById('toggleChangePassword').checked = false;
        document.getElementById('passwordFields').style.display = 'none';

        form.style.display = 'none';
        profileView.style.display = 'block';
        saveBtn.style.display = 'none';
        cancelBtn.style.display = 'none';
        toggleEdit.style.display = 'inline-block';
        toggleDetails.style.display = 'inline-block';
        messageBox.style.display = 'none';
    });

    // toggle password change fields
    const toggleChangePassword = document.getElementById('toggleChangePassword');
    const verifyBtn = document.getElementById('verifyPwdBtn');
    const verifyMsg = document.getElementById('verifyMsg');
    const newPwdContainer = document.getElementById('newPasswordContainer');
    toggleChangePassword?.addEventListener('change', function(){
        const show = this.checked;
        document.getElementById('passwordFields').style.display = show ? 'block' : 'none';
        if(!show){
            // reset verification state
            verifyMsg.style.display = 'none';
            newPwdContainer.style.display = 'none';
            document.getElementById('current_password').value = '';
            document.getElementById('new_password').value = '';
            document.getElementById('new_password_confirmation').value = '';
        }
    });

    // verify current password via AJAX before allowing new password inputs
    verifyBtn?.addEventListener('click', function(){
        const cur = document.getElementById('current_password').value || '';
        if(!cur){
            verifyMsg.style.display = 'block';
            verifyMsg.className = 'alert alert-danger';
            verifyMsg.textContent = 'Veuillez saisir votre mot de passe actuel.';
            return;
        }
        verifyBtn.disabled = true;
        fetch('/parametres/verify-password', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
            body: new FormData(document.getElementById('clientSettingsForm'))
        }).then(r => r.json()).then(json => {
            verifyBtn.disabled = false;
            if(json.success){
                verifyMsg.style.display = 'block';
                verifyMsg.className = 'alert alert-success';
                verifyMsg.textContent = 'Mot de passe vérifié. Vous pouvez maintenant saisir le nouveau mot de passe.';
                newPwdContainer.style.display = 'block';
                document.getElementById('current_password').readOnly = true;
                verifyBtn.style.display = 'none';
            } else {
                verifyMsg.style.display = 'block';
                verifyMsg.className = 'alert alert-danger';
                verifyMsg.textContent = json.message || 'Mot de passe incorrect.';
            }
        }).catch(err => {
            verifyBtn.disabled = false;
            verifyMsg.style.display = 'block';
            verifyMsg.className = 'alert alert-danger';
            verifyMsg.textContent = 'Erreur réseau';
        });
    });

    function validateEmail(email) {
        return /^\S+@\S+\.\S+$/.test(email);
    }

    saveBtn?.addEventListener('click', function(){
        // client-side validation
        const email = document.getElementById('inputEmail').value.trim();
        if(email && !validateEmail(email)){
            messageBox.style.display = 'block';
            messageBox.className = 'alert alert-danger';
            messageBox.textContent = 'Email invalide';
            return;
        }

        // password validation if user wants to change password
        const changePwd = document.getElementById('toggleChangePassword').checked;
        const current = document.getElementById('current_password').value || '';
        const newPwd = document.getElementById('new_password').value || '';
        const confirm = document.getElementById('new_password_confirmation').value || '';
        if(changePwd){
            if(!current){
                messageBox.style.display = 'block';
                messageBox.className = 'alert alert-danger';
                messageBox.textContent = 'Le mot de passe actuel est requis.';
                return;
            }
            if(newPwd.length < 8){
                messageBox.style.display = 'block';
                messageBox.className = 'alert alert-danger';
                messageBox.textContent = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
                return;
            }
            if(newPwd !== confirm){
                messageBox.style.display = 'block';
                messageBox.className = 'alert alert-danger';
                messageBox.textContent = 'La confirmation du mot de passe ne correspond pas.';
                return;
            }
        }

        const data = new FormData(form);
        const token = document.querySelector('input[name="_token"]')?.value;
        if (token) data.set('_token', token);

        saveBtn.disabled = true;
        fetch('/parametres', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
            body: data
        }).then(r => r.json()).then(json => {
            saveBtn.disabled = false;
            if(json.success){
                // update display fields
                document.getElementById('displayNom').textContent = json.client.Nom || (data.get('Nom') || '');
                document.getElementById('displayPrenom').textContent = json.client.Prenom || (data.get('Prenom') || '');
                document.getElementById('displayEmail').textContent = json.client.email || (data.get('email') || '');
                document.getElementById('displayTel').textContent = json.client.TelClient ?? (data.get('TelClient') || '-');

                // update originals
                original.Nom = json.client.Nom ?? data.get('Nom');
                original.Prenom = json.client.Prenom ?? data.get('Prenom');
                original.email = json.client.email ?? data.get('email');
                original.TelClient = json.client.TelClient ?? data.get('TelClient');

                // hide form
                form.style.display = 'none';
                profileView.style.display = 'block';
                saveBtn.style.display = 'none';
                cancelBtn.style.display = 'none';
                toggleEdit.style.display = 'inline-block';
                toggleDetails.style.display = 'inline-block';

                // clear password fields
                document.getElementById('current_password').value = '';
                document.getElementById('new_password').value = '';
                document.getElementById('new_password_confirmation').value = '';
                document.getElementById('toggleChangePassword').checked = false;
                document.getElementById('passwordFields').style.display = 'none';

                messageBox.style.display = 'block';
                messageBox.className = 'alert alert-success';
                messageBox.textContent = json.message || 'Enregistré.';
                setTimeout(()=> { messageBox.style.display = 'none'; }, 3000);
            } else {
                messageBox.style.display = 'block';
                messageBox.className = 'alert alert-danger';
                messageBox.textContent = json.message || 'Erreur lors de la sauvegarde.';
            }
        }).catch(err => {
            saveBtn.disabled = false;
            messageBox.style.display = 'block';
            messageBox.className = 'alert alert-danger';
            messageBox.textContent = 'Erreur réseau.';
            console.error(err);
        });
    });
})();
</script>

<style>
    .client-settings-card .field{ margin-bottom:6px; }
    .client-settings-card .form-control{ background:transparent; color:inherit; border:1px solid rgba(255,255,255,0.06); }
    /* spacing between buttons and rounded corners */
    .client-settings-card .btn-group { display:flex; gap:0.5rem; }
    .client-settings-card .btn { border-radius:8px !important; transition: transform .12s ease, box-shadow .12s ease, background-color .15s ease; }
    .client-settings-card .btn-sm { padding: 6px 10px; }
    /* Ensure btn-group children keep rounded corners */
    .client-settings-card .btn-group .btn { border-radius:8px !important; }

    /* Hover/active styles to match Voir détail / Modifier effects */
    .client-settings-card .btn-outline-primary:hover,
    .client-settings-card .btn-outline-secondary:hover,
    .client-settings-card .btn-outline-success:hover{
        color: #fff !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.12);
    }
    .client-settings-card .btn-outline-primary:hover{ background: #0d6efd; border-color: #0d6efd; }
    .client-settings-card .btn-outline-secondary:hover{ background: #6c757d; border-color: #6c757d; }
    .client-settings-card .btn-outline-success:hover{ background: #198754; border-color: #198754; }
</style>
