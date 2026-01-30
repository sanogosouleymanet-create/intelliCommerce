@php
// Partial: affichage du profil client (injectable dans SPA PageClient)
@endphp

<div class="card">
    <h1>Profil</h1>
    <div class="field"><strong>Nom:</strong> {{ $client->Nom }} {{ $client->Prenom ?? '' }}</div>
    <div class="field"><strong>Email:</strong> {{ $client->email }}</div>
    <div class="field"><strong>Téléphone:</strong> {{ $client->TelClient ?? '-' }}</div>

    <form class="logout" method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Se déconnecter</button>
    </form>
</div>