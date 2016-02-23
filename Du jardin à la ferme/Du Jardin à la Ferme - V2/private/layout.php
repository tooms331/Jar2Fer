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
                'equalParent'=>function($template, Mustache_LambdaHelper $lambdaHelper){
                    $context = $lambdaHelper->getContext();
                    $lastContext = $context->pop();
                    $valueExists = $context->last()==$lastContext;
                    $context->push($lastContext);
                    return $valueExists?$template:"";
                },
                '?'=>function($value){
                    return !!$value;
                },
                'Lookup_categorie'=>function($values){
                    $buff = [];
                    foreach($values as $value)
                    {
                        $categorie=$value->categorie;
                        if(!isset($buff[$categorie]))
                            $buff[$categorie]=['categorie'=>$categorie,'values'=>[$value]];
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
                },
                '!'=>function($value){
                    return !$value;
                }
            ]
        ));
    }

    public function render($template, $data=true)
    {   
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
            'globals'=>[
                'unites' => array(Produit::UNITE_BOUQUET,Produit::UNITE_PIECE,Produit::UNITE_KILOGRAMME)
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
}