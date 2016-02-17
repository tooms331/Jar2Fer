<?php
require_once './private/config.php';
require_once './private/layout.php';
require_once './private/api.php';

API::useAPI(function(API $api){
    $layout=new LAYOUT($api);
    
    if($api->estAuthentifier())
    {
        $api->API_compte_deconnecter();
    }
        
    $layout->writeHeader("Déconnection");
    
    ?> Vous êtes maintenant déconnecter, à bientôt.<?php
        
    $layout->writeFooter();
});