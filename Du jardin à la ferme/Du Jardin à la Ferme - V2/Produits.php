<?php
require_once './private/config.php';
require_once './private/layout.php';
require_once './private/api.php';

API::useAPI(function(API $api){
    LAYOUT::writeHeader("Boutique", $api);
    
    $elements = $api->API_produits_lister();
    
    
    if(count($elements)==0)
    {
        ?> Il n'y as aucun produit à vendre pour le moment.<?php
    }
    else
    {
        ?>
        <table class="tableproduits">
            <thead>
                <tr>
                    <td>Produit</td>
                    <td>Photo</td>
                    <td>Commande</td>
                </tr>
            </thead>
            <tbody>
                <?php  foreach($elements as $element) {?>
                <tr>
                    <th><a href="Produit.php?id_produit=<?php LAYOUT::safeWrite($element->id_produit);?>"><?php LAYOUT::safeWrite($element->nom);?></a></th>
                    <td><img src="imgs/produit.jpg" /></td>
                    <td>
                        <div class="infosdetail">
                            <div><div>unitée : </div><div><?php LAYOUT::safeWrite($element->unite);?></div></div>
                            <div><div>stock : </div><div><?php LAYOUT::safeWrite($element->stocks_previsionnel);?></div></div>
                            <div><div>commande : </div><div><input type="number" data-inputtype="panier_qte_selector" data-idproduit="<?php LAYOUT::safeWrite($element->id_produit);?>" value="<?php LAYOUT::safeWrite($element->quantite_commande);?>" data-max="<?php LAYOUT::safeWrite($element->stocks_previsionnel);?>"/></div></div>
                        </div>
                    </td>
                </tr>
                <?php }?>
            </tbody>
        </table>
        <?php 
    }
    
    LAYOUT::writeFooter($api);
});