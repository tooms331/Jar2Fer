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
<div class="whitePanel produitInfoPanel">
    <img src="imgs/produit.jpg" />
    <div>
        <div class="infosdetail">
            <div>
                <div>unitée : </div>
                <div><?php LAYOUT::safeWrite($element->unite);?></div>
            </div>
            <div>
                <div>stock : </div>
                <div><?php LAYOUT::safeWrite($element->stocks_previsionnel);?></div>
            </div>
            <div>
                <div>commande : </div>
                <div>
                    <input type="number" data-inputtype="panier_qte_selector" data-idproduit="<?php LAYOUT::safeWrite($element->id_produit);?>" value="<?php LAYOUT::safeWrite($element->quantite_commande);?>" data-max="<?php LAYOUT::safeWrite($element->quantite_max);?>"/></div>
            </div>
        </div>
    </div>
</div>
    <?php if($api->estAdmin()) { ?>
        <article data-inputtype="Produit-Description-Editor" contenteditable="true" class="whitePanel produitDescriptionPanel" data-idproduit="<?php LAYOUT::safeWrite($element->id_produit);?>">
            <?php echo $element->description;?>
        </article>
    <?php } else {?>
        <article class="whitePanel produitDescriptionPanel">
            <?php echo $element->description;?>
        </article>
    <?php }?>

<?php 
    }
    
    LAYOUT::writeFooter($api);
});