<?php
require_once('./private/config.php');
require_once('./private/entities.php');
require_once('./private/api.php');
require_once('./private/layout.php');

API::useAPI(function(API $api){
    $layout=new LAYOUT($api);
    
    $rechercheProduit=isset($_REQUEST['rechercheProduit'])? (string)$_REQUEST['rechercheProduit']:"";
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
        $categorized =$layout->Lookup($produits,'id_categorie',['id_categorie'=>'id_categorie','categorie'=>'categorie']);
        echo $layout->render("{{>ProduitListDetails}}",$categorized);
    }
    else
    {   
        $categorized =$layout->Lookup($produits,'id_categorie',['id_categorie'=>'id_categorie','categorie'=>'categorie']);
        echo $layout->render("{{>ProduitListSimple}}",$categorized);
    }
    
    echo $layout->renderFooter();
});