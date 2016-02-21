<?php
require_once './private/config.php';
require_once './private/layout.php';
require_once './private/api.php';

API::useAPI(function(API $api){
    $layout=new LAYOUT($api);
    
    $panier = $api->API_panier_recuperer();
    $elements = $api->API_commande_lister_elements($panier->id_commande);
    
    
    echo $layout->renderHeader("Mon Panier");
    
    echo $layout->render('{{> CommandeDetails}}', [
        'commande'=>$panier,
        'elements'=>$elements
    ]);
    
    echo $layout->renderFooter();
});