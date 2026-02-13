<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Détail alerte IA</title>
    <link rel="stylesheet" href="{{ asset('css/StylePagePrincipale.css') }}" >
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        
        body{font-family:Arial,Helvetica,sans-serif;background:#f5f6fa;padding:20px}
        .wrap{max-width:900px;margin:20px auto}
        .card{background:#fff;padding:16px;border-radius:6px;margin-bottom:12px;box-shadow:0 2px 8px rgba(0,0,0,.06)}
        .meta{color: #6b7280;font-size:13px}
        a.back{display:inline-block;margin-bottom:12px}
    </style>
</head>
<body>
        <a href="{{ route('admin.ia_alertes') }}" style="color: black;"><- Retour</a>

<div class="wrap">
    
    <div class="card">
        <h2 style="margin-top:0">{{ $alert->TypeAlerte }}</h2>
        <div class="meta">Niveau: {{ $alert->NiveauGravité ?? 'N/A' }} — Créée: {{ $alert->DateCreation }}</div>
        <hr>
        <!--<p style="white-space:pre-wrap">{{ $alert->Description }}</p>-->

        @if($alert->source)
            <div style="margin-top:12px;padding:10px;border-radius:6px;background:#fbfbfb">
                @php
                    // Prefer new column names, fallback to old ones if present
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
                <div class="meta"><strong>Expéditeur:</strong> {{ $senderLabel }} (ID: {{ $rawSenderId ?? 'N/A' }})</div>
                @php $messageContent = $alert->Message ?? null; @endphp
                @if(empty($messageContent) && isset($alert->source->Contenu))
                    {{-- Fallback to source->Contenu if present on related model --}}
                    @php $messageContent = $alert->source->Contenu; @endphp
                @endif
                @if(!empty($messageContent))
                    <div style="margin-top:6px"><strong>Message :</strong></div>
                    <div style="white-space:pre-wrap">{{ $messageContent }}</div>
                @endif
            </div>
        @endif

        @if($alert->destinataire)
            <div style="margin-top:12px" class="meta"><strong>Destinataire :</strong> {{ class_basename($alert->destinataire_type ?? '') }} (ID: {{ $alert->destinataire_id }}) </div>
        @endif
    </div>
</div>
    
    
</body>
</html>
