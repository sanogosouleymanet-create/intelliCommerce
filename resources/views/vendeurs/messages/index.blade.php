<header class="header">
    <h1>Messages</h1>
    <div class="account">
        <i class="fa-solid fa-user"></i>
        @if(isset($vendeur->Prenom) || isset($vendeur->Nom))
            {{ trim(($vendeur->Prenom ?? '') . ' ' . ($vendeur->Nom ?? '')) }}
        @else
            Mon Compte
        @endif
    </div>
</header>
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
