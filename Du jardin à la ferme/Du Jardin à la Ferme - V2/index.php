<?php
require_once './private/config.php';
require_once './private/layout.php';
require_once './private/api.php';


API::useAPI(function($api){
    $layout=new LAYOUT($api);
    
    $layout->writeHeader("Accueil");
    
    $layout->writeFooter();
});