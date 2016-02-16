<?php
require_once './private/config.php';
require_once './private/layout.php';
require_once './private/api.php';

API::useAPI(function(API $api){
    if($api->estAuthentifier())
    {
        $api->API_compte_deconnecter();
    }
        
    LAYOUT::writeHeader("Déconnection", $api);
    
?> Vous êtes maintenant déconnecter, à bientôt.<?php
        
    LAYOUT::writeFooter($api);
});