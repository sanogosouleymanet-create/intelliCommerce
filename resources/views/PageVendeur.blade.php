<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord Vendeur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/StylePageVendeur.css') }}">
</head>
<body>

<div class="container">

    <!-- SIDEBAR -->
    <aside class="sidebar">
    <h2 class="logo"><i class="fa-solid fa-store"></i> IntelliCommerce</h2>
    <ul>
        <li class="active"><i class="fa-solid fa-chart-line"></i> Tableau de Bord</li>
        <li><i class="fa-solid fa-box"></i> Produits</li>
        <li><i class="fa-solid fa-cart-shopping"></i> Commandes</li>
        <li><i class="fa-solid fa-users"></i> Clients</li>
        <li><i class="fa-solid fa-chart-pie"></i> Analyses</li>
        <li><i class="fa-solid fa-gear"></i> Paramètres</li>
        <li><i class="fa-solid fa-envelope"></i> Messages</li>
    </ul>
</aside>


    <!-- CONTENU PRINCIPAL -->
    <main class="main-content">

        <!-- HEADER -->
        <header class="header">
            <h1>Tableau de Bord Vendeur</h1>
            <div class="account">
    <i class="fa-solid fa-user"></i> Mon Compte
</div>

        </header>

        <section class="stats">
    <div class="card">
    <h3><i class="fa-solid fa-euro-sign"></i> Revenus Totaux</h3>
    <p class="number">12 500 €</p>
    <span class="green"><i class="fa-solid fa-arrow-up"></i> +15% ce mois-ci</span>
</div>

<div class="card">
    <h3><i class="fa-solid fa-clock"></i> Commandes en Attente</h3>
    <p class="number">45</p>
    <span class="green"><i class="fa-solid fa-arrow-up"></i> +15% ce mois-ci</span>
</div>

<div class="card">
    <h3><i class="fa-solid fa-chart-simple"></i> Performance du Mois</h3>
    <p class="number">230</p>
    <span class="green"><i class="fa-solid fa-arrow-up"></i> +10%</span>
</div>

</section>

<section class="orders">
    <h2>Commandes Récentes</h2>

    <table>
        <tr>
            <th>N° Commande</th>
            <th>Date</th>
            <th>Statut</th>
        </tr>
        <tr>
            <td>#C-00123</td>
            <td>2023-10-26</td>
            <td>150,00 €</td>
        </tr>
        <tr>
            <td>Jean Dupont</td>
            <td>2023-10-26</td>
            <td>En cours</td>
        </tr>
        <tr>
            <td>Marie Lefebre</td>
            <td>2023-10-25</td>
            <td>Livré</td>
        </tr>
    </table>
</section>

<section class="top-products">
    <h2>Top Produits</h2>

    <div class="product">
    <span><i class="fa-solid fa-shirt"></i> T-shirt Coton Premium</span>
    <small>500 vendus</small>
</div>

<div class="product">
    <span><i class="fa-solid fa-hat-cowboy"></i> Casquette Stylée</span>
    <small>320 vendus</small>
</div>

<div class="product">
    <span><i class="fa-solid fa-backpack"></i> Sac à Dos Urbain</span>
    <small>210 vendus</small>
</div>

</section>


    </main>

</div>


</body>
</html>
