<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel ="stylesheet" href="css/StylePagePrincipale.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css">
    <title>Site Intelli-Commerce</title>
</head>
<body>
    <div id="page" class="site">
        <header>
           <div class="header-top mobile-hide">
            <div class="conteiner">
                <div class="wrapper flexitem">
                    <div class="left">
                        <ul class="flexitem main-links">
                            <li><a href="#">Accueil</a></li>
                            <li><a href="#">À propos</a></li>
                            <li><a href="#">Contact</a></li>
                        </ul>
                    </div>
                    <div class="right">
                        <ul class="flexitem main-links">
                            <li class="main-links">
                               <li><select onchange="if(this.value) window.location.href=this.value">
                                    <option value="" disabled selected>S'inscrire/Se Connecter</option>
                                    <option value="/ConnexionClient">Client</option>
                                    <option value="/ConnexionVendeur">Vendeur</option>
                                 </select>
                               </li> 
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
           </div>
           <div class="header-nav">
            <container>
                <div class="wrapper flexitem">
                    <a href="#" class="trigger desktop-hide"></a>
                    <div class="left flexitem">
                        <div class="logo"><a href="/"><span class="circle"></span>.Boutique</a></div>
                        <nav class="mobile-hide">
                            <ul class="flexitem second-links">
                                <li><a href="{{('/welcome')}}">Accueil</a></li>
                                <li><a href="#">Boutique</a></li>
                                <li class="has-child">
                                    <a href="#">Femme 
                                    <div class="icon-small"><i class="ri-arrow-down-s-line"></i></div>
                                    </a>
                                  <div class="mega">
                                     <div class="container">
                                          <div class="wrapper">
                                             <div class="flexcol">
                                                 <div class="row">
                                                     <h4>Vêtements femme</h4>
                                                     <ul>
                                                         <li><a href="#">Robes</a></li>
                                                         <li><a href="#">Hauts & T-shirts</a></li>
                                                         <li><a href="#">Vestes et manteaux</a></li>
                                                         <li><a href="#">Pantalons & capris</a></li>
                                                         <li><a href="#">Pulls</a></li>
                                                         <li><a href="#">Costumes</a></li>
                                                         <li><a href="#">Sweats à capuche & sweatshirts</a></li>
                                                         <li><a href="#">Pyjamas & peignoirs</a></li>
                                                         <li><a href="#">Shorts</a></li>
                                                         <li><a href="#">Maillots de bain</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="flexcol">
                                                <div class="row">
                                                    <h4>Bijoux</h4>
                                                    <ul>
                                                        <li><a href="#">Accessoires</a></li>
                                                        <li><a href="#">Sacs & pochettes</a></li>
                                                        <li><a href="#">Colliers</a></li>
                                                        <li><a href="#">Bagues</a></li>
                                                        <li><a href="#">Boucles d'oreilles</a></li>
                                                        <li><a href="#">Bracelets</a></li>
                                                        <li><a href="#">Bijoux de corps</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="flexcol">
                                                <div class="row">
                                                    <h4>Beauté</h4>
                                                    <ul>
                                                        <li><a href="#">Accessoires de bain</a></li>
                                                        <li><a href="#">Soins de la peau</a></li>
                                                        <li><a href="#">Kits spa & cadeaux</a></li>
                                                        <li><a href="#">Maquillage & cosmétiques</a></li>
                                                        <li><a href="#">Huiles essentielles</a></li>
                                                        <li><a href="#">Savons & bombes de bain</a></li>
                                                        <li><a href="#">Soins capillaires</a></li>
                                                        <li><a href="#">Masques pour le visage</a></li>
                                                        <li><a href="#">Parfums</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="flexcol">
                                                <div class="row">
                                                    <h4>Meilleures marques</h4>
                                                    <ul class="Women-brands">
                                                        <li><a href="#">Nike</a></li>
                                                        <li><a href="#">Louis Vuitton</a></li>
                                                        <li><a href="#">Chanel</a></li>
                                                        <li><a href="#">Dior</a></li>
                                                        <li><a href="#">Gucci</a></li>
                                                        <li><a href="#">Prada</a></li>
                                                        <li><a href="#">Hermès</a></li>
                                                        <li><a href="#">Rolex</a></li>
                                                        <li><a href="#">Cartier</a></li>
                                                        <li><a href="#">Givenchy</a></li>
                                                        <li><a href="#">Sara</a></li>
                                                        <li><a href="#">H&M</a></li>
                                                    </ul>
                                                    <a href="#" class="view-all">Voir toutes les marques <i class="ri-arrow-right-line"></i></a>
                                                </div>
                                            </div>
                                            <div class="flexcol products">
                                                <div class="row">
                                                    <div class="media">
                                                        
                                                        <div class="thumbnail object-cover">
                                                            <a href="#"><img src="Image1.jpg" alt=""></a>
                                                        </div>
                                                </div>
                                                 <div class="text-content">
                                                    <h4>Les plus recherchés</h4>
                                                    <a href="" class="primary-button">Commander maintenant</a>
                                                 </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                 </div>
                            </li>
                                <li><a href="#">Homme
                                    <div class="icon-small"><i class="ri-arrow-down-s-line"></i></div>
                                </a></li>
                                <li><a href="#">Enfant
                                    <div class="icon-small"><i class="ri-arrow-down-s-line"></i></div>
                                </a></li>
                                <li><a href="#">Sports
                                    <div class="fly-item"><span>Nouveau!</span></div>
                                </a></li>
                            </ul>
                        </nav>
                    </div>
                    <div class="right">
                        <ul class="flexitem second-links">
                            <li class="mobile-hide"><a href="#">
                                <div class="icon-large"><i class="ri-heart-line"></i></div>
                                <div class="fly-item"><span class="item-number">0</span></div>
                            </a></li>
                            <li><a href="#" class="iscart">
                                <div class="icon-large"><i class="ri-shopping-cart-line"></i></div>
                                    <div class="fly-item"><span class="item-number">0</span></div>
                                
                            </a></li>
                            <li><a href="#">
                                <div class="icon-text">
                                    <div class="mini-text">Total</div>
                                    <div class="cart-total">0 FCFA</div>
                                </div>
                            </a></li>  
                        </ul>
                    </div>
                </div>
            </container>
           </div>

           <div class="header-main mobile-hide">
           <div class="conteiner">
             <div class="wrapper flexitem">
                    <div class="left">
                        <div class="dpt-cat">
                            <div class="dpt-head">
                                <div class="main-text">Tous le Departements</div>
                                <div class="mini-text mobile-hide">Total 5000 Produits</div>
                                <a href="#" class="dpt-trigger mobile-hide">
                                    <i class="ri-menu-3-line ri-xl"></i>
                                </a>
                            </div>
                            <div class="dpt-menu">
                                <ul class="second-links">
                                    <li class="has-child beauty">
                                        <a href="#">
                                            <div class="icon-large"><i class="ri-bear-smile-line"></i></div>
                                            Beauté
                                            <div class="icon-small"><i class="ri-arrow-right-s-ligne"></i></div>
                                        </a>
                                        <ul>
                                            <li><a href="#">Maquillage</a></li>
                                            <li><a href="#">Soins de la peau</a></li>
                                            <li><a href="#">Soins capillaires</a></li>
                                            <li><a href="#">Parfums</a></li>
                                            <li><a href="#">Soins des pieds & mains</a></li>
                                            <li><a href="#">Outils & accessoires</a></li>
                                            <li><a href="#">Rasage & épilation</a></li>
                                            <li><a href="#">Soins personnels</a></li>
                                        </ul>
                                    </li>
                                    <li class="has-child electronic">
                                        <a href="#">
                                            <div class="icon-large"><i class="ri-bluetooth-connect-line"></i></div>
                                            Électronique
                                            <div class="icon-small"><i class="ri-arrow-right-s-ligne"></i></div>
                                        </a>
                                    </li>
                                    <li class="has-child fashion">
                                        <a href="#">
                                            <div class="icon-large"><i class="ri-t-shirt-air-line"></i></div>
                                            Mode Femme
                                            <div class="icon-small"><i class="ri-arrow-right-s-ligne"></i></div>
                                        </a>
                                    </li>
                                    <li class="has-child fashion">
                                        <a href="#">
                                            <div class="icon-large"><i class="ri-shirt-line"></i></div>
                                            Mode Homme
                                            
                                        </a>
                                    </li>
                                    <li class="has-child fashion">
                                        <a href="#">
                                            <div class="icon-large"><i class="ri-user-5-line"></i></div>
                                            Mode Fille

                                        </a>
                                    </li>
                                    <li class="has-child fashion">
                                        <a href="#">
                                            <div class="icon-large"><i class="ri-user-6-line"></i></div>
                                            Mode Garçon
                                        </a>
                                    </li>
                                    <li class="has-child fashion">
                                        <a href="#">
                                            <div class="icon-large"><i class="ri-heart-pulse-line"></i></div>
                                            Santé & Maison
                                        </a>
                                    </li>
                                    <li class="has-child homekit">
                                        <a href="#">
                                            <div class="icon-large"><i class="ri-home-8-line"></i></div>
                                            Maison & Cuisine
                                            <div class="icon-small"><i class="ri-arrow-right-s-ligne"></i></div>
                                        </a>
                                    </li>
                                    <li class="has-child fashion">
                                        <a href="#">
                                            <div class="icon-large"><i class="ri-android-line"></i></div>
                                            Fournitures pour animaux
                                        </a>
                                    </li>
                                    <li class="has-child fashion">
                                        <a href="#">
                                            <div class="icon-large"><i class="ri-basketball-line"></i></div>
                                            Sports
                                        </a>
                                    </li>
                                    <li class="has-child fashion">
                                        <a href="#">
                                            <div class="icon-large"><i class="ri-shield-star-line"></i></div>
                                            Meilleures ventes
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="right">
                        <div class="search-box">
                            <form action="" class="search">
                                <span class="icon-large"><i class="ri-search-line"></i></span>
                                <input type="search" placeholder="Rechercher produits..." />
                                <button type="submit">Rechercher</button>
                            </form>
                        </div>
                    </div>
                </div>
           </div> 
           </div>
        </header>
        <main>

        </main>
        <footer>

        </footer>
    </div>

    <script src="script.js"></script>
</body>
</html>