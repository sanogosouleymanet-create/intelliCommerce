@php
// Paramètres du client - vue adaptée visuellement comme demandé
@endphp

<style>
    .clients-parametres .form-control, .clients-parametres input.form-control, .clients-parametres textarea.form-control {
        border: 1px solid #000 !important;
        box-shadow: none !important;
        background-color: #fff !important;
        color: #000 !important;
    }
    .clients-parametres .form-control-plaintext,
    .clients-parametres input.form-control-plaintext,
    .clients-parametres textarea.form-control-plaintext {
        border: 1px solid #000 !important;
        padding: .375rem .75rem !important;
        border-radius: .25rem !important;
        background-color: #fff !important;
        color: #000 !important;
        box-shadow: none !important;
        display: block;
        width: 100%;
    }
    .clients-parametres .form-control:focus {
        box-shadow: 0 0 0 .2rem rgba(43,124,255,.15) !important;
    }
</style>

<section class="container clients-parametres">
    <div class="card p-3 mb-3">
        <h4>Paramètres du compte</h4>
        <p class="text-muted small">Mettre à jour les informations de votre compte.</p>
    </div>

    <div class="card p-3">
        <form id="formParametres" method="POST" action="/parametres">
            @csrf
            <div id="paramStatus"></div>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Nom</label>
                    <input name="Nom" class="form-control form-control-plaintext" readonly value="{{ $client->Nom ?? '' }}">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Prénom</label>
                    <input name="Prenom" class="form-control form-control-plaintext" readonly value="{{ $client->Prenom ?? '' }}">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Email</label>
                    <input name="email" class="form-control form-control-plaintext" readonly value="{{ $client->email ?? '' }}">
                </div>
                
                
                <div class="col-md-6 mb-2">
                    <label class="form-label">Téléphone</label>
                    <input name="TelClient" class="form-control form-control-plaintext" readonly value="{{ $client->TelClient ?? '' }}">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Adresse</label>
                    <input name="Adresse" class="form-control form-control-plaintext" readonly value="{{ $client->Adresse ?? '' }}">
                </div>
            </div>
            <div class="card mt-3 p-3">
                <h5>Changer le mot de passe</h5>
                <p class="text-muted small">Laissez vide si vous ne souhaitez pas modifier le mot de passe.</p>
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Mot de passe actuel</label>
                        <input type="password" name="current_password" class="form-control form-control-plaintext" readonly>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Nouveau mot de passe</label>
                        <input type="password" name="new_password" class="form-control form-control-plaintext" readonly>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Confirmer le mot de passe</label>
                        <input type="password" name="new_password_confirmation" class="form-control form-control-plaintext" readonly>
                    </div>
                </div>
            </div>
            <div class="text-end mt-3">
                <button type="button" id="btnEditParam" class="btn btn-outline-primary me-2">Modifier</button>
                <button id="btnSaveParam" class="btn btn-primary" type="submit" disabled>Enregistrer</button>
            </div>
        </form>
    </div>
</section>