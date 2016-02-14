<?php
class Compte
{
    /**
     * @var int
     */
    public $id_compte;
    /**
     * @var bool
     */
    public $actif;
    /**
     * @var string
     */
    public $email;
    /**
     * @var string
     */
    public $date_creation;
    
    public function __construct(){
        $this->id_compte=(int)$this->id_compte;
        $this->actif=(bool)$this->actif;
    }
}

class Produit
{
    /**
     * @var int
     */
    public $id_produit;
    /**
     * @var string
     */
    public $nom;
    /**
     * @var string
     */
    public $description;
    
    public function __construct(){
        $this->id_produit=(int)$this->id_produit;
    }
}

class Stock
{
    /**
     * @var int
     */
    public $id_produit;
    /**
     * @var string
     */
    public $nom;
    /**
     * @var string
     */
    public $description;
    /**
     * @var double
     */
    public $stock;
    
    public function __construct(){
        $this->id_produit=(int)$this->id_produit;
        $this->stock=(double)$this->stock;
    }
}

class Commande
{
    const ETAT_CREATION = 'Création';
    const ETAT_VALIDE = 'Validé';
    const ETAT_PREPARATION = 'Préparation';
    const ETAT_LIVRAISON = 'Livraison';
    const ETAT_TERMINE = 'Terminé';
    
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
    
    public function __construct(){
        $this->id_commande=(int)$this->id_commande;
        $this->id_compte=(int)$this->id_compte;
    }
}

class ElementCommande
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
    
    public function __construct(){
        $this->id_element_commande=(int)$this->id_element_commande;
        $this->id_commande=(int)$this->id_commande;
        $this->id_produit=(int)$this->id_produit;
        $this->quantite_commande=(double)$this->quantite_commande;
        $this->quantite_reel= isset($this->quantite_reel)?(double)$this->quantite_reel:null;
    }
}

class VariationStock
{
    const TYPE_ACHAT = 'ACHAT';
    const TYPE_VENTE = 'VENTE';
    const TYPE_PERTE = 'PERTE';
    const TYPE_AUTRE = 'AUTRE';
    const TYPE_RECOLTE = 'RECOLTE';
    
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
    
    public function __construct(){
        $this->id_variation_stock=(int)$this->id_variation_stock;
        $this->id_produit=(int)$this->id_produit;
        $this->variation=(double)$this->variation;
    }
}
?>