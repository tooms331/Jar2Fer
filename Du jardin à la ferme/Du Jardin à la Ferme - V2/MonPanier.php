<?php
require_once './private/config.php';
require_once './private/layout.php';
require_once './private/api.php';

API::useAPI(function(API $api){
    
    LAYOUT::writeHeader("Mon Panier", $api);
    
    $elements = $api->API_panier_lister_elements();
    
    if(count($elements)==0)
    {
        ?><p>
            Votre panier est actuelement vide. 
            Consulté la <a href="Produits.php">boutique</a> pour faire vos emplettes!
            Ou bien connectez-vous pour retouver votre panier.
          </p>
        <?php
    }
    else
    {
        ?>
    <div class="whitePanel">
        <p>Votre paniers doit être validè jeudi au plus tards pour être livrer cette semaine.</p>
        <p>Les paniers non validez ne sont pas pris en compte, c'est pour vous comme pour nous l'assurance d'évité un malentendus.</p>
        <p>Les livraisons sont effectué le vendredi et le samedi selon nos capacitées.</p>
    </div>

        <table class="tablepanier">
            <thead>
                <tr>
                    <td>Photo</td>
                    <td>Produit</td>
                    <td>Commande</td>
                </tr>
            </thead>
            <tbody>
                <?php  foreach($elements as $element) {?>
                <tr>
                    <td><img src="imgs/produit.jpg" /></td>
                    <th><a href="Produit.php?id_produit=<?php LAYOUT::safeWrite($element->id_produit);?>"><?php LAYOUT::safeWrite($element->produit);?></a></th>
                    <td>
                        <div class="infosdetail">
                            <div><div>unitée : </div><div><?php LAYOUT::safeWrite($element->unite);?></div></div>
                            <div><div>stock : </div><div><?php LAYOUT::safeWrite($element->quantite_max);?></div></div>
                            <div><div>commande : </div><div><input type="number" data-inputtype="panier_qte_selector" data-idproduit="<?php LAYOUT::safeWrite($element->id_produit);?>" value="<?php LAYOUT::safeWrite($element->quantite_commande);?>" data-max="<?php LAYOUT::safeWrite($element->quantite_max);?>"/></div></div>
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