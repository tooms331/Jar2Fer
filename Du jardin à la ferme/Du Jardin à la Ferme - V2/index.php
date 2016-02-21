<?php
require_once './private/config.php';
require_once './private/api.php';
require_once './private/layout.php';

API::useAPI(function(API $api){
    
    $layout=new LAYOUT($api);
    
    echo $layout->renderHeader('Accueil');
    
    echo $layout->render('{{> accueil}}',true);
    
    echo $layout->renderFooter();
});