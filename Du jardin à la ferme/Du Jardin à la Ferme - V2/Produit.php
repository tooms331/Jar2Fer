<?php
require_once('./private/config.php');
require_once('./private/entities.php');
require_once('./private/api.php');
require_once('./private/layout.php');

API::useAPI(function(API $api){
    $layout=new LAYOUT($api);
    
    $id_produit=isset($_REQUEST['id_produit'])? (int)$_REQUEST['id_produit']:0;
    
    $produit = $api->API_produit_recuperer($id_produit);
    
    echo $layout->renderHeader('Produit');
    
    echo $layout->render('{{> ProduitDetails}}', $produit);
    
    echo $layout->renderFooter();
});