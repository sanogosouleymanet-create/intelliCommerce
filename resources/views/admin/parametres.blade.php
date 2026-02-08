@extends('admin.header')

@section('content')
<div class="container mt-4">
    <h2>Paramètres du Site</h2>

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

    <form method="POST" action="{{ route('admin.parametres.update') }}">
        @csrf
        @method('PATCH')

        <div class="form-group">
            <label for="site_name">Nom du Site</label>
            <input type="text" class="form-control" id="site_name" name="site_name" value="{{ old('site_name', \App\Models\Setting::getValue('site_name', 'Mon Site')) }}">
        </div>

        <div class="form-group">
            <label for="site_description">Description du Site</label>
            <textarea class="form-control" id="site_description" name="site_description" rows="3">{{ old('site_description', \App\Models\Setting::getValue('site_description', 'Description par défaut')) }}</textarea>
        </div>

        <div class="form-group">
            <label for="contact_email">Email de Contact</label>
            <input type="email" class="form-control" id="contact_email" name="contact_email" value="{{ old('contact_email', \App\Models\Setting::getValue('contact_email', 'contact@example.com')) }}">
        </div>

        <div class="form-group">
            <label for="contact_phone">Téléphone de Contact</label>
            <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', \App\Models\Setting::getValue('contact_phone', '+1234567890')) }}">
        </div>

        <div class="form-group">
            <label for="address">Adresse</label>
            <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', \App\Models\Setting::getValue('address', 'Adresse par défaut')) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
</div>
@endsection
