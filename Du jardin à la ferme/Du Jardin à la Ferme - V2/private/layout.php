<?php
require_once 'config.php';
require_once 'api.php';

class LAYOUT{
    /**
     * @var API
     */
    private $api;
    
    
    
    public function __construct(API $api)
    {
        $this->api = $api;
    }
    
    public function safeWrite($text)
    {
        echo htmlspecialchars($text);
    }
    
    public function writePrix($prix)
    {
        $this->safeWrite(number_format ( $prix, 2, "," , " " )." €");
    }
    
    public function writeHeader($title)
    {
        ?>
            <!DOCTYPE html>
            <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                    <title>Du jardin à la ferme - <?php $this->safeWrite($title) ?></title>

                    <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
                    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        
                    <link rel="stylesheet" type="text/css" href="content.css" />
                    <link rel="stylesheet" type="text/css" href="djalf.css" />

                    <script src="http://code.jquery.com/jquery-1.11.3.js"></script>
                    <script src="http://benalman.com/code/projects/jquery-throttle-debounce/jquery.ba-throttle-debounce.js"></script>
        
                    <script src="ckeditor/ckeditor.js"></script>
                    <script src="ckeditor/adapters/jquery.js"></script>

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
                            <li><a href="Produits.php">Nos Produits</a></li>
                            <?php if($this->api->peutCommander()) { ?>
                                <li><a href="MonPanier.php">Mon Panier</a></li>
                            <?php } ?>
                            <?php if(!$this->api->estAuthentifier()) { ?>
                                <li><a href="Connection.php">Connection</a></li>
                            <?php } else{ ?>
                                <li><a href="Deconnection.php">Déconnection</a></li>
                            <?php } ?>
                        </ul>
                    </nav>
                    <h3><?php $this->safeWrite($title) ?></h3>
                    <div class="Main">
        <?php 
    }
    
    
    public function CheckAuthentified(callable $callback)
    {
        if($this->api->estAuthentifier())
        {
            $callback($this->api, $this);
            return;
        }
        
        $email = (string)$_REQUEST['email'];
        $motdepasse = (string)$_REQUEST['motdepasse'];
        $erreur="";
        
        if(!empty($email) && !empty($motdepasse))
        {
            try
            {
                $this->api->API_compte_authentifier($email, $motdepasse);
                $callback($this->api, $this);
                return;
            }
            catch(Exception $ex)
            {
                $erreur= $ex->getMessage();
            }
        }
        
        $this->writeHeader("Connection");
        
        if($erreur) {
            ?>
                <p><?php $this->safeWrite($erreur); ?></p>
            <?php 
        } 
        ?>
            <form class="whitePanel center" action="<?php $this->safeWrite($_SERVER["PHP_SELF"]); ?>" accept-charset="utf-8" enctype="multipart/form-data">
                <ul class="infosdetail">
                    <li>
                        <label for="email">Email :</label>
                        <input id="email" name="email" type="email" />
                    </li>
                    <li>
                        <label for="motdepasse">Mot de passe :</label>
                        <input id="motdepasse" name="motdepasse" type="password" />
                    </li>
                </ul>
                <br /><br />
                <input type="submit" value="Se connecter" />
            </form>
        <?php    
        $this->writeFooter();
    }
    
    public function writeFooter()
    {
        ?>
                    </div>
                    <footer></footer>
                </body>
            </html>

        <?php 
    }
}