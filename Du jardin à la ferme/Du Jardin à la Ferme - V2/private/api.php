<?php 
require_once './private/config.php';
require_once './private/bdd.php';
require_once('./private/mustache.php');

class API
{ 
    public static function useAPI(callable $callback)
    {
        session_start();
        $endpoint=new static();
        $callback($endpoint);
        unset($endpoint);
        session_commit();
    }
    
    public static function execute($commande, $params)
    {
        session_start();
        $endpoint=new static();
        $commande = "API_".$commande;
        $endpointReflx = new ReflectionObject($endpoint);
        $methodReflx = $endpointReflx->getMethod($commande);
        if(!$params)$params=[];
        try{
            $result = [
                'status'=>'success',
                'value'=>$methodReflx->invokeArgs($endpoint, $params)
            ];
            
        }
        catch(ErrorException $ex)
        {
            $result = [
                'status'=>'error',
                'value'=>$ex->getMessage()
            ];
        }
        session_commit();
        return $result;
    }
    
    /**
     * Liens vers la base de donnée
     * @var BDD
     */
    private $bdd;
    private function __construct()
    {
        $this->bdd = new BDD();
    }
    
    
    /**
     * Recupère ou défini le compte connecté
     * @param Compte|null $compte 
     * @return Compte|null
     */
    public function compteConnecte($compte=false)
    {   
        static $CompteConnecte=false;
        if($compte===false)
        {
            if($CompteConnecte===false)
            {
                $CompteConnecte=$this->bdd->Compte_Recuperer((int)$_SESSION['CompteConnecte']);
            }
        }
        else
        {
            if($compte===null)
            {
                unset($_SESSION['CompteConnecte']);
            }
            else
            {
                $_SESSION['CompteConnecte']=(int)$compte->id_compte;
            }
            $CompteConnecte=$compte;
        }
        return $CompteConnecte;
    }
    
    /**
     * Indique si l'utilisateur est authentifier
     * @return bool
     */
    public function estAuthentifier(){
        return $this->compteConnecte()!==null;
    }
    
    /**
     * Indique si l'utilisateur est Admin
     * @return bool
     */
    public function estAdmin()
    {
        $Compte=$this->compteConnecte();
        return $Compte!==null && $Compte->statut==Compte::STATUT_Admin;
    }
    
    /**
     * Indique si l'utilisateur est Nouveau
     * @return bool
     */
    public function estNouveau()
    {
        $Compte=$this->compteConnecte();
        return $Compte!==null && $Compte->statut==Compte::STATUT_Nouveau;
    }
    
    /**
     * Indique si l'utilisateur est Panier
     * @return bool
     */
    public function estPanier()
    {
        $Compte=$this->compteConnecte();
        return $Compte!==null && $Compte->statut==Compte::STATUT_Panier;
    }
    
    /**
     * Indique si l'utilisateur est Désactivé
     * @return bool
     */
    public function estDésactivé()
    {
        $Compte=$this->compteConnecte();
        return $Compte!==null && $Compte->statut==Compte::STATUT_Desactive;
    }
    
    /**
     * Indique si l'utilisateur est LibreService
     * @return bool
     */
    public function estLibreService()
    {
        $Compte=$this->compteConnecte();
        return $Compte!==null && $Compte->statut==Compte::STATUT_LibreService;
    }
    
    /**
     * Indique si l'utilisateur est Premium
     * @return bool
     */
    public function estPremium()
    {
        $Compte=$this->compteConnecte();
        return $Compte!==null && $Compte->statut==Compte::STATUT_Premium;
    }
    
    /**
     * Indique si l'utilisateur peut modifier une commande en particulier
     * @return bool
     */
    public function peutCommander()
    {
        $Compte=$this->compteConnecte();
        if(!$Compte) return false;
        switch($Compte->statut)
        {
            case Compte::STATUT_Admin:
                return true;
            case Compte::STATUT_LibreService:
                $jour = date('N');
                return ($jour>2) && ($jour< 6);
            case Compte::STATUT_Premium:
                $jour = date('N');
                return ($jour>0) && ($jour< 6);
            case Compte::STATUT_Desactive:
            case Compte::STATUT_Nouveau:
            case Compte::STATUT_Panier:
            default:
                return false;
        }
    }
    
    public function peutModifierCommande($id_commande)
    {
        $id_commande=(int)$id_commande;
        
        if(!$this->estAdmin())
        {
            if(!$this->peutCommander())
                return false;

            $commande = $this->bdd->Commande_Recupere($id_commande);
            if($commande->id_compte!=$this->compteConnecte()->id_compte)
                return false;
            if($commande->etat!==Commande::ETAT_CREATION)
                return false;
        }
        return true;
    }
    
    public function peutVoirCommande($id_commande)
    {
        $id_commande=(int)$id_commande;
        
        if(!$this->estAdmin())
        {
            if(!$this->peutCommander())
                return false;

            $commande = $this->bdd->Commande_Recupere($id_commande);
            if($commande->id_compte!=$this->compteConnecte()->id_compte)
                return false;
        }
        return true;
    }
    
    
    
    public function API_compte_authentifier($email, $mot_de_passe)
    {
        if($this->estAuthentifier())
            throw new ErrorException("Vous êtes déjà authentifier");
        
        $email=(string)$email;
        $mot_de_passe=(string)$mot_de_passe;
        
        if(empty($email))
            throw new ErrorException("email non renseigné");
        if(empty($mot_de_passe))
            throw new ErrorException("mot_de_passe non renseigné");
    
        $compte = $this->bdd->Compte_Authentifier($email, $mot_de_passe);
        if(!$compte)
            throw new ErrorException("Erreur de connexion, veuillez vérifier votre email et votre mots de passe.");
        
        $this->compteConnecte($compte);
        return $compte;
    }
    
    public function API_compte_deconnecter()
    {
        if(!$this->estAuthentifier())
            throw new ErrorException("Vous n'êtes pas authentifier");
        $compte = $this->compteConnecte(null);
        return $compte;
    }
    
    public function API_compte_creer($email, $mot_de_passe)
    {
        if($this->estAuthentifier())
            throw new ErrorException("Vous êtes déjà authentifier");
        
        $email=(string)$email;
        $mot_de_passe=(string)$mot_de_passe;
        
        if(empty($email))
            throw new ErrorException("email non renseigné");
        if(empty($mot_de_passe))
            throw new ErrorException("mot_de_passe non renseigné");
        
        $compte = $this->bdd->Compte_Creer($email, $mot_de_passe);
        if(!$compte)
            throw new ErrorException("Impossible de créer le compte, êtes vous sur de ne pas avoir déjà un compte.");
        
        $this->compteConnecte($compte);
        
        return $compte;
    }
    
    public function API_compte_modifier_mot_de_passe($actuel_mot_de_passe, $nouveau_mot_de_passe)
    {
        if(!$this->estAuthentifier())
            throw new ErrorException("Vous devez vous authentifier");
        
        $actuel_mot_de_passe=(string)$actuel_mot_de_passe;
        $nouveau_mot_de_passe=(string)$nouveau_mot_de_passe;
        
        if(empty($actuel_mot_de_passe))
            throw new ErrorException("actuel_mot_de_passe non renseigné");
        if(empty($nouveau_mot_de_passe))
            throw new ErrorException("nouveau_mot_de_passe non renseigné");
        
        $compte = $this->bdd->Compte_Modifier_MotDePasse($this->compteConnecte()->id_compte, $actuel_mot_de_passe, $nouveau_mot_de_passe);
        
        if(!$compte)
            throw new ErrorException("Impossible de modifier le mot de passe du compte, êtes vous sûr d'avoir mis le bon mot de passe actuel?.");
        
        $this->compteConnecte($compte);
        
        return $compte;
    }
    
    
    
    
    
    public function API_produits_lister($rechercheProduit)
    {   
        $id_panier=null;
        if($this->peutCommander())
            $id_panier=$this->API_panier_recuperer()->id_commande;
        
        return $this->bdd->Produits_Lister($rechercheProduit,$id_panier,true);
    }
    
    public function API_produit_recuperer($id_produit)
    {   
        $id_panier=null;
        if($this->peutCommander())
            $id_panier=$this->API_panier_recuperer()->id_commande;
        
        return $this->bdd->Produits_Recuperer($id_produit, $id_panier);
    }
    
    public function API_produit_modifier_description($id_produit, $description)
    {   
        if(!$this->estAdmin())
            throw new ErrorException("Cet opération n'est possible qu'aux administrateurs!");
        return $this->bdd->Produits_Modifier_Description($id_produit,$description);
    }
    public function API_produit_modifier_nom($id_produit, $nom)
    {   
        if(!$this->estAdmin())
            throw new ErrorException("Cet opération n'est possible qu'aux administrateurs!");
        return $this->bdd->Produits_Modifier_Nom($id_produit,$nom);
    }
    public function API_produit_modifier_unite($id_produit, $unite)
    {   
        if(!$this->estAdmin())
            throw new ErrorException("Cet opération n'est possible qu'aux administrateurs!");
        return $this->bdd->Produits_Modifier_Unite($id_produit,$unite);
    }
    public function API_produit_modifier_prix_unitaire_ttc($id_produit, $prix_unitaire_ttc)
    {   
        if(!$this->estAdmin())
            throw new ErrorException("Cet opération n'est possible qu'aux administrateurs!");
        return $this->bdd->Produits_Modifier_prix_unitaire_ttc($id_produit,$prix_unitaire_ttc);
    }
    
    
    
    
    
    
    public function API_panier_recuperer()
    {
        if(!$this->peutCommander())
            throw ErrorException("Vous ne pouvez pas accédé au panier");
        return $this->bdd->Panier_RecupererOuCreer($this->compteConnecte()->id_compte);
    }
    
    public function API_commande_recuperer($id_commande)
    {
        $id_commande=(int)$id_commande;
        
        if(!$this->peutVoirCommande($id_commande))
            throw new ErrorException('vous ne pouvez pas voir la commande');
        
        return $this->bdd->Panier_RecupererOuCreer($this->compteConnecte()->id_compte);
    }
    
    public function API_commande_lister_elements($id_commande)
    {   
        $id_commande=(int)$id_commande;
        
        if(!$this->peutVoirCommande($id_commande))
            throw new ErrorException('vous ne pouvez pas voir la commande');
        
        return $this->bdd->Commande_lister_Elements($id_commande);
    }
    
    public function API_commande_vider($id_commande)
    {   
        $id_commande=(int)$id_commande;
        
        if(!$this->peutModifierCommande($id_commande))
            throw new ErrorException('vous ne pouvez pas modifier la commande');
        
        return $this->bdd->Commande_Vider($id_commande);
    }
    
    public function API_commande_valider($id_commande)
    {   
        $id_commande=(int)$id_commande;
        
        if(!$this->peutModifierCommande($id_commande))
            throw new ErrorException('vous ne pouvez pas modifier la commande');
        
        return $this->bdd->Commande_Valider($id_commande);
    }
    
    public function API_produitcommande_modifier_quantite_commande($id_commande, $id_produit, $quantite)
    {
        $id_commande=(int)$id_commande;
        $id_produit=(int)$id_produit;
        $quantite=(double)$quantite;
        
        if(!$this->peutModifierCommande($id_commande))
            throw new ErrorException('vous ne pouvez pas modifier la commande');
        
        return $this->bdd->Commande_Modifier_Element($id_commande, $id_produit, $quantite);
    }
    
}