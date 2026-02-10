
<main class="main-content" id="main-content">
    <div class="admin-settings-wrapper">
        <h2 class="settings-title">Paramètres du Site</h2>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.parametres.update') }}" class="settings-form">
            @csrf
            <div class="form-section">
                <div class="form-group">
                    <label for="site_name">Nom du Site</label>
                    <input type="text" id="site_name" name="site_name" value="{{ old('site_name', \App\Models\Setting::getValue('site_name', 'Mon Site')) }}">
                </div>
                <div class="form-group">
                    <label for="site_description">Description du Site</label>
                    <textarea id="site_description" name="site_description" rows="2">{{ old('site_description', \App\Models\Setting::getValue('site_description', 'Description par défaut')) }}</textarea>
                </div>
                <div class="form-group">
                    <label for="contact_email">Email de Contact</label>
                    <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', \App\Models\Setting::getValue('contact_email', 'contact@example.com')) }}">
                </div>
                <div class="form-group">
                    <label for="contact_phone">Téléphone de Contact</label>
                    <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', \App\Models\Setting::getValue('contact_phone', '+1234567890')) }}">
                </div>
                <div class="form-group">
                    <label for="address">Adresse</label>
                    <textarea id="address" name="address" rows="2">{{ old('address', \App\Models\Setting::getValue('address', 'Adresse par défaut')) }}</textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </form>

        <div class="admin-info-section">
            <h3>Informations administrateur</h3>
            @if(isset($admin))
                <form method="POST" action="{{ route('admin.update.info') }}" class="admin-info-form">
                    @csrf
                    @method('PATCH')
                    <div class="admin-info-card">
                        <div class="admin-avatar"><i class="fa-solid fa-user fa-2x"></i></div>
                        <div class="admin-details">
                            <div class="form-group">
                                <label for="admin_prenom">Prénom</label>
                                <input type="text" id="admin_prenom" name="Prenom" value="{{ old('Prenom', $admin->Prenom ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="admin_nom">Nom</label>
                                <input type="text" id="admin_nom" name="Nom" value="{{ old('Nom', $admin->Nom ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="admin_email">Email</label>
                                <input type="email" id="admin_email" name="email" value="{{ old('email', $admin->email ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="admin_password">Nouveau mot de passe <span style="font-weight:400;font-size:0.95em;">(laisser vide pour ne pas changer)</span></label>
                                <input type="password" id="admin_password" name="MotDePasse" autocomplete="new-password">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-top:12px;">Mettre à jour mes infos</button>
                </form>
            @else
                <div class="admin-info-card">Non connecté</div>
            @endif
        </div>
    </div>
</main>
