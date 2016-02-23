<?php
require_once 'config.php';
require_once 'api.php';

class LAYOUT
{   
    /**
     * @var Mustache_Engine
     */
    private $m;
    /**
     * @var API
     */
    private $api;
    
    public function __construct(API $api)
    {
        $this->api = $api;
        
        $this->m = new Mustache_Engine(array(
            'partials_loader' => new Mustache_Loader_FilesystemLoader(dirname(dirname(__FILE__)) . '/tmplt'),
            'pragmas'=>[Mustache_Engine::PRAGMA_FILTERS],
            'helpers'=>[
                'render'=>function($template, Mustache_LambdaHelper $lambdaHelper){
                    $context = $lambdaHelper->getContext();
                    $context->push($context->last()['data']);
                    return $template;
                },
                '?'=>function($value){
                    return !!$value;
                },
                '!'=>function($value){
                    return !$value;
                }
            ]
        ));
    }

    public function render($template, $data=true)
    {   
        $data=$this->createView($data);
        return $this->m->render("{{# render}}$template{{/ render}}",[
            'session'=>[
                'compte'=>$this->api->compteConnecte(),
                'estAuthentifier'=>$this->api->estAuthentifier(),
                'estAdministrateur'=>$this->api->estAdmin(),
                'estDesactive'=>$this->api->estDésactivé(),
                'estLibreService'=>$this->api->estLibreService(),
                'estNouveau'=>$this->api->estNouveau(),
                'estPanier'=>$this->api->estPanier(),
                'estPremium'=>$this->api->estPremium(),
                'peutCommander'=>$this->api->peutCommander(),
                'peutModifierCommande'=>function($value){
                    return $this->api->peutModifierCommande($value);
                }
            ],
            'data'=>$data
        ]);
        
    }
    
    public function renderHeader($pageTitle)
    {
        return $this->render("{{>header}}",$pageTitle);
    }
    
    public function renderFooter()
    {   
        return $this->render('{{>footer}}');
    }
    
    private function createView($value)
    {
        switch(gettype($value))
        {
            case "array":
                $arr = array_map(function($item){
                    return $this->createView($item);
                },$value);
                return $arr;
            case "object":
                return $this->createViewForObject($value);
            case "boolean":
            case "integer":
            case "double":
            case "string":
            case "NULL":
            case "resource":
            case "unknown type":
            default:
                return $value;
        }
    }
    private function createViewForObject($value)
    {
        $view = [];
        if($value instanceof Entity)
        {
            foreach(class_uses($value) as $trait)
            {
                switch ($trait)
                {
                    case"_CompteBase":
                        $this->createViewFor_CompteBase($value, $view);
                        break;
                    case"_CompteDetail":
                        $this->createViewFor_CompteDetail($value, $view);
                        break;
                    case"_Categorie":
                        $this->createViewFor_Categorie($value, $view);
                        break;
                    case"_Produit":
                        $this->createViewFor_Produit($value, $view);
                        break;
                    case"_ElementCommande":
                        $this->createViewFor_ElementCommande($value, $view);
                        break;
                    case"_Commande":
                        $this->createViewFor_Commande($value, $view);
                        break;
                }
            }
        }
        else
        {
            foreach(get_object_vars($value) as $property => $propertyvalue)
            {
                $view[$property]=$this->createView($propertyvalue);
            }
        }
        return $view;
    }
    private function createViewFor_CompteBase($value, &$view)
    {
        $view['id_compte'] = $value->id_compte;
        $view['email'] = $value->email;
    }
    private function createViewFor_CompteDetail($value, &$view)
    {
        $this->createViewFor_CompteBase($value,$view);
        $view['statut'] = $value->statut;
        $view['demande_statut'] = $value->demande_statut;
        $view['date_creation_compte'] = $value->date_creation_compte;
    }
    private function createViewFor_Categorie($value, &$view)
    {
        $view['id_categorie'] = $value->id_categorie;
        $view['categorie'] = $value->categorie;
    }
    private function createViewFor_Produit($value, &$view)
    {
        $view['id_produit'] = $value->id_produit;
        $view['id_categorie'] = $value->id_categorie;
        $view['produit'] = $value->produit;
        $view['description'] = $value->description;
        $view['prix_unitaire_ttc'] =  number_format($value->prix_unitaire_ttc,2,".","");
        $view['tva'] = number_format($value->tva,2,".","");;
        $view['unite'] = $value->unite;
        $view['stocks_previsionnel'] = number_format($value->stocks_previsionnel,$value->unite_decimals,".","");
        $view['stocks_courant'] = number_format($value->stocks_courant,$value->unite_decimals,".","");
        $view['unite_decimals'] = $value->unite_decimals;
        $view['unite_step'] = $value->unite_step;
        
        $view['uniteOptions'] = array_map(function($item)use($value){
            return [ 'value'=>$item,'display'=>$item,'selected'=>$item==$value->unite];
        },[Produit::UNITE_BOUQUET,Produit::UNITE_PIECE,Produit::UNITE_KILOGRAMME]);
    }
    private function createViewFor_ElementCommande($value, &$view)
    {
        $this->createViewFor_Produit($value,$view);
        $view['id_produit'] = $value->id_produit;
        $view['id_commande'] = $value->id_commande;
        $view['quantite_commande'] = number_format($value->quantite_commande,$value->unite_decimals,".","");
        $view['quantite_reel'] = isset($value->quantite_commande)?number_format($value->quantite_commande,$value->unite_decimals,".",""):null;
        $view['prix_total_element_ttc'] = number_format($value->prix_total_element_ttc,2,".","");
        $view['prix_total_element_ht'] = number_format($value->prix_total_element_ht,2,".","");
        $view['tva_total_element'] = number_format($value->tva_total_element,2,".","");
    }
    private function createViewFor_Commande($value, &$view)
    {
        $this->createViewFor_CompteBase($value,$view);
        $view['id_compte'] = $value->id_compte;
        $view['id_commande'] = $value->id_commande;
        $view['date_creation_commande'] = $value->date_creation_commande;
        $view['remarque'] = $value->remarque;
        $view['etat'] = $value->etat;
        $view['nb_elements'] = $value->nb_elements;
        $view['prix_total_commande_ttc'] = number_format($value->prix_total_commande_ttc,2,".","");
        $view['prix_total_commande_ht'] = number_format($value->prix_total_commande_ht,2,".","");
        $view['tva_total_commande'] = number_format($value->tva_total_commande,2,".","");
    }
    public function Lookup_categories($values){
        $buff = [];
        foreach($values as $value)
        {
            $categorie=$value->categorie;
            $id_categorie=$value->id_categorie;
            if(!isset($buff[$categorie]))
                $buff[$categorie]=['categorie'=>$categorie, 'id_categorie'=>$id_categorie, 'values'=>[$value]];
            else
                $buff[$categorie]['values'][]=$value;
        }
        foreach($buff as $value)
        {
            uasort($value['values'],function($a,$b){ return strnatcmp($a->produit,$b->produit);});
        }
        $buff = array_values($buff);
        uasort($buff,function($a,$b){ return strnatcmp($a['categorie'],$b['categorie']);});
        return $buff;
    }
}