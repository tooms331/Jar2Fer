<?php
require_once './private/config.php';
require_once './private/layout.php';
require_once './private/api.php';

API::useAPI(function(API $api){
    $layout=new LAYOUT($api);
    
    $layout->writeHeader("Mon Panier");
    
    $panier = $api->API_panier_recuperer();
    
    $elements = $api->API_commande_lister_elements($panier->id_commande);
    
    if(count($elements)==0)
    {
        ?>
            <p class="whitePanel center">
                    Votre panier est actuelement vide. 
                    Consulté la <a href="Produits.php">Nos produits</a> pour faire vos emplettes!
            </p>
        <?php
    }
    else
    {
        ?>
            <section class="whitePanel center">
                <p>Votre paniers doit être validè jeudi au plus tards pour être livrer cette semaine.</p>
                <p>Les paniers non validez ne sont pas pris en compte, c'est pour vous comme pour nous l'assurance d'évité un malentendus.</p>
                <p>Les livraisons sont effectué le vendredi et le samedi selon nos capacitées.</p>
            </section>
        <?php
        $categorie="";
        $FirstProduit=true;
        
        foreach($elements as $element) {
            if($categorie != $element->categorie)
            {
                $categorie = $element->categorie;
                if($FirstProduit)
                {
                    $FirstProduit=false;
                }
                else
                {
                    ?>
                            </tbody>
                        </table>
                    <?php
                }
                
                ?>
                    <h4><?php $layout->safeWrite($element->categorie);?></h4>
                    <table class="tablepanier">
                        <thead>
                            <tr>
                                <td>Photo</td>
                                <td>Produit</td>
                                <td>Commande</td>
                            </tr>
                        </thead>
                        <tbody>
                <?php
            }
            ?>
                <tr>
                    <td><img src="imgs/produit.jpg" /></td>
                    <th class="largecol">
                        <a href="Produit.php?id_produit=<?php $layout->safeWrite($element->id_produit);?>"><?php $layout->safeWrite($element->produit);?></a>
                    </th>
                    <td>
                        <ul class="infosdetail">
                            <li>
                                <span>unitée : </span>
                                <span><?php $layout->safeWrite($element->unite);?></span>
                            </li>
                            <li>
                                <span>tarif : </span>
                                <span><?php $layout->writePrix($element->tarif);?></span>
                            </li>
                            <li>
                                <span>stock : </span>
                                <span data-djalf="Produit_stocks_previsionnel" ><?php $layout->safeWrite($element->stocks_previsionnel);?></span>
                            </li>
                            <li>
                                <span>commande : </span>
                                <span>
                                    <?php $layout->writeProduitCommande_quantite_commande($element);?>
                                </span>
                            </li>
                        </ul>
                    </td>
                </tr>
            <?php
        }
        ?>
                </tbody>
            </table>
        <?php 
    }
    $layout->writeFooter();
});