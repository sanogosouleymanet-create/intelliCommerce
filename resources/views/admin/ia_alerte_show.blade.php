
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Détail alerte IA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/StylePagePrincipale.css') }}" >
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body{font-family:'Segoe UI',Arial,Helvetica,sans-serif;background:linear-gradient(120deg,#f5f6fa 60%,#86cfe6 100%);padding:24px;min-height:100vh;}
        .wrap{max-width:900px;margin:30px auto;}
        .card{background:#fff;padding:28px 28px 20px 28px;border-radius:12px;margin-bottom:18px;box-shadow:0 4px 18px rgba(0,0,0,.09);}
        .meta{color:#6b7280;font-size:14px;margin-bottom:8px;}
        a.back{display:inline-block;margin-bottom:18px;color:#0b3546;font-weight:600;text-decoration:none;transition:color .2s}
        a.back:hover{color:#007bff;text-decoration:underline}
        .badge{display:inline-block;padding:4px 12px;border-radius:12px;font-size:13px;font-weight:700;color:#fff;margin-right:10px;letter-spacing:0.5px;vertical-align:middle;box-shadow:0 1px 4px rgba(0,0,0,0.07);}
        .badge.critical{background:#c0392b}
        .badge.warning{background:#f39c12}
        .badge.info{background:#3498db}
        .desc{margin:12px 0 0 0;color:#222;font-size:1.08rem;line-height:1.6;white-space:pre-line;}
        .section{margin-top:18px;}
        .section-title{font-weight:600;font-size:1.1rem;margin-bottom:6px;color:#0b3546;}
        .card .fa{margin-right:7px;}
        @media (max-width:700px){
            .wrap{padding:0 2vw;}
            .card{padding:12px 6px;}
        }
    </style>
</head>
<body>
    <a href="{{ route('admin.ia_alertes') }}" class="back"><i class="fa fa-arrow-left"></i> Retour</a>
    <div class="wrap">
        <div class="card">
            @php
                $lvlRaw = $alert->NiveauGravité ?? '';
                $lvl = strtolower((string)$lvlRaw);
                $isCritical = str_contains($lvl, 'crit') || str_contains($lvl, 'danger') || str_contains($lvl, 'high') || (is_numeric($lvlRaw) && intval($lvlRaw) >= 3);
                $badgeClass = $isCritical ? 'critical' : (str_contains($lvl,'warn') || str_contains($lvl,'grave') ? 'warning' : 'info');
                $badgeText = $isCritical ? 'CRITIQUE' : (str_contains($lvl,'warn') || str_contains($lvl,'grave') ? 'ALERTE' : strtoupper($lvlRaw ?: 'INFO'));
                $icon = $isCritical ? 'fa-skull-crossbones' : (str_contains($lvl,'warn') || str_contains($lvl,'grave') ? 'fa-triangle-exclamation' : 'fa-circle-info');
            @endphp
            <h2 style="margin-top:0"><span class="badge {{ $badgeClass }}"><i class="fa-solid {{ $icon }}"></i> {{ $badgeText }}</span> {{ $alert->TypeAlerte }}</h2>
            <div class="meta">Niveau: {{ $alert->NiveauGravité ?? 'N/A' }} — Créée: {{ $alert->DateCreation }}</div>
            <hr>
            <div class="desc">{{ $alert->Description }}</div>
            @if($alert->source)
                <div class="section">
                    <div class="section-title"><i class="fa fa-user"></i> Expéditeur</div>
                    @php
                        $rawSenderType = $alert->Expediteur_type ?? $alert->source_type ?? '';
                        $rawSenderId = $alert->Expediteur_id ?? $alert->source_id ?? null;
                        $senderType = class_basename($rawSenderType ?: '');
                        if ($senderType === 'Client') {
                            $senderLabel = 'Client';
                        } elseif ($senderType === 'Vendeur') {
                            $senderLabel = 'Vendeur';
                        } else {
                            $senderLabel = $senderType ?: 'N/A';
                        }
                    @endphp
                    <div class="meta"><strong>Type:</strong> {{ $senderLabel }} (ID: {{ $rawSenderId ?? 'N/A' }})</div>
                    @php $messageContent = $alert->Message ?? null; @endphp
                    @if(empty($messageContent) && isset($alert->source->Contenu))
                        @php $messageContent = $alert->source->Contenu; @endphp
                    @endif
                    @if(!empty($messageContent))
                        <div style="margin-top:6px"><strong>Message :</strong></div>
                        <div style="white-space:pre-wrap">{{ $messageContent }}</div>
                    @endif
                </div>
            @endif
            @if($alert->destinataire)
                <div class="section">
                    <div class="section-title"><i class="fa fa-user-check"></i> Destinataire</div>
                    <div class="meta"><strong>Type:</strong> {{ class_basename($alert->destinataire_type ?? '') }} (ID: {{ $alert->destinataire_id }}) </div>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
