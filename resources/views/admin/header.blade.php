<header class="header" style="display:flex;justify-content:space-between;align-items:center;gap:12px">
            <h1>Tableau de bord</h1>
            <div style="display:flex;align-items:center;gap:12px">
                <div class="account">
                    <i class="fa-solid fa-user"></i>
                    {{-- Affiche le prénom et le nom de l'administrateur s'il est connecté, sinon celui du vendeur, sinon "Mon Compte" --}}
                    @if(isset($admin) && (isset($admin->Prenom) || isset($admin->Nom)))
                        {{ trim(($admin->Prenom ?? '') . ' ' . ($admin->Nom ?? '')) }}
                    @elseif(isset($vendeur) && (isset($vendeur->Prenom) || isset($vendeur->Nom)))
                        {{ trim(($vendeur->Prenom ?? '') . ' ' . ($vendeur->Nom ?? '')) }}
                    @else
                        Mon Compte
                    @endif
                </div>

                <a href="{{ route('admin.ia_alertes') }}" class="ia-notif" title="Alertes IA" style="position:relative;display:inline-flex;align-items:center">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M12 2C10.3431 2 9 3.34315 9 5V6.087C6.16344 6.65392 4 9.12479 4 12V17L2 19V20H22V19L20 17V12C20 9.12479 17.8366 6.65392 15 6.087V5C15 3.34315 13.6569 2 12 2Z" stroke="#fff" stroke-width="1"/>
                        <path d="M9.5 21C9.5 22.1046 10.3954 23 11.5 23H12.5C13.6046 23 14.5 22.1046 14.5 21" stroke="#fff" stroke-width="1"/>
                    </svg>
                    @if(($counts['ia_alertes'] ?? 0) > 0)
                        <span style="position:absolute;top:-6px;right:-6px;background:#c0392b;color:#fff;border-radius:999px;padding:2px 6px;font-size:12px;min-width:20px;text-align:center">{{ $counts['ia_alertes'] ?? 0 }}</span>
                    @endif
                </a>
            </div>
        </header>