<?php

function setNtype(&$ref,$type)
{
    if($ref!==null)
        settype($ref,$type);
}

trait _Compte
{
    /**
     * @var int
     */
    public $id_compte;
    /**
     * @var string
     */
    public $statut;
    /**
     * @var string
     */
    public $demande_statut;
    /**
     * @var string
     */
    public $email;
    /**
     * @var string
     */
    public $date_creation;
    
    protected function _CompteInit(){
        setNtype($this->id_compte,'int');
        setNtype($this->statut,'string');
        setNtype($this->demande_statut,'string');
        setNtype($this->email,'string');
        setNtype($this->date_creation,'string');
    }
}

trait _Categorie{
    /**
     * @var int
     */
    public $id_categorie;
    /** 
     * @var string
     */
    public $categorie;

    public function _CategorieInit(){
        setNtype($this->id_categorie,'int');
        setNtype($this->categorie,'string');
    }
}

trait _Produit
{   
    public $id_produit;
    public $id_categorie;
    public $produit;
    public $description;
    public $tarif;
    public $unite;
    public $stocks_previsionnel;
    public $stocks_courant;
    /**
     * @var int
     */
    public $unite_decimals=0;
    /**
     * @var int
     */
    public $unite_step=1;

    public function _ProduitInit(){
        setNtype($this->id_produit,'int');
        setNtype($this->id_categorie,'int');
        setNtype($this->produit,'string');
        setNtype($this->description,'string');
        setNtype($this->tarif,'double');
        setNtype($this->unite,'string');
        setNtype($this->stocks_previsionnel,'double');
        setNtype($this->stocks_courant,'double');
        switch($this->unite)
        {
            case Produit::UNITE_KILOGRAMME:
                $this->unite_decimals=3;
                $this->unite_step=0.1;
                break;
        }
    }
}

trait _ElementCommande
{
    /**
     * @var int
     */
    public $id_element_commande;
    /**
     * @var int
     */
    public $id_commande;
    /**
     * @var int
     */
    public $id_produit;
    /**
     * @var double
     */
    public $quantite_commande;
    /**
     * @var double
     */
    public $quantite_reel;
    
    public function _ElementCommandeInit(){
        setNtype($this->id_element_commande,'int');
        setNtype($this->id_produit,'int');
        setNtype($this->id_commande,'int');
        setNtype($this->quantite_commande,'double');
        setNtype($this->quantite_reel,'double');
    }
}

trait _Commande
{
    /**
     * @var int
     */
    public $id_commande;
    /**
     * @var int
     */
    public $id_compte;
    /**
     * @var string
     */
    public $date_creation;
    /**
     * @var string
     */
    public $remarque;
    /**
     * @var string
     */
    public $etat;
    
    public function _CommandeInit(){
        setNtype($this->id_commande,'int');
        setNtype($this->id_compte,'int');
        setNtype($this->date_creation,'string');
        setNtype($this->remarque,'string');
        setNtype($this->etat,'string');
    }
}

trait _VariationStock
{
    /**
     * @var int
     */
    public $id_variation_stock;
    /** 
     * @var int
     */
    public $id_produit;
    /**
     * @var string
     */ 
    public $date_variation;
    /**
     * @var double
     */
    public $variation;
    /**
     * @var string
     */
    public $type_variation;
    /**
     * @var string
     */
    public $remarque;
    
    public function _VariationStockInit(){
        setNtype($this->id_variation_stock,'int');
        setNtype($this->id_produit,'int');
        setNtype($this->date_variation,'string');
        setNtype($this->variation,'double');
        setNtype($this->type_variation,'string');
        setNtype($this->remarque,'string');
    }
}

class Compte
{
    use _Compte;
    
    const STATUT_Nouveau='Nouveau';
    const STATUT_Panier='Panier';
    const STATUT_Premium='Premium';
    const STATUT_LibreService='Libre Service';
    const STATUT_Admin='Admin';
    const STATUT_Desactive='Désactivé';
    
    public function __construct(){
        $this->_CompteInit();
    }
}

class Categorie
{
    use _Categorie;

    public function __construct(){
        $this->_CategorieInit();
    }
}

class Produit
{
    use _Categorie, _Produit;
    
    const UNITE_PIECE = 'Pièce';
    const UNITE_BOUQUET = 'Bouquet';
    const UNITE_KILOGRAMME = 'Kilogramme';
    
    public function __construct(){
        $this->_CategorieInit();
        $this->_ProduitInit();
    }
}

class ProduitCommande
{
    use _Categorie, _Produit, _ElementCommande;
    
    public function __construct(){
        $this->_CategorieInit();
        $this->_ProduitInit();
        $this->_ElementCommandeInit();
    }
}

class Commande
{
    use _Compte, _Commande;
    
    const ETAT_CREATION = 'Création';
    const ETAT_VALIDE = 'Validé';
    const ETAT_PREPARATION = 'Préparation';
    const ETAT_LIVRAISON = 'Livraison';
    const ETAT_TERMINE = 'Terminé';
    
    public function __construct(){
        $this->_CompteInit();
        $this->_CommandeInit();
    }
}

class VariationStock
{
    use _VariationStock;
    
    const TYPE_ACHAT = 'ACHAT';
    const TYPE_VENTE = 'VENTE';
    const TYPE_PERTE = 'PERTE';
    const TYPE_AUTRE = 'AUTRE';
    const TYPE_RECOLTE = 'RECOLTE';
    
    public function __construct(){
        $this->_VariationStockInit();
    }
}