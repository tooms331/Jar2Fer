<?php

function setNtype(&$ref,$type)
{
    if($ref!==null)
        settype($ref,$type);
}

trait _CompteBase
{
    /**
     * @var int
     */
    public $id_compte;
    /**
     * @var string
     */
    public $email;
    
    protected function _CompteBaseInit(){
        setNtype($this->id_compte,'int');
        setNtype($this->email,'string');
    }
}

trait _CompteDetail
{
    use _CompteBase;
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
    public $date_creation_compte;
    
    protected function _CompteInit(){
        $this->_CompteBaseInit();
        setNtype($this->statut,'string');
        setNtype($this->demande_statut,'string');
        setNtype($this->date_creation_compte,'string');
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

    protected function _CategorieInit(){
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
    public $tva;
    public $prix_unitaire_ttc;
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

    protected function _ProduitInit(){
        setNtype($this->id_produit,'int');
        setNtype($this->id_categorie,'int');
        setNtype($this->produit,'string');
        setNtype($this->description,'string');
        setNtype($this->prix_unitaire_ttc,'double');
        setNtype($this->unite,'string');
        setNtype($this->stocks_previsionnel,'double');
        setNtype($this->stocks_courant,'double');
        setNtype($this->tva,'double');
        
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
    use _Produit;
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
    /**
     * @var double
     */
    public $prix_total_element_ttc;
    /**
     * @var double
     */
    public $prix_total_element_ht;
    /**
     * @var double
     */
    public $tva_total_element;
    
    protected function _ElementCommandeInit(){
        $this->_ProduitInit();
        setNtype($this->id_produit,'int');
        setNtype($this->id_commande,'int');
        setNtype($this->quantite_commande,'double');
        setNtype($this->quantite_reel,'double');
        setNtype($this->prix_total_element_ttc,'double');
        setNtype($this->prix_total_element_ht,'double');
        setNtype($this->tva_total_element,'double');
    }
}

trait _Commande
{
    use _CompteBase;
    /**
     * @var int
     */
    public $id_commande;
    /**
     * @var string
     */
    public $date_creation_commande;
    /**
     * @var string
     */
    public $remarque;
    /**
     * @var string
     */
    public $etat;
    /**
     * @var int
     */
    public $nb_elements;
    /**
     * @var double
     */
    public $prix_total_commande_ttc;
    /**
     * @var double
     */
    public $prix_total_commande_ht;
    /**
     * @var double
     */
    public $tva_total_commande;
    
    protected function _CommandeInit(){
        $this->_CompteBaseInit();
        setNtype($this->id_commande,'int');
        setNtype($this->id_compte,'int');
        setNtype($this->date_creation_commande,'string');
        setNtype($this->remarque,'string');
        setNtype($this->etat,'string');
        setNtype($this->nb_elements,'int');
        setNtype($this->prix_total_commande_ttc,'double');
        setNtype($this->prix_total_commande_ht,'double');
        setNtype($this->tva_total_commande,'double');
    }
}

class Entity{
}

class Compte extends Entity
{
    use _CompteDetail;
    
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

class Categorie extends Entity
{
    use _Categorie;

    public function __construct(){
        $this->_CategorieInit();
    }
}

class Produit extends Entity
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

class ProduitCommande extends Entity
{
    use _Categorie, _ElementCommande;
    
    public function __construct(){
        $this->_CategorieInit();
        $this->_ElementCommandeInit();
    }
}

class ProduitCommandeDetail extends Entity
{
    use _Categorie, _ElementCommande, _Commande;
    
    public function __construct(){
        $this->_CategorieInit();
        $this->_ElementCommandeInit();
        $this->_CommandeInit();
    }
}

class Commande extends Entity
{
    use _Commande;
    
    const ETAT_CREATION = 'Création';
    const ETAT_VALIDE = 'Validé';
    const ETAT_PREPARATION = 'Préparation';
    const ETAT_LIVRAISON = 'Livraison';
    const ETAT_TERMINE = 'Terminé';
    
    public function __construct(){
        $this->_CommandeInit();
    }
}
