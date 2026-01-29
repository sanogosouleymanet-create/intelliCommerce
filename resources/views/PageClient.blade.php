<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Profil client</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body{font-family:Arial,Helvetica,sans-serif;background:#f5f6fa;padding:20px}
        .card{background:#fff;padding:20px;border-radius:6px;max-width:720px;margin:20px auto;box-shadow:0 2px 8px rgba(0,0,0,.08)}
        .field{margin-top:8px}
    </style>
</head>
<body>
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
</body>
</html>