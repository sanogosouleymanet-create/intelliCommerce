<section class="orders-header">
    <!--<div>
        <h2>Liste des clients</h2>
        <p class="small-muted">Recherchez et gérez vos clients.</p>
    </div>-->
    <div class="clients-filtres">
        <div class="orders-actions">
            <input type="text" id="searchClient" placeholder="Rechercher par nom ou email" class="search-input">
            <button class="btn" id="btnFilterClients">Rechercher</button>
        </div>
    </div>
</section>

<!--<section class="orders-summary">
    <div class="summary-card">
        <div>Clients totaux</div>
        <strong>{{ is_array($clients) ? count($clients) : $clients->count() }}</strong>
    </div>
</section>-->

    <section class="clients-list">
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Commandes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="clientsBody">
                @foreach($clients as $client)
                    <tr data-id="{{ $client->idClient }}">
                        <td>{{ $client->Nom }} {{ $client->Prenom }}</td>
                        <td>{{ $client->email }}</td>
                        <td>{{ $client->TelClient ?? '—' }}</td>
                        <td>{{ $client->commandes()->count() }}</td>
                        <td><button class="btn secondary btn-view-client">Voir</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <script>
        document.getElementById('btnFilterClients')?.addEventListener('click', function(){
            const q = (document.getElementById('searchClient')?.value || '').toLowerCase();
            document.querySelectorAll('#clientsBody tr[data-id]').forEach(function(row){
                const name = row.querySelectorAll('td')[0]?.textContent.toLowerCase() || '';
                const email = row.querySelectorAll('td')[1]?.textContent.toLowerCase() || '';
                const matches = q === '' || name.includes(q) || email.includes(q);
                row.style.display = matches ? '' : 'none';
            });
        });

        // Example client view action (placeholder)
        document.addEventListener('click', function(e){
            const btn = e.target.closest('.btn-view-client');
            if(!btn) return;
            const tr = btn.closest('tr');
            const id = tr?.dataset?.id;
            alert('Afficher le client: ' + id);
        });
    </script>

<style>
    /* Styles spécifiques à la page Clients 
@import url('StyleVendeurProduits.css');*/

.clients-list .orders-table td, .clients-list .orders-table th{
    color: #e6eef8;
}
.clients-list .btn-view-client{ padding:6px 10px; }

/*.summary-card{ 
    background:#ffffff; 
    padding-bottom: 15px;
}*/

.clients-filtres {
    display: flex;
    gap: 12px;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;

    background-color: #ffffff;
    padding: 15px 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}
/* Champ de recherche */
.search-input {
    padding: 10px 14px;
    width: 250px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    outline: none;
    transition: border-color 0.3s ease;
}
.search-input:focus {
    border-color: #007bff;
}
/* Bouton */
.btn {
    padding: 10px 18px;
    border-radius: 8px;
    border: none;
    background-color: #007bff;
    color: #fff;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}
</style>
