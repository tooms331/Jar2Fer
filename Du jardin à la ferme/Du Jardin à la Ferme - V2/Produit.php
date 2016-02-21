<?php
require_once './private/config.php';
require_once './private/layout.php';
require_once './private/api.php';



API::useAPI(function(API $api){
    $layout=new LAYOUT($api);
    
    $id_produit = (int)$_REQUEST['id_produit'];
    $produit = $api->API_produit_recuperer($id_produit);
    
    echo $layout->renderHeader('Produit');
    
    echo $layout->render('{{> PageProduit}}', $produit);
    
    echo $layout->renderFooter();
});