<?php
require_once './private/config.php';
require_once './private/layout.php';
require_once './private/api.php';

API::useAPI(function(API $api){
    $layout=new LAYOUT($api);
    $layout->writeHeader("Nos Produits");
    
    $rechercheProduit= ((string)$_REQUEST['rechercheProduit'])?:"";
    
    if(empty($rechercheProduit))
    {
        $produits = $api->API_produits_lister("");
        
        if(count($produits)==0)
        {
            ?>
                <p class="whitePanel"> 
                    Il n'y as aucun produit pour le moment.
                </p>
            <?php
        }
        else
        {
            ?>
                <form class="whitePanel center" action="<?php $layout->safeWrite($_SERVER["PHP_SELF"]); ?>" accept-charset="utf-8" enctype="application/x-www-form-urlencoded">
                    <input type="text" name="rechercheProduit" value="<?php $layout->safeWrite($rechercheProduit); ?>" />
                    <input type="submit" value="Rechercher" />
                </form>
                
            <?php
            
        
            $categorie="";
            $FirstProduit=true;
        
            foreach($produits as $produit) {
                if($categorie != $produit->categorie)
                {
                    $categorie = $produit->categorie;
                    if($FirstProduit)
                    {
                        $FirstProduit=false;
                    }
                    else
                    {
                        ?>
                                </ul>
                            </div>
                        <?php
                    }
                
                    ?>
                        <div class="whitePanel">
                            <h5><?php $layout->safeWrite($produit->categorie);?></h5>
                            <ul>
                    <?php
                }
                ?>
                    <li><a href="Produit.php?id_produit=<?php $layout->safeWrite($produit->id_produit);?>"><?php $layout->safeWrite($produit->produit);?></a></li>
                <?php
            }
            ?>
                    </ul>
                </div>
            <?php 
        }
    }
    else
    {
        $produits = $api->API_produits_lister($rechercheProduit);
        
        if(count($produits)==0)
        {
            ?>
                <div class="whitePanel center">
                    Il n'y as aucun produit correspondant à votre recherche.
                </div>
            <?php
        }
        else
        {
            
            ?>
                <form class="whitePanel center" action="<?php $layout->safeWrite($_SERVER["PHP_SELF"]); ?>" accept-charset="utf-8" enctype="application/x-www-form-urlencoded">
                    <input type="text" name="rechercheProduit" value="<?php $layout->safeWrite($rechercheProduit); ?>" />
                    <input type="submit" value="Rechercher" />
                </form>
            <?php
            
        
            $categorie="";
            $FirstProduit=true;
        
            foreach($produits as $produit) {
                if($categorie != $produit->categorie)
                {
                    $categorie = $produit->categorie;
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
                        <h4><?php $layout->safeWrite($produit->categorie);?></h4>
                        <table class="tableproduits">
                            <thead>
                                <tr>
                                    <td>Variété</td>
                                    <td>Photo</td>
                                    <?php if($api->peutCommander()) {?> 
                                    <td>Commande</td>
                                    <?php }?>
                                </tr>
                            </thead>
                            <tbody>
                    <?php
                }
                ?>
                    <tr>
                        <th><a href="Produit.php?id_produit=<?php $layout->safeWrite($produit->id_produit);?>"><?php $layout->safeWrite($produit->produit);?></a></th>
                        <td><img src="imgs/produit.jpg" /></td>
                        <?php if($api->peutCommander()) {?> 
                        <td>
                            <ul class="infosdetail">
                                <li>
                                    <span>unitée : </span>
                                    <span><?php $layout->safeWrite($produit->unite);?></span>
                                </li>
                                <li>
                                    <span>tarif : </span>
                                    <span><?php $layout->writePrix($produit->tarif);?></span>
                                </li>
                                <li><span>stock : </span>
                                    <span><?php $layout->safeWrite($produit->stocks_previsionnel);?></span>
                                </li>
                                <li>
                                    <span>commande : </span>
                                    <span>
                                        <?php $layout->writeProduitCommande_quantite_commande($produit);?>
                                    </span>
                                </li>
                            </ul>
                        </td>
                        <?php }?>
                    </tr>
                <?php
            }
            ?>
                    </tbody>
                </table>
            <?php 
        }
    }
    $layout->writeFooter();
});