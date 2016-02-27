<?php
require_once('./private/config.php');
require_once('./private/entities.php');
require_once('./private/api.php');
require_once('./private/layout.php');

API::useAPI(function(API $api){
    
    $layout=new LAYOUT($api); 
    
    echo $layout->renderHeader("Gestion des commandes");
    
    if(!$api->estAuthentifier())
    {
        echo $layout->render('{{> SimpleMessage}}','Vous devez vous authentifier pour accéder à cette page.');
    }
    else 
    if(!$api->estAdmin())
    {
        echo $layout->render('{{> SimpleMessage}}','Vous devez être un administrateur pour accéder à cette page.');
    }
    else
    {
        $commandes = $api->API_commande_lister(null);
        $commandes = $layout->Lookup($commandes,'etat',['etat'=>'etat']);
        echo $layout->render('{{> CommandeList}}',['commandes'=>$commandes]);
    }
        
    $layout->renderFooter();
});