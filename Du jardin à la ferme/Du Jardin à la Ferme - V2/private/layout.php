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
                },
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
                ]
            ]
        ));
    }

    public function render($template, $data)
    {   
        return $this->m->render($template,$data);
        
    }
    
    public function renderHeader($pageTitle)
    {
        return $this->m->render("{{>header}}",$pageTitle);
    }
    
    public function renderFooter()
    {   
        return $this->m->render('{{>footer}}');
    }
    
    
    /**
     * Summary of writeProduit_nom
     * @param _Produit $produit 
     * @param bool $modifiable 
     */
    public function writeProduit_nom($produit, $modifiable=true)
    {
        if($modifiable)
            echo $this->render("{{>Produit-nom-mod}}",$produit);
        else
            echo $this->render("{{>Produit-nom}}",$produit);
    }
    /**
     * Summary of writeProduit_prix_unitaire_ttc
     * @param _Produit $produit 
     * @param bool $modifiable 
     */
    public function writeProduit_prix_unitaire_ttc($produit, $modifiable=true)
    {
        if($modifiable)
            echo $this->render("{{>Produit-prix_unitaire_ttc-mod}}",$produit);
        else
            echo $this->render("{{>Produit-prix_unitaire_ttc}}",$produit);
    }
    /**
     * Summary of writeProduit_description
     * @param _Produit $produit 
     * @param bool $modifiable 
     */
    public function writeProduit_description( $produit, $modifiable=true)
    {
        if($modifiable)
            echo $this->render("{{>Produit-description-mod}}",$produit);
        else
            echo $this->render("{{>Produit-description}}",$produit);
    }
    /**
     * Summary of writeProduit_nom
     * @param _Produit $produit 
     * @param bool $modifiable 
     */
    public function writeProduit_unite($produit, $modifiable=true)
    {
        if($modifiable)
            echo $this->render("{{>Produit-unite-mod}}",$produit);
        else
            echo $this->render("{{>Produit-unite}}",$produit);
    }
    
    
    /**
     * Summary of writeProduitCommande_quantite_commande
     * @param _ElementCommande|_Produit $produit 
     * @param bool $modifiable 
     */
    public function writeProduitCommande_quantite_commande($produit, $modifiable=true)
    {
        if($modifiable)
            echo $this->render("{{>ElementCommande-quantite_commande-mod}}",$produit);
        else
            echo $this->render("{{>ElementCommande-quantite_commande}}",$produit);
    }
    
    
    
    
    public function safeWrite($text)
    {
        echo htmlspecialchars($text);
    }
    
    public function writeDecimal($prix,$decimal =2,$decimalsep=".")
    {
        $this->safeWrite(number_format ( $prix, $decimal, $decimalsep , ""));
    }
    
    public function writePrix($prix,$decimalsep=".")
    {
        $this->writeDecimal($prix,2,$decimalsep);
        $this->safeWrite(" €");
    }
    
    public function writeHeader($title)
    {
        echo $this->renderHeader((string)$title);
    }
    
    public function writeFooter()
    {
        echo $this->renderFooter();
    }
}