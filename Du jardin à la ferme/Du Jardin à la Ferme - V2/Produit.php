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
        ?> Ce produit n'éxiste pas.<?php
    }
    else
    {
        $layout->writeHeader($produit->categorie." / ".$produit->produit);
        ?>
            <div class="produitInfoPanel">
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
                                <span><input type="number" data-inputtype="panier_qte_selector" data-idproduit="<?php $layout->safeWrite($produit->id_produit);?>" value="<?php $layout->safeWrite($produit->quantite_commande);?>" data-max="<?php $layout->safeWrite($produit->quantite_max);?>"/></span>
                            </li>
                        <?php }?>
                    </ul>
                </div>
            </div>
            <?php
                if($api->estAdmin()) 
                { 
                    ?>
                        <article class="whitePanel contentPanel" data-inputtype="Produit-Description-Editor" contenteditable="true" data-idproduit="<?php $layout->safeWrite($produit->id_produit);?>">
                            <?php echo $produit->description;?>
                        </article>
                    <?php
                } 
                elseif(!empty($produit->description))
                {
                    ?>
                        <article class="whitePanel contentPanel">
                            <?php echo $produit->description;?>
                        </article>
                    <?php 
                }
            ?>
        <?php 
    }
    
    $layout->writeFooter();
});