<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Admin - Dashboard</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body{font-family:Arial,Helvetica,sans-serif;background:#f5f6fa;padding:20px}
        .wrap{max-width:960px;margin:20px auto}
        .card{background:#fff;padding:16px;border-radius:6px;margin-bottom:12px;box-shadow:0 2px 8px rgba(0,0,0,.06)}
        .row{display:flex;gap:12px}
        .stat{flex:1;padding:12px;background:#f8f9fb;border-radius:6px;text-align:center}
        form.logout{display:inline}
        button{padding:8px 12px;border:none;background:#2b7cff;color:#fff;border-radius:4px}
    </style>
</head>
<body>
<div class="wrap">
    <div style="display:flex;justify-content:space-between;align-items:center">
        <h1>Tableau de bord</h1>
        <form class="logout" method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit">Se déconnecter</button>
        </form>
    </div>

    <div class="row">
        <div class="stat card">
            <strong>Produits</strong>
            <div style="font-size:24px;margin-top:8px">{{ $counts['produits'] }}</div>
        </div>
        <div class="stat card">
            <strong>Vendeurs</strong>
            <div style="font-size:24px;margin-top:8px">{{ $counts['vendeurs'] }}</div>
        </div>
        <div class="stat card">
            <strong>Clients</strong>
            <div style="font-size:24px;margin-top:8px">{{ $counts['clients'] }}</div>
        </div>
        <div class="stat card">
            <strong>Admins</strong>
            <div style="font-size:24px;margin-top:8px">{{ $counts['administrateurs'] }}</div>
        </div>
    </div>

    <div class="card" style="margin-top:12px">
        <p>Liens rapides:</p>
        <ul>
            <li><a href="{{ url('/produits') }}">Gérer les produits</a></li>
            <li><a href="{{ url('/vendeurs') }}">Gérer les vendeurs</a></li>
            <li><a href="{{ url('/clients') }}">Gérer les clients</a></li>
        </ul>
    </div>
</div>
</body>
</html>