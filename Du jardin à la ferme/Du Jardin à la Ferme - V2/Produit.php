<?php
require_once './private/config.php';
require_once './private/layout.php';
require_once './private/api.php';



API::useAPI(function(API $api){
    $layout=new LAYOUT($api);
    
    $id_produit = (int)$_REQUEST['id_produit'];
    $produit = $api->API_produit_recuperer($id_produit);
    
    $layout->writeHeader("Produit");
        
    if(!$produit)
    {
        ?> 
            <p class="whitePanel center">
                Ce produit n'éxiste pas.
            </p>
        <?php
    }
    else
    {
        ?>
            <h4><?php $layout->safeWrite($produit->categorie);?> / <?php $layout->writeProduit_nom($produit,false);?></h4>

            <section class="produitInfoPanel">
                <div class="whitePanel">
                    <img src="imgs/produit.jpg" />
                </div>
                <div class="spacer">&nbsp;</div>
                <div class="whitePanel">
                    <ul class="infosdetail">
                        <li>
                            <span>catégorie : </span>
                            <span><?php $layout->safeWrite($produit->categorie);?></span>
                        </li>
                        <li>
                            <span>produit : </span>
                            <?php $layout->writeProduit_nom($produit);?>
                        </li>
                        <?php if($api->peutCommander()) {?> 
                            <li>
                                <span>unitée : </span>
                                <span><?php $layout->safeWrite($produit->unite);?></span>
                            </li>
                            <li>
                                <span>tarif : </span>
                                <?php $layout->writeProduit_tarif($produit);?>
                            </li>
                            <li>
                                <span>stock : </span>
                                <span><?php $layout->safeWrite($produit->stocks_previsionnel);?></span>
                            </li>
                            <li>
                                <span>commande : </span>
                                <?php $layout->writeProduitCommande_quantite_commande($produit);?>
                            </li>
                        <?php }?>
                    </ul>
                </div>
            </section>
            <?php
                if(!empty($produit->description)||$api->estAdmin()) 
                { 
                    ?>
                        <section class="whitePanel">
                            <?php $layout->writeProduit_description($produit);?>
                        </section>
                    <?php
                } 
            ?>
        <?php 
    }
    
    $layout->writeFooter();
});