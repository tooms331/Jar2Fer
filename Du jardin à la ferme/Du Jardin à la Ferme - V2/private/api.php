<?php 
require_once './private/config.php';
require_once './private/bdd.php';

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
                if(isset($_SESSION['idPanierAnonyme']))
                {
                    $this->bdd->PanierAnonyme_Transferer((int)$_SESSION['idPanierAnonyme'],(int)$compte->id_compte);
                    unset($_SESSION['idPanierAnonyme']);
                }
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
     * Indique si l'utilisateur est administrateur
     * @return bool
     */
    public function estAdmin()
    {
        $Compte=$this->compteConnecte();
        return $Compte!==null && $Compte->etat=='Admin';
    }
    
    private function panier_recuperer()
    {
        $panier = null;
        if($this->estAuthentifier())
        {
            $panier =  $this->bdd->Panier_RecupererOuCreer($this->compteConnecte()->id_compte);
        }
        else
        {
            $idPanierAnonyme = (int)$_SESSION['idPanierAnonyme'];
            
            if($idPanierAnonyme)
            {
                $panier =  $this->bdd->Commande_Recupere($idPanierAnonyme);
            }
            
            if(!$panier)
                $panier = $this->bdd->Panier_RecupererOuCreer(null);
            
            if($panier)
            {
                $_SESSION['idPanierAnonyme']=$panier->id_commande;
            }
        }
        return $panier;
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
    
    public function API_panier_modifier_element($id_produit, $quantite)
    {
        $id_produit=(int)$id_produit;
        $quantite=(double)$quantite;
            
        $panier=$this->panier_recuperer();
        return $this->bdd->Commande_Modifier_Element($panier->id_commande,$id_produit, $quantite);
    }
    
    public function API_panier_lister_elements()
    {   
        $panier=$this->panier_recuperer();
        return $this->bdd->Commande_lister_Elements($panier->id_commande);
    }
    
    public function API_panier_vider()
    {   
        $panier=$this->panier_recuperer();
        return $this->bdd->Commande_Vider($panier->id_commande);
    }
    
    public function API_panier_valider()
    {   
        if(!$this->estAuthentifier())
            throw new ErrorException("Vous devez vous authentifier ou créer un compte pour valider le panier.");
        
        $panier=$this->panier_recuperer();
        return $this->bdd->Commande_Valider($panier->id_commande);
    }
    
    public function API_produits_lister()
    {   
        $panier=$this->panier_recuperer();
        return $this->bdd->Produits_Lister_DetailAvecPanier($panier->id_commande,true);
    }
    public function API_produit_recuperer($id_produit)
    {   
        $panier=$this->panier_recuperer();
        return $this->bdd->Produits_Recuperer_DetailAvecPanier($panier->id_commande,$id_produit);
    }
}