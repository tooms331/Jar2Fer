<?php
require_once './private/config.php';
require_once './private/layout.php';
require_once './private/api.php';



API::useAPI(function(API $api){
    $layout=new LAYOUT($api);
    
    $id_produit = (int)$_REQUEST['id_produit'];
    $produit = $api->API_produit_recuperer($id_produit);
    
    
    if(!$produit)
    {
        $layout->writeHeader("Produit");
        ?> 
            <p class="whitePanel center">
                Ce produit n'éxiste pas.
            </p>
        <?php
    }
    else
    {
        $layout->writeHeader($produit->categorie." / ".$produit->produit);
        ?>
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
                            <span><?php $layout->safeWrite($produit->produit);?></span>
                        </li>
                        <?php if($api->peutCommander()) {?> 
                            <li>
                                <span>unitée : </span>
                                <span><?php $layout->safeWrite($produit->unite);?></span>
                            </li>
                            <li>
                                <span>tarif : </span>
                                <span><?php $layout->writePrix($produit->tarif);?></span>
                            </li>
                            <li>
                                <span>stock : </span>
                                <span><?php $layout->safeWrite($produit->stocks_previsionnel);?></span>
                            </li>
                            <li>
                                <span>commande : </span>
                                <span>
                                    <input 
                                        type="number" 
                                        data-djalf="ProduitCommande-quantite_commande" 
                                            data-id_element_commande="<?php $layout->safeWrite($produit->id_element_commande);?>" 
                                            data-id_commande="<?php $layout->safeWrite($produit->id_commande);?>" 
                                            data-id_produit="<?php $layout->safeWrite($produit->id_produit);?>" 
                                        value="<?php $layout->safeWrite($produit->quantite_commande);?>"/>
                                </span>
                            </li>
                        <?php }?>
                    </ul>
                </div>
            </section>
            <?php
                if($api->estAdmin()) 
                { 
                    ?>
                        <section class="whitePanel contentPanel" data-djalf="Produit-description" contenteditable="true" data-id_produit="<?php $layout->safeWrite($produit->id_produit);?>">
                            <?php echo $produit->description;?>
                        </section>
                    <?php
                } 
                elseif(!empty($produit->description))
                {
                    ?>
                        <section class="whitePanel contentPanel" data-djalf="Produit-description">
                            <?php echo $produit->description;?>
                        </section>
                    <?php 
                }
            ?>
        <?php 
    }
    
    $layout->writeFooter();
});