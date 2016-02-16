<?php
require_once 'config.php';
require_once 'api.php';

class LAYOUT{
    
    public static function safeWrite($text)
    {
        echo htmlspecialchars($text);
    }
    
    public static function writeHeader($title,API $api)
    {?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Du jardin à la ferme - <?php self::safeWrite($title) ?></title>

        <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <link rel="stylesheet" type="text/css" href="djalf.css" />

        <script src="http://code.jquery.com/jquery-1.11.3.js"></script>
        <script src="http://benalman.com/code/projects/jquery-throttle-debounce/jquery.ba-throttle-debounce.js"></script>
        <script src="client.js.php" type="text/javascript"></script>
        <script src="djalf.js" type="text/javascript"></script>
    </head>
    <body>
        <header>
            <h1>Du jardin à la ferme</h1>
            <h2>Projet de ferme durable</h2>
        </header>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="Produits.php">Boutique</a></li>
                <li><a href="MonPanier.php">Mon Panier</a></li>
                <?php if(!$api->estAuthentifier()) { ?>
                    <li><a href="#">Connection</a></li>
                <?php } elseif ($api->estAdmin()){ ?>
                    <li><a href="#">Administration</a></li>
                <?php } ?>
            </ul>
        </nav>
        <h3><?php self::safeWrite($title) ?></h3>
        <div class="Main">

    <?php }
    
    public static function writeFooter(API $api)
    {?>
        </div>
        <footer></footer>
    </body>
</html>

    <?php }
}