<?php
require_once('./private/config.php');
require_once('./private/entities.php');
require_once('./private/api.php');
require_once('./private/layout.php');

API::useAPI(function(API $api){
    $layout=new LAYOUT($api);
    
    $panier = $api->API_panier_recuperer();
    $elements = $api->API_commande_lister_elements($panier->id_commande);
    
    
    echo $layout->renderHeader("Mon Panier");
    
    $categorized =$layout->Lookup($elements,'id_categorie',['id_categorie'=>'id_categorie','categorie'=>'categorie']);
        
    echo $layout->render('{{> CommandeDetails}}', [
        'commande'=>$panier,
        'elements'=>$categorized
    ]);
    
    echo $layout->renderFooter();
});