@php
// Partial: listing messages (generic)
@endphp

@if(isset($messages) && $messages->count())
    <ul style="list-style:none;padding:0;">
        @foreach($messages as $msg)
            <li style="background:#fff;padding:10px;margin-bottom:8px;border-radius:6px;">
                <div style="font-size:12px;color:#666;">{{ $msg->DateEnvoi }}</div>
                @php
                    $content = $msg->Contenu ?? '';
                    $decoded = null;
                    try { $decoded = json_decode($content, true); } catch (\Throwable $e) { $decoded = null; }
                @endphp
                @if(is_array($decoded))
                    <pre style="white-space:pre-wrap;background:#f8f9fb;padding:8px;border-radius:6px;overflow:auto;">{{ json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @else
                    <div style="margin-top:6px;">{!! nl2br(e($content)) !!}</div>
                @endif
            </li>
        @endforeach
    </ul>
@else
    <p>Aucun message pour le moment.</p>
@endif