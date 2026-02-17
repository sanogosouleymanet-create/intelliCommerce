<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Alertes IA — Aperçu</title>
    <link rel="stylesheet" href="{{ asset('css/StylePagePrincipale.css') }}" >
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body{font-family:Arial,Helvetica,sans-serif;background:#86cfe6;padding:30px}
        .container{max-width:1000px;margin:0 auto}
        a.back{color: #08304b;display:inline-block;margin-bottom:18px;text-decoration:none}
        h1{font-size:40px;color:#0b3546;margin:6px 0 18px}
        .alert-card{background:#fff;border-radius:8px;padding:18px 20px;box-shadow:0 6px 18px rgba(0,0,0,0.08);border-left:6px solid #c0392b;display:flex;align-items:flex-start;gap:16px;transition:transform .15s ease,box-shadow .15s ease}
        .alert-card:hover{transform:translateY(-6px);box-shadow:0 12px 30px rgba(0,0,0,0.14)}
        .card-link{display:flex;flex:1;align-items:stretch;text-decoration:none;color:inherit}
        .card-link:hover{text-decoration:none}
        .card-link, .alert-card{cursor:default}
        .alert-card:hover .card-link{cursor:pointer}
        .left-col{width:40px;display:flex;flex-direction:column;align-items:center}
        .left-col input{width:18px;height:18px}
        .title-wrap{flex:1}
        .title{font-weight:700;text-align:center;margin:0 0 6px}
        .badge{display:inline-block;padding:4px 8px;border-radius:12px;font-size:12px;font-weight:700;color:#fff;margin-right:8px}
        .badge.critical{background:#c0392b}
        .badge.warning{background:#f39c12}
        .badge.info{background:#3498db}
        .desc{margin:6px 0;color:#222}
        .meta{color:#6b7280;font-size:13px;text-align:right}
        .actions{margin-top:10px;display:flex;gap:8px}
        .btn{background:#eee;border:1px solid #bdbdbd;padding:6px 10px;border-radius:4px;cursor:pointer}
        .btn.danger{background: #ddd}
    </style>
</head>
<body>
    
        <a href="{{ url('/admin') }}" style="color: black;"><- Retour</a>
    
    <div class="container">
        <h1>Alertes IA</h1>

        @if($alerts->isEmpty())
            <div style="background:#fff;padding:18px;border-radius:8px;">Aucune alerte pour le moment.</div>
        @else
            <form method="POST" action="{{ route('admin.ia_alertes.delete') }}" id="delete-form" style="text-align:right;margin-bottom:12px">
                @csrf
                <button type="submit" class="btn danger" id="delete-btn" disabled>Supprimer la sélection</button>
            </form>

            @foreach($alerts as $a)
                <div class="alert-card">
                    <div class="left-col">
                        <input type="checkbox" data-id="{{ $a->idAlerte }}" class="alert-checkbox" onchange="updateDeleteButton()" onclick="event.stopPropagation();">
                    </div>

                    @php
                        $lvlRaw = $a->NiveauGravité ?? '';
                        $lvl = strtolower((string)$lvlRaw);
                        $isCritical = str_contains($lvl, 'crit') || str_contains($lvl, 'danger') || str_contains($lvl, 'high') || (is_numeric($lvlRaw) && intval($lvlRaw) >= 3);
                        $badgeClass = $isCritical ? 'critical' : (str_contains($lvl,'warn') || str_contains($lvl,'grave') ? 'warning' : 'info');
                        $badgeText = $isCritical ? 'CRITIQUE' : (str_contains($lvl,'warn') || str_contains($lvl,'grave') ? 'ALERTE' : strtoupper($lvlRaw ?: 'INFO'));
                    @endphp

                    <a href="{{ route('admin.ia_alertes.show', ['id' => $a->idAlerte]) }}" class="card-link" style="display:flex;flex:1;align-items:stretch;text-decoration:none;color:inherit">
                        <div class="title-wrap" style="padding-right:12px">
                            @if($isCritical)
                                <span class="badge critical">{{ $badgeText }}</span>
                            @elseif(!empty($lvlRaw))
                                <span class="badge {{ $badgeClass }}">{{ $badgeText }}</span>
                            @endif
                            <div class="title">{{ $a->TypeAlerte ?? 'Message' }}</div>
                            <div class="desc">{{ \Illuminate\Support\Str::limit($a->Description ?? '', 160) }}</div>
                        </div>

                        <div style="min-width:220px;text-align:right;display:flex;align-items:flex-start;justify-content:flex-end">
                            <div class="meta">{{ $a->DateCreation }}</div>
                        </div>
                    </a>
                </div>
            @endforeach
        @endif
    </div>
    
    
</body>
</html>

<script>
function updateDeleteButton() {
    const checkboxes = document.querySelectorAll('.alert-checkbox:checked');
    const deleteBtn = document.getElementById('delete-btn');
    if (deleteBtn) deleteBtn.disabled = checkboxes.length === 0;
}

document.getElementById('delete-form')?.addEventListener('submit', function(e){
    // If submit is from per-card form, let it proceed normally
    if (e.submitter && e.submitter.closest('form') && e.submitter.closest('form') !== this) return;
    e.preventDefault();
    const checked = Array.from(document.querySelectorAll('.alert-checkbox')).filter(cb => cb.checked).map(cb => cb.getAttribute('data-id'));
    if (!checked.length) { alert('Aucune alerte sélectionnée.'); return; }
    if (!confirm('Supprimer les alertes sélectionnées ?')) return;

    // remove previous hidden inputs
    Array.from(this.querySelectorAll('input[name="ids[]"]')).forEach(n => n.remove());
    checked.forEach(id => {
        const inp = document.createElement('input'); inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = id; this.appendChild(inp);
    });
    this.submit();
});
</script>
