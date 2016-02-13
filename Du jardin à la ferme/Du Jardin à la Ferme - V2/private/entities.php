<?php
class Compte
{
    public $id_compte;
    public $actif;
    public $email;
    public $date_creation;
    
    public function __construct(){
        $this->id_compte=(int)$this->id_compte;
        $this->actif=(bool)$this->actif;
    }
}

class Produit
{
    public $id_produit;
    public $nom;
    public $description;
    public function __construct(){
        $this->id_produit=(int)$this->id_produit;
    }
}

class Stock
{
    public $id_produit;
    public $nom;
    public $description;
    public $stock;
    public function __construct(){
        $this->id_produit=(int)$this->id_produit;
        $this->stock=(double)$this->stock;
    }
}

class Commande
{
    public $id_commande;
    public $id_compte;
    public $date_creation;
    public $remarque;
    public $etat;
    public function __construct(){
        $this->id_commande=(int)$this->id_commande;
        $this->id_compte=(int)$this->id_compte;
    }
}

class ElementCommande
{
    public $id_element_commande;
    public $id_commande;
    public $id_produit;
    public $quantite_commande;
    public $quantite_reel;
    public function __construct(){
        $this->id_element_commande=(int)$this->id_element_commande;
        $this->id_commande=(int)$this->id_commande;
        $this->id_produit=(int)$this->id_produit;
        $this->quantite_commande=(double)$this->quantite_commande;
        $this->quantite_reel= isset($this->quantite_reel)?(double)$this->quantite_reel:null;
    }
}
?>