@php
// Partial: paramètres du client (simple form)
@endphp

<h2>Paramètres</h2>
<form id="clientSettings" method="POST" action="/parametres">
    @csrf
    <div style="margin-bottom:8px;">
        <label>Email</label>
        <input type="email" name="email" value="{{ $client->email ?? '' }}">
    </div>
    <div style="margin-bottom:8px;">
        <label>Téléphone</label>
        <input type="text" name="TelClient" value="{{ $client->TelClient ?? '' }}">
    </div>
    <div>
        <button type="submit" class="filter-btn">Enregistrer</button>
    </div>
</form>