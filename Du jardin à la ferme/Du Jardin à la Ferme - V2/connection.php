<?php
require_once './private/config.php';
require_once './private/layout.php';
require_once './private/api.php';

API::useAPI(function(API $api){
    $layout=new LAYOUT($api);
    
    $layout->CheckAuthentified(function(API $api,LAYOUT $layout){
        $layout->writeHeader("Connection");
    
        ?> Vous êtes maintenant authentifier.<?php
        
        $layout->writeFooter();
    });
});