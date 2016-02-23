<?php
require_once './private/config.php';
require_once './private/layout.php';
require_once './private/api.php';
 
API::useAPI(function(API $api){
    $layout=new LAYOUT($api); 
    
    echo $layout->renderHeader("Mon compte");
    
    if(!$api->estAuthentifier())
    {
        echo $layout->render('{{> SimpleMessage}}','Vous devez vous authentifier pour accéder à cette page.');
    }
    else
    {
        $compte = $api->compteConnecte();
        echo $layout->render('{{> CompteDetails}}',$compte);
    }
        
    $layout->renderFooter();
});