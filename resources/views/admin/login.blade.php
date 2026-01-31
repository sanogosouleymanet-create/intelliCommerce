<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Admin - Connexion</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body{font-family:Arial,Helvetica,sans-serif;background:#f5f6fa;padding:40px}
        .card{background:#fff;padding:20px;border-radius:6px;max-width:420px;margin:40px auto;box-shadow:0 2px 8px rgba(0,0,0,.08)}
        label{display:block;margin-top:10px}
        input{width:100%;padding:8px;margin-top:6px;border:1px solid #ddd;border-radius:4px}
        button{margin-top:12px;padding:8px 12px;border:none;background:#2b7cff;color:#fff;border-radius:4px}
        .error{color:#c0392b}
    </style>
</head>
<body>
<div class="card">
    <h2>Connexion Administrateur</h2>

    @if($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.login.post') }}">
        @csrf
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required>

        <label for="motdepasse">Mot de passe</label>
        <input id="motdepasse" name="motdepasse" type="password" required>

        <button type="submit">Se connecter</button>
    </form>
</div>
</body>
</html>