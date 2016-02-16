<?php
require_once './private/config.php';
require_once './private/layout.php';
require_once './private/api.php';

API::useAPI(function(API $api){
    LAYOUT::CheckAuthentified($api, function(API $api){
        LAYOUT::writeHeader("Connection", $api);
    
?> Vous êtes maintenant authentifier.<?php
        
        LAYOUT::writeFooter($api);
    });
});