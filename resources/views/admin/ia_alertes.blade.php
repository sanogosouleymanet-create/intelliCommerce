
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Alertes IA — Aperçu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/StylePagePrincipale.css') }}" >
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body{font-family:'Segoe UI',Arial,Helvetica,sans-serif;background:linear-gradient(120deg,#86cfe6 60%,#f5f6fa 100%);padding:30px;min-height:100vh;}
        .container{max-width:1100px;margin:0 auto;box-shadow:0 8px 32px rgba(0,0,0,0.08);background:#fff;border-radius:18px;padding:36px 32px 32px 32px;}
        a.back{color:#0b3546;display:inline-block;margin-bottom:18px;text-decoration:none;font-weight:600;transition:color .2s}
        a.back:hover{color:#007bff;text-decoration:underline}
        h1{font-size:2.5rem;color:#0b3546;margin:6px 0 28px;letter-spacing:-1px;}
        .alert-card{background:#f9fafb;border-radius:10px;padding:20px 24px;box-shadow:0 2px 10px rgba(0,0,0,0.06);border-left:7px solid #c0392b;display:flex;align-items:flex-start;gap:18px;transition:transform .15s,box-shadow .15s;position:relative;}
        .alert-card:hover{transform:translateY(-4px) scale(1.01);box-shadow:0 8px 32px rgba(0,0,0,0.13);z-index:2;}
        .card-link{display:flex;flex:1;align-items:stretch;text-decoration:none;color:inherit;}
        .card-link:hover .title{color:#007bff;}
        .left-col{width:40px;display:flex;flex-direction:column;align-items:center;justify-content:center;}
        .left-col input{width:20px;height:20px;cursor:pointer;}
        .title-wrap{flex:1;min-width:0;}
        .title{font-weight:700;font-size:1.18rem;text-align:left;margin:0 0 6px;transition:color .2s;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .badge{display:inline-block;padding:4px 12px;border-radius:12px;font-size:13px;font-weight:700;color:#fff;margin-right:10px;letter-spacing:0.5px;vertical-align:middle;box-shadow:0 1px 4px rgba(0,0,0,0.07);}
        .badge.critical{background:#c0392b}
        .badge.warning{background:#f39c12}
        .badge.info{background:#3498db}
        .desc{margin:6px 0 0 0;color:#222;font-size:1.01rem;line-height:1.5;max-width:600px;white-space:pre-line;}
        .meta{color:#6b7280;font-size:13px;text-align:right;min-width:120px;}
        .actions{margin-top:10px;display:flex;gap:8px}
        .btn{background:#eee;border:1px solid #bdbdbd;padding:7px 14px;border-radius:5px;cursor:pointer;font-weight:600;transition:background .15s,border .15s;}
        .btn.danger{background:#fbeaea;border-color:#c0392b;color:#c0392b;}
        .btn.danger:disabled{opacity:0.5;cursor:not-allowed;}
        .alert-card .fa-circle-info{color:#3498db;margin-right:6px;}
        .alert-card .fa-triangle-exclamation{color:#f39c12;margin-right:6px;}
        .alert-card .fa-skull-crossbones{color:#c0392b;margin-right:6px;}
        .alert-card .quick-actions{position:absolute;top:12px;right:18px;display:flex;gap:8px;}
        .alert-card .quick-actions .fa{font-size:1.1em;cursor:pointer;opacity:0.7;transition:opacity .15s;}
        .alert-card .quick-actions .fa:hover{opacity:1;}
        @media (max-width:700px){
            .container{padding:12px 2vw;}
            .alert-card{flex-direction:column;gap:10px;padding:14px 8px;}
            .meta{min-width:0;text-align:left;}
        }
    </style>
</head>
<body>
    <a href="{{ url('/admin') }}" class="back"><i class="fa fa-arrow-left"></i> Retour</a>
    <div class="container">
        <h1><i class="fa-solid fa-robot"></i> Alertes IA</h1>
        @if($alerts->isEmpty())
            <div style="background:#fff;padding:18px;border-radius:8px;">Aucune alerte pour le moment.</div>
        @else
            <form method="POST" action="{{ route('admin.ia_alertes.delete') }}" id="delete-form" style="text-align:right;margin-bottom:12px">
                @csrf
                <button type="submit" class="btn danger" id="delete-btn" disabled><i class="fa fa-trash"></i> Supprimer la sélection</button>
            </form>
            @foreach($alerts as $a)
                <div class="alert-card" data-id="{{ $a->idAlerte }}">
                    <div class="left-col">
                        <input type="checkbox" data-id="{{ $a->idAlerte }}" class="alert-checkbox" onchange="updateDeleteButton()" onclick="event.stopPropagation();">
                    </div>
                    @php
                        $lvlRaw = $a->NiveauGravité ?? '';
                        $lvl = strtolower((string)$lvlRaw);
                        $isCritical = str_contains($lvl, 'crit') || str_contains($lvl, 'danger') || str_contains($lvl, 'high') || (is_numeric($lvlRaw) && intval($lvlRaw) >= 3);
                        $badgeClass = $isCritical ? 'critical' : (str_contains($lvl,'warn') || str_contains($lvl,'grave') ? 'warning' : 'info');
                        $badgeText = $isCritical ? 'CRITIQUE' : (str_contains($lvl,'warn') || str_contains($lvl,'grave') ? 'ALERTE' : strtoupper($lvlRaw ?: 'INFO'));
                        $icon = $isCritical ? 'fa-skull-crossbones' : (str_contains($lvl,'warn') || str_contains($lvl,'grave') ? 'fa-triangle-exclamation' : 'fa-circle-info');
                    @endphp
                    <a href="{{ route('admin.ia_alertes.show', ['id' => $a->idAlerte]) }}" class="card-link">
                        <div class="title-wrap">
                            <span class="badge {{ $badgeClass }}"><i class="fa-solid {{ $icon }}"></i> {{ $badgeText }}</span>
                            <span class="title">{{ $a->TypeAlerte ?? 'Message' }}</span>
                            <div class="desc">{{ \Illuminate\Support\Str::limit($a->Description ?? '', 160) }}</div>
                        </div>
                        <div class="meta">{{ $a->DateCreation }}</div>
                    </a>
                    <div class="quick-actions">
                        <i class="fa fa-eye" title="Voir le détail" onclick="window.location='{{ route('admin.ia_alertes.show', ['id' => $a->idAlerte]) }}';event.stopPropagation();"></i>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <script>
    function updateDeleteButton() {
        const checkboxes = document.querySelectorAll('.alert-checkbox:checked');
        const deleteBtn = document.getElementById('delete-btn');
        if (deleteBtn) deleteBtn.disabled = checkboxes.length === 0;
    }
    document.getElementById('delete-form')?.addEventListener('submit', function(e){
        if (e.submitter && e.submitter.closest('form') && e.submitter.closest('form') !== this) return;
        e.preventDefault();
        const checked = Array.from(document.querySelectorAll('.alert-checkbox')).filter(cb => cb.checked).map(cb => cb.getAttribute('data-id'));
        if (!checked.length) { alert('Aucune alerte sélectionnée.'); return; }
        if (!confirm('Supprimer les alertes sélectionnées ?')) return;
        Array.from(this.querySelectorAll('input[name="ids[]"]')).forEach(n => n.remove());
        checked.forEach(id => {
            const inp = document.createElement('input'); inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = id; this.appendChild(inp);
        });
        this.submit();
    });
    // UX: double-clic sur une carte = voir détail
    document.querySelectorAll('.alert-card').forEach(card => {
        card.addEventListener('dblclick', function(){
            const link = card.querySelector('.card-link');
            if(link) link.click();
        });
    });
    </script>
</body>
</html>
