<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos - Site Intelli-Commerce</title>
    <link rel="stylesheet" href="{{ asset('css/StylePagePrincipale.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ENjdO4Dr2bkBIFxQpeoA6VZ6bQZ6Y9o2e2Z1ZlFZC+0h5Y5n3/tf6Yb6Y1Y3pXx+" crossorigin="anonymous">
</head>
<body>
    <div id="page" class="site">
        <header>
            <div class="header-top mobile-hide">
                <div class="conteiner">
                    <div class="wrapper flexitem">
                        <div class="left">
                            <ul class="flexitem main-links">
                                <li><a href="/">Accueil</a></li>
                                <li><a href="/a-propos" class="active">À propos</a></li>
                                <li><a href="/contact">Contact</a></li>
                            </ul>
                        </div>
                        <div class="right">
                            <ul class="flexitem main-links">
                                <li>
                                    <button onclick="window.location.href='/Connexion'" style="margin-left:10px;padding:6px 10px;border-radius:4px;border:1px solid #ddd;background:#fff;color:#2b7cff;cursor:pointer">S'inscrire/Se Connecter</button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-nav">
                <container>
                    <div class="wrapper flexitem">
                        <a href="#" class="trigger desktop-hide"><i class="ri-menu-3-line"></i></a>
                        <div class="left flexitem">
                            <div class="logo"><a href="/"><span class="circle"></span><img src="Logo-site.png" width="250" alt="logo"></a></div>
                            <nav class="mobile-hide">
                                <ul class="flexitem second-links">
                                    <li><a href="/">Accueil</a></li>
                                    <li><a href="/a-propos" class="active">À propos</a></li>
                                    <li><a href="/contact">Contact</a></li>
                                </ul>
                            </nav>
                        </div>
                        <div class="right">
                            <ul class="flexitem second-links">
                                <li><a href="#" class="iscart">
                                    <div class="icon-large"><i class="ri-shopping-cart-line"></i></div>
                                    <div class="fly-item"><span class="item-number">0</span></div>
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </container>
            </div>
        </header>

        <main>
            <div class="container py-5">
                <div class="row">
                    <div class="col-12">
                        <h1 class="text-center mb-4">À propos de Intelli-Commerce</h1>
                        <div class="row">
                            <div class="col-md-8 mx-auto">
                                <p class="lead text-center mb-4">
                                    Bienvenue sur Intelli-Commerce, votre plateforme e-commerce intelligente dédiée à la vente de produits de qualité.
                                </p>
                                <h2>Notre Mission</h2>
                                <p>
                                    Chez Intelli-Commerce, nous nous engageons à offrir une expérience d'achat en ligne exceptionnelle en connectant les vendeurs et les clients de manière transparente et sécurisée. Notre plateforme utilise des technologies avancées pour faciliter les transactions et améliorer l'expérience utilisateur.
                                </p>
                                <h2>Nos Valeurs</h2>
                                <ul>
                                    <li><strong>Qualité :</strong> Nous sélectionnons rigoureusement nos vendeurs et produits pour garantir la meilleure qualité.</li>
                                    <li><strong>Innovation :</strong> Nous intégrons constamment les dernières technologies pour améliorer notre service.</li>
                                    <li><strong>Fiabilité :</strong> La sécurité et la confiance sont au cœur de toutes nos opérations.</li>
                                    <li><strong>Communauté :</strong> Nous construisons une communauté de vendeurs et d'acheteurs satisfaits.</li>
                                </ul>
                                <h2>Notre Équipe</h2>
                                <p>
                                    Notre équipe est composée de professionnels passionnés par le e-commerce et déterminés à révolutionner l'expérience d'achat en ligne. Nous travaillons ensemble pour créer une plateforme qui répond aux besoins de tous nos utilisateurs.
                                </p>
                                <h2>Contactez-nous</h2>
                                <p>
                                    Pour toute question ou suggestion, n'hésitez pas à nous contacter via notre <a href="/contact">page de contact</a>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            <div class="container py-4">
                <div class="row">
                    <div class="col-12 text-center">
                        <p>&copy; 2024 Intelli-Commerce. Tous droits réservés.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>
