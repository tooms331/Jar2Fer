<?php
require_once './private/config.php';
require_once './private/layout.php';
require_once './private/api.php';



API::useAPI(function(API $api){
    
    
    $id_produit = (int)$_REQUEST['id_produit'];
    
    $element = $api->API_produit_recuperer($id_produit);
    
    if(!$element)
    {
        LAYOUT::writeHeader("Produit", $api);
        ?> Ce produit n'éxiste pas.<?php
    }
    else
    {
        LAYOUT::writeHeader("Produit : ".$element->nom, $api);
        ?>
            <div class="produitPanel">
                <img src="imgs/produit.jpg" />
                <div class="infosdetail">
                    <div><div>unitée : </div><div><?php LAYOUT::safeWrite($element->unite);?></div></div>
                    <div><div>stock : </div><div><?php LAYOUT::safeWrite($element->quantite_max);?></div></div>
                    <div><div>commande : </div><div><input type="number" data-inputtype="panier_qte_selector" data-idproduit="<?php LAYOUT::safeWrite($element->id_produit);?>" value="<?php LAYOUT::safeWrite($element->quantite_commande);?>" data-max="<?php LAYOUT::safeWrite($element->quantite_max);?>"/></div></div>
                </div>
                <p><?php LAYOUT::safeWrite($element->description);?></p>
            </div>
        <?php 
    }
    
    LAYOUT::writeFooter($api);
});