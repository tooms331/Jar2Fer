<?php
require_once('./private/config.php');
require_once('./private/entities.php');
require_once('./private/api.php');
require_once('./private/layout.php');

API::useAPI(function(API $api){
    $layout=new LAYOUT($api);
    $erreurauth="";
    $erreurcrea="";
    
    if(!$api->estAuthentifier())
    {   
        $email = (string)$_REQUEST['email'];
        $motdepasse = (string)$_REQUEST['motdepasse'];
        $type = (string)$_REQUEST['type'];
        
        
        if(!empty($email) && !empty($motdepasse))
        {
            if($type=='CREATION')
            {
                $motdepasse_confirmation = (string)$_REQUEST['motdepasse_confirmation'];
                if($motdepasse_confirmation===$motdepasse)
                {
                    try
                    {
                        $api->API_compte_creer($email, $motdepasse);
                        header('location:MonCompte.php');
                        return;
                        
                    }
                    catch(Exception $ex)
                    {
                        $erreurauth= $ex->getMessage();
                    }
                }
                else
                {
                    $erreurcrea= 'Les mots de passe ne correspondent pas!';
                }
            }
            else
            {
                try
                {
                    $api->API_compte_authentifier($email, $motdepasse);
                }
                catch(Exception $ex)
                {
                    $erreurauth= $ex->getMessage();
                }
            }
        }
    }
    
    echo $layout->renderHeader('Connection');
        
    if(!$api->estAuthentifier())
    {   
        echo $layout->render('{{> Connection}}', $erreurauth);
        
        echo $layout->render('{{> CreationCompte}}', $erreurcrea);
    }
    else
    {
        echo $layout->render('{{> SimpleMessage}}','Vous êtes maintenant connecté.');
    }
    
    echo $layout->renderFooter();
});