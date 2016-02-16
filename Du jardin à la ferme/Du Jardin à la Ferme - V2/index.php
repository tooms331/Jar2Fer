<?php
require_once './private/config.php';
require_once './private/layout.php';
require_once './private/api.php';



API::useAPI(function(API $api){
    LAYOUT::writeHeader("Accueil", $api);
    
    LAYOUT::writeFooter($api);
});