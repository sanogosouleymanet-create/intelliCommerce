<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Site Intelli-Commerce</title>
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
                                <li><a href="/a-propos">À propos</a></li>
                                <li><a href="/contact" class="active">Contact</a></li>
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
                                    <li><a href="/a-propos">À propos</a></li>
                                    <li><a href="/contact" class="active">Contact</a></li>
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
                        <h1 class="text-center mb-4">Contactez-nous</h1>
                        <div class="row">
                            <div class="col-md-8 mx-auto">
                                <p class="lead text-center mb-4">
                                    Nous sommes là pour vous aider. N'hésitez pas à nous contacter pour toute question ou assistance.
                                </p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3>Informations de contact</h3>
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-envelope"></i> Email: intelli-commerce@gmail.com</li>
                                            <li><i class="fas fa-phone"></i> Téléphone: +223 98 00 31 87 / 98 00 30 61</li>
                                            <li><i class="fas fa-map-marker-alt"></i> Adresse: Bamako, Mali</li>
                                            <li><i class="fas fa-clock"></i> Horaires: 24H/24 & 7j/7</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h3>Envoyez-nous un message</h3>
                                        <form action="#" method="post" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef); padding: 30px; border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); border: 1px solid rgba(255,255,255,0.2);">
                                            @csrf
                                            <fieldset style="border: none; padding: 0; margin: 0;">
                                                <div style="margin-bottom: 20px;">
                                                    <label for="name" style="display: block; font-weight: bold; margin-bottom: 8px; color: #333; font-size: 14px;">NOM COMPLET :</label>
                                                    <input type="text" id="name" name="name" placeholder="Votre nom complet" required style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; background: rgba(255,255,255,0.9); transition: all 0.3s ease; box-sizing: border-box;">
                                                </div>
                                                <div style="margin-bottom: 20px;">
                                                    <label for="email" style="display: block; font-weight: bold; margin-bottom: 8px; color: #333; font-size: 14px;">ADRESSE EMAIL :</label>
                                                    <input type="email" id="email" name="email" placeholder="votre.email@example.com" required style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; background: rgba(255,255,255,0.9); transition: all 0.3s ease; box-sizing: border-box;">
                                                </div>
                                                <div style="margin-bottom: 20px;">
                                                    <label for="subject" style="display: block; font-weight: bold; margin-bottom: 8px; color: #333; font-size: 14px;">SUJET DU MESSAGE :</label>
                                                    <input type="text" id="subject" name="subject" placeholder="Objet de votre message" required style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; background: rgba(255,255,255,0.9); transition: all 0.3s ease; box-sizing: border-box;">
                                                </div>
                                                <div style="margin-bottom: 25px;">
                                                    <label for="message" style="display: block; font-weight: bold; margin-bottom: 8px; color: #333; font-size: 14px;">VOTRE MESSAGE :</label>
                                                    <textarea id="message" name="message" rows="6" placeholder="Écrivez votre message ici..." required style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; background: rgba(255,255,255,0.9); transition: all 0.3s ease; resize: vertical; box-sizing: border-box; min-height: 120px;"></textarea>
                                                </div>
                                                <div style="text-align: center;">
                                                    <input type="submit" value="Envoyer le message" style="background: linear-gradient(135deg, #0b66d1, #1e88e5); color: white; border: none; padding: 15px 30px; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(11, 102, 209, 0.3);">
                                                </div>
                                            </fieldset>
                                        </form>
                                    </div>
                                </div>
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
                        <p style="text-align: center;">&copy; 2024 Intelli-Commerce. Tous droits réservés.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>
