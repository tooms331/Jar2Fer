<?php
require_once './private/config.php';
require_once './private/layout.php';
require_once './private/api.php';

API::useAPI(function(API $api){
    $layout=new LAYOUT($api);
    
    $rechercheProduit= ((string)$_REQUEST['rechercheProduit'])?:"";
    $produits = $api->API_produits_lister($rechercheProduit);
    
    echo $layout->renderHeader("Nos Produits");
    
    echo $layout->render("{{>RechercheProduits}}",$rechercheProduit);
    
    
    if(count($produits)==0)
    {
        if(empty($rechercheProduit))
        {
            echo $layout->render("{{>SimpleMessage}}","Il n'y as aucun produit correspondant à votre recherche.");
        }
        else
        {
            echo $layout->render("{{>SimpleMessage}}","Il n'y as aucun produit pour le moment.");
        }
    }
    else if(count($produits)==1)
    {
        echo $layout->render("{{>ProduitDetails}}",$produits[0]);
    }
    else if(count($produits)<=20)
    {
        echo $layout->render("{{>ProduitListDetails}}",$produits);
    }
    else
    {   
        echo $layout->render("{{>ProduitListSimple}}",$produits);
    }
    
    echo $layout->renderFooter();
});