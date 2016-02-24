<?php
require_once('./private/config.php');
require_once('./private/entities.php');
require_once('./private/api.php');
require_once('./private/layout.php');


API::useAPI(function(API $api){
    $layout=new LAYOUT($api);
    
    if($api->estAuthentifier())
    {
        $api->API_compte_deconnecter();
    }
    
    echo $layout->renderHeader('Déconnection');
    
    echo $layout->render('{{> SimpleMessage}}','Vous êtes maintenant déconnecté, à bientôt.');
    
    echo $layout->renderFooter();
});
