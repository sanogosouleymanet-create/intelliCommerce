@php
// Partial: liste des commandes d'un client
@endphp

<h2>Mes Commandes</h2>
@if(isset($commandes) && $commandes->count())
    <table class="orders-table" style="width:100%;border-collapse:collapse;">
        <thead>
            <tr>
                <th style="text-align:left;padding:8px;border-bottom:1px solid #eee;">N° Commande</th>
                <th style="text-align:left;padding:8px;border-bottom:1px solid #eee;">Date</th>
                <th style="text-align:left;padding:8px;border-bottom:1px solid #eee;">Statut</th>
                <th style="text-align:left;padding:8px;border-bottom:1px solid #eee;">Montant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commandes as $commande)
                <tr>
                    <td style="padding:8px;border-bottom:1px solid #f6f6f6;">#C-{{ $commande->idCommande }}</td>
                    <td style="padding:8px;border-bottom:1px solid #f6f6f6;">{{ $commande->DateCommande }}</td>
                    <td style="padding:8px;border-bottom:1px solid #f6f6f6;">{{ $commande->Statut ?? '—' }}</td>
                    <td style="padding:8px;border-bottom:1px solid #f6f6f6;">{{ $commande->MontanTotal ?? '—' }} FCFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>Aucune commande trouvée.</p>
@endif