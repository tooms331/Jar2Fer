<?php
require_once('./private/config.php');
require_once('./private/entities.php');
require_once('./private/api.php');
require_once('./private/layout.php');

API::useAPI(function(API $api){
    
    $layout=new LAYOUT($api); 
    
    echo $layout->renderHeader("Mon compte");
    
    if(!$api->estAuthentifier())
    {
        echo $layout->render('{{> SimpleMessage}}','Vous devez vous authentifier pour accéder à cette page.');
    }
    else
    {
        $erreur_change_password="";
        $erreur_demande_statut="";
        $erreur_change_adresses="";
    
        $form_type=(string)$_REQUEST['type'];
        switch($form_type)
        {
            case "Demande-Statut":
                try
                {       
                }
                catch(ErrorException $ex)
                {
                    $erreur_demande_statut=$ex->getMessage();
                }
                break;
            
            case "Change-Password":
                try
                {
                    $form_motdepasse = (string)$_REQUEST['motdepasse'];
                    $form_motdepasse_nouveau = (string)$_REQUEST['motdepasse_nouveau'];
                    $form_motdepasse_nouveau_confirmation = (string)$_REQUEST['motdepasse_nouveau_confirmation'];
                    if($form_motdepasse_nouveau!==$form_motdepasse_nouveau_confirmation)
                        throw new ErrorException("Les mots de passes saisie ne se correspondent pas!");
                    $api->API_compte_modifier_mot_de_passe($form_motdepasse,$form_motdepasse_nouveau);
                }
                catch(ErrorException $ex)
                {
                    $erreur_change_password=$ex->getMessage();
                }
                break;
            
            case "Change-Adresses":
                try
                {
                    $form_titre_facturation = (string)$_REQUEST['titre_facturation'];
                    $form_prenom_facturation = (string)$_REQUEST['prenom_facturation'];
                    $form_nom_facturation = (string)$_REQUEST['nom_facturation'];
                    $form_adresse_A_facturation = (string)$_REQUEST['adresse_A_facturation'];
                    $form_adresse_B_facturation = (string)$_REQUEST['adresse_B_facturation'];
                    $form_codepostal_facturation = (string)$_REQUEST['codepostal_facturation'];
                    $form_ville_facturation = (string)$_REQUEST['ville_facturation'];
                
                    $form_titre_livraison = (string)$_REQUEST['titre_livraison'];
                    $form_prenom_livraison = (string)$_REQUEST['prenom_livraison'];
                    $form_nom_livraison = (string)$_REQUEST['nom_livraison'];
                    $form_adresse_A_livraison = (string)$_REQUEST['adresse_A_livraison'];
                    $form_adresse_B_livraison = (string)$_REQUEST['adresse_B_livraison'];
                    $form_codepostal_livraison = (string)$_REQUEST['codepostal_livraison'];
                    $form_ville_livraison = (string)$_REQUEST['ville_livraison'];
                }
                catch(ErrorException $ex)
                {
                    $erreur_change_adresses=$ex->getMessage();
                }
                break;
            
            default:
                break;
        }
    
        $compte = $api->compteConnecte();
        echo $layout->render('{{> CompteDetails}}',[
            'compte'=>$compte,
            'erreur_change_password'=>$erreur_change_password,
            'erreur_demande_statut'=>$erreur_demande_statut,
            'erreur_change_adresses'=>$erreur_change_adresses
        ]);
    }
        
    $layout->renderFooter();
});