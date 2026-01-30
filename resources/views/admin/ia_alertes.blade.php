<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Alertes IA</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body{font-family:Arial,Helvetica,sans-serif;background:#f5f6fa;padding:20px}
        .wrap{max-width:960px;margin:20px auto}
        .card{background:#fff;padding:16px;border-radius:6px;margin-bottom:12px;box-shadow:0 2px 8px rgba(0,0,0,.06)}
        .alert{border-left:4px solid #c0392b;padding:10px;margin-bottom:8px}
        .meta{color:#6b7280;font-size:12px}
        a.back{display:inline-block;margin-bottom:12px}
    </style>
</head>
<body>
<div class="wrap">
    <a href="{{ route('admin.dashboard') }}" class="back">&larr; Retour au tableau de bord</a>
    <h1>Alertes IA</h1>

    @if($alerts->isEmpty())
        <div class="card">Aucune alerte pour le moment.</div>
    @else
        @foreach($alerts as $a)
            <div class="card alert">
                <strong>{{ $a->TypeAlerte }}</strong>
                <div class="meta">Niveau: {{ $a->NiveauGravité ?? 'N/A' }} — {{ $a->DateCreation }}</div>
                <div style="margin-top:8px">{{ $a->Description }}</div>
            </div>
        @endforeach
    @endif
</div>
</body>
</html>