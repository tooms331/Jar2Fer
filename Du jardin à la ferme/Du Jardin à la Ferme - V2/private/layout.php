<?php
require_once 'config.php';
require_once 'api.php';

class LAYOUT
{   
    /**
     * @var Mustache_Engine
     */
    private $m;
    /**
     * @var API
     */
    private $api;
    
    public function __construct(API $api)
    {
        $this->api = $api;
        
        $this->m = new Mustache_Engine(array(
            'loader'          => new Mustache_Loader_FilesystemLoader(dirname(dirname(__FILE__)) . '/tmplt'),
            'partials_loader' => new Mustache_Loader_FilesystemLoader(dirname(dirname(__FILE__)) . '/tmplt/partials'),
        ));
    }

    public function render($template, $data)
    {   
        return $this->m->render($template,
            [
                'session'=>[
                    'compte'=>$this->api->compteConnecte(),
                    'estAuthentifier'=>$this->api->estAuthentifier(),
                    'estAdministrateur'=>$this->api->estAdmin(),
                    'estDesactive'=>$this->api->estDésactivé(),
                    'estLibreService'=>$this->api->estLibreService(),
                    'estNouveau'=>$this->api->estNouveau(),
                    'estPanier'=>$this->api->estPanier(),
                    'estPremium'=>$this->api->estPremium(),
                    'peutCommander'=>$this->api->peutCommander(),
                    'peutModifierCommande'=>function($template,$helper){
                        $id_commande = $helper->context.last()->id_commande;
                        $res = $this->api->peutModifierCommande($id_commande);
                        return $res?$template:"";
                     },
                    'nePeutModifierCommande'=>function($template,$helper){
                        $id_commande = $helper->context.last()->id_commande;
                        $res = $this->api->peutModifierCommande($id_commande);
                        return $res?"":$template;
                     }
                ],
                'datas'=>$data
            ]
            
        );
        
    }
    
    public function renderHeader($pageTitle)
    {   
        return $this->render('header', $pageTitle);
    }
    
    public function renderFooter()
    {   
        return $this->m->render('footer', true);
    }
    
    /**
     * Summary of writeProduit_nom
     * @param _Produit $produit 
     * @param bool $modifiable 
     */
    public function writeProduit_nom($produit, $modifiable=true)
    {
        $modifiable=(bool)$modifiable;
        if($modifiable && $this->api->estAdmin($produit->id_commande))
        {
            echo '<span><input';
            echo ' type="text"';
            echo ' data-djalf="Produit-produit"';
            echo ' data-id_produit="';$this->safeWrite($produit->id_produit);echo '"';
            echo ' value="';$this->safeWrite($produit->produit);echo '"';
            echo '/></span>';
        }
        else
        {
            echo '<span';
            echo ' data-djalf="Produit-produit"';
            echo ' data-id_produit="';$this->safeWrite($produit->id_produit);echo '">';
            $this->safeWrite($produit->produit);
            echo '</span>';
        }
    }
    /**
     * Summary of writeProduit_tarif
     * @param _Produit $produit 
     * @param bool $modifiable 
     */
    public function writeProduit_tarif($produit, $modifiable=true)
    {
        $modifiable=(bool)$modifiable;
        if($modifiable && $this->api->estAdmin($produit->id_commande))
        {
            echo '<span><input';
            echo ' type="number"';
            echo ' step="0.1"';
            echo ' min="0"';
            echo ' data-djalf="Produit-tarif"';
            echo ' data-id_produit="';$this->safeWrite($produit->id_produit);echo '"';
            echo ' value="';$this->safeWrite($produit->tarif);echo '"/>';
            echo ' €</span>';

        }
        else
        {
            echo '<span';
            echo ' data-djalf="Produit-tarif"';
            echo ' data-id_produit="';$this->safeWrite($produit->id_produit);echo '">';
            $this->safeWrite($produit->tarif);
            echo ' </span> €';
        }
    }
    /**
     * Summary of writeProduit_description
     * @param _Produit $produit 
     * @param bool $modifiable 
     */
    public function writeProduit_description( $produit, $modifiable=true)
    {
        $modifiable=(bool)$modifiable;
        echo '<div class="contentPanel"';
        echo ' data-djalf="Produit-description"';
        if($modifiable && $this->api->estAdmin()){
            echo ' contenteditable="true"'; 
        }
        echo ' data-id_produit="';$this->safeWrite($produit->id_produit);echo '">';
        echo $produit->description;
        echo '</div>';
    }
    /**
     * Summary of writeProduit_nom
     * @param _Produit $produit 
     * @param bool $modifiable 
     */
    public function writeProduit_unite($produit, $modifiable=true)
    {
        $modifiable=(bool)$modifiable;
        if($modifiable && $this->api->estAdmin($produit->id_commande))
        {
            $unites = array(Produit::UNITE_BOUQUET,Produit::UNITE_PIECE,Produit::UNITE_KILOGRAMME);
            echo '<select data-djalf="Produit-unite"';
            echo ' data-id_produit="';$this->safeWrite($produit->id_produit);echo '">';
            foreach($unites as $unite)
            {
                $selected = $unite==$produit->unite?' selected="selected"':'';
                echo '<option value="'.$unite.'"'.$selected.'>'.$unite.'</option>';
            }
            echo '</select>';
        }
        else
        {
            echo '<span';
            echo ' data-djalf="Produit-unite"';
            echo ' data-id_produit="';$this->safeWrite($produit->id_produit);echo '">';
            $this->safeWrite($produit->unite);
            echo '</span>';
        }
    }
    
    
    
    
    /**
     * Summary of writeProduitCommande_quantite_commande
     * @param _ElementCommande|_Produit $produit 
     * @param bool $modifiable 
     */
    public function writeProduitCommande_quantite_commande($produit, $modifiable=true)
    {
        $modifiable=(bool)$modifiable;
        switch($produit->unite)
        {
            case Produit::UNITE_KILOGRAMME:
                $decimals=3;
                $step=0.1;
                break;
            default:
                $decimals=0;
                $step=1;
                break;
        }
        if($modifiable && $this->api->peutModifierCommande($produit->id_commande))
        {
            echo '<span><input';
            echo ' type="number"';
            echo ' data-djalf="ProduitCommande-quantite_commande"';
            echo ' min="0"';
            echo ' step="';$this->safeWrite($step);echo '"';
            echo ' max="';$this->safeWrite($produit->stocks_previsionnel);echo '"';
            echo ' data-decimals="';$this->safeWrite($decimals);echo '"';
            echo ' data-id_element_commande="';$this->safeWrite($produit->id_element_commande);echo '"';
            echo ' data-id_commande="';$this->safeWrite($produit->id_commande);echo '"';
            echo ' data-id_produit="';$this->safeWrite($produit->id_produit);echo '"';
            echo ' value="';$this->safeWrite($produit->quantite_commande);echo '"';
            echo '/></span>';

        }
        else
        {
            echo '<span';
            echo ' data-djalf="ProduitCommande-quantite_commande"';
            echo ' data-id_element_commande="';$this->safeWrite($produit->id_element_commande);echo '"';
            echo ' data-id_commande="';$this->safeWrite($produit->id_commande);echo '"';
            echo ' data-id_produit="';$this->safeWrite($produit->id_produit);echo '">';
            $this->safeWrite($produit->quantite_commande);
            echo '</span>';
        }
    }
    
    
    
    
    public function safeWrite($text)
    {
        echo htmlspecialchars($text);
    }
    
    public function writeDecimal($prix,$decimal =2,$decimalsep=".")
    {
        $this->safeWrite(number_format ( $prix, $decimal, $decimalsep , ""));
    }
    
    public function writePrix($prix,$decimalsep=".")
    {
        $this->writeDecimal($prix,2,$decimalsep);
        $this->safeWrite(" €");
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
                                <li><a href="MonCompte.php">Mon Compte</a></li>
                                <li><a href="Deconnection.php">Déconnection</a></li>
                            <?php } ?>
                        </ul>
                    </nav>
                    <h3><?php $this->safeWrite($title) ?></h3>
                    <div class="Main">
        <?php 
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