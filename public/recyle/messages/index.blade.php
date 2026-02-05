<section class="card">
    @if($messages->count())
        <ul class="messages-list">
            @foreach($messages as $m)
                <li class="message-item">
                    <strong>{{ $m->Sujet ?? 'Sans objet' }}</strong>
                    <div class="small-muted">{{ $m->Date ?? '' }}</div>
                    <p>{{ \Illuminate\Support\Str::limit($m->Contenu ?? '', 200) }}</p>
                </li>
            @endforeach
        </ul>
    @else
        <p>Aucun message.</p>
    @endif
</section>
