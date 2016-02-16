<?php
require_once 'config.php';
require_once 'password.php';
require_once 'entities.php';

/**
 * Couche de liaison à la BDD.
 *
 * permet l'acces à la base de donnée et fournis 
 * les principale fonctionnalité
 *
 * @version 1.0
 * @author Romain
 */
class BDD
{
    /**
     * lien vers la base de donnée
     * @var \PDO
     */
    private $pdolink;
    
    /**
     * Ouverture de la base de donnée
     */
    public function __construct()
    {
        $this->pdolink = new \PDO(
			"mysql:host=".BDD_SERVER.";dbname=".BDD_SCHEMA.";charset=utf8",
			BDD_USER ,  
			BDD_PASSWORD,
			array(
				\PDO::ATTR_ERRMODE				=> \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_DEFAULT_FETCH_MODE	=> \PDO::FETCH_ASSOC,
				\PDO::MYSQL_ATTR_LOCAL_INFILE	=> 1
			)
		);
        $this->pdolink->exec("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
    }
    
    
    /**
     * Indique le niveau d'imbrication des transaction
     * @var int
     */
    private $transactionCounter = 0;

    /**
     * Démarre une transaction ou créer un point de sauvegarde dans la transaction courante
     * @return bool
     */
    public function beginTransaction()
    {
        if (!$this->transactionCounter++) {
            return $this->pdolink->beginTransaction();
        }
        $this->pdolink->exec('SAVEPOINT trans'.$this->transactionCounter);
        return true;
    }

    /**
     * Valide la transaction ou bien libére le point de sauvegarde courant
     * @return bool
     */
    public function commit()
    {
        if (!--$this->transactionCounter) {
            return $this->pdolink->commit();
        }
        else{
            $this->pdolink->exec('RELEASE SAVEPOINT trans'.($this->transactionCounter + 1));
            return true;
        }
    }

    /**
     * Annule la transaction ou bien revient au point de sauvegarde courant
     * @return bool
     */
    public function rollback()
    {
        if (--$this->transactionCounter) {
            $this->pdolink->exec('ROLLBACK TO SAVEPOINT trans'.($this->transactionCounter + 1));
            return true;
        }
        return $this->pdolink->rollback();
    }
    
    /**
     * Encapsule l'execution de $function dans une transaction ou un point de sauvegarde.
     * La transaction ou le point de sauvegarde sont validé automatiquement si la $function arrive a sont terme.
     * La transaction est annulé en cas d'exception (l'exception est ensuite relancé)
     * @param callable $function 
     * @return mixed
     */
    public function InTransaction(callable $function)
    {   
        $this->beginTransaction();
        try{
            $result = $function();
            $this->commit();
        }
        catch(Exception $ex)
        {   
            $this->rollBack();
            throw $ex;
        }
        return $result;
    }
    
    
    
    
    
    /**
     * Bind les paramettres d'une requettes.
     * Le type de bind est automatiquement définie en fonction du type de valeur.
     * @param PDOStatement $statement
     * La requettes préparé
     * @param array $parameters
     * Les paramettres à binder
     */
    private function bindValues(PDOStatement $statement, $parameters)
    {
        foreach($parameters as $name => $value)
        {
            $type = PDO::PARAM_STR;
            if(is_bool($value))
                $type = PDO::PARAM_BOOL;
            else if(is_int($value))
                $type = PDO::PARAM_INT;
            else if(is_long($value))
                $type = PDO::PARAM_INT;
            $statement->bindValue($name,$value,$type);
        }
    }
    
    /**
     * Execute une requette et revois les lignes sous forme d'un tableau d'objet
     * @param string $class 
     * Classe de l'objet à retourné
     * @param string $sql 
     * Requette à exécuté
     * @param array $parameters 
     * Paramettres de la requettes(ex: [':id'=>(int)$id,':nom'=>(string)$nom])
     * @throws ErrorException 
     * @return array
     */
    public function getAllObjects($class, $sql, $parameters)
    {
        $statement = $this->pdolink->prepare($sql);
        $this->bindValues($statement, $parameters);
        $statement->execute();
        $return_objects = $statement->fetchAll(PDO::FETCH_CLASS,$class);
        $statement->closeCursor();
        if(!$return_objects)$return_objects=[];
        return $return_objects;
    }
    
    /**
     * Execute une requette et revois les lignes sous forme d'un tableau de tableau associatif
     * @param string $sql 
     * Requette à exécuté
     * @param array $parameters 
     * Paramettres de la requettes(ex: [':id'=>(int)$id,':nom'=>(string)$nom])
     * @throws ErrorException 
     * @return array
     */
    public function getAll($sql, $parameters)
    {
        $statement = $this->pdolink->prepare($sql);
        $this->bindValues($statement, $parameters);
        $statement->execute();
        $return_objects = $statement->fetchAll();
        $statement->closeCursor();
        if(!$return_objects)$return_objects=[];
        return $return_objects;
    }
    
    /**
     * Execute une requette et revois la première ligne sous forme d'objet
     * @param string $class 
     * Classe de l'objet à retourné
     * @param string $sql 
     * Requette à exécuté
     * @param array $parameters 
     * Paramettres de la requettes(ex: [':id'=>(int)$id,':nom'=>(string)$nom])
     * @throws ErrorException 
     * @return object|null
     */
    public function getSingleObject($class, $sql, $parameters)
    {
        $statement = $this->pdolink->prepare($sql);
        $this->bindValues($statement, $parameters);
        $statement->execute();
        $return_object = $statement->fetchObject($class);
        $statement->closeCursor();
        if(!$return_object)$return_object=null;
        return $return_object;
    }
    
    /**
     * Execute une requette et revois la première ligne sous forme d'un tableau associatif
     * @param string $sql 
     * Requette à exécuté
     * @param array $parameters 
     * Paramettres de la requettes(ex: [':id'=>(int)$id,':nom'=>(string)$nom])
     * @throws ErrorException 
     * @return array|null
     */
    public function getSingle($sql, $parameters)
    {
        $statement = $this->pdolink->prepare($sql);
        $this->bindValues($statement, $parameters);
        $statement->execute();
        $return_object = $statement->fetch();
        $statement->closeCursor();
        if(!$return_object)$return_object=null;
        return $return_object;
    }
    
    /**
     * Execute une requette et revois la valeur de la premiere colone de la première ligne.
     * @param string $sql 
     * Requette à exécuté
     * @param array $parameters 
     * Paramettres de la requettes(ex: [':id'=>(int)$id,':nom'=>(string)$nom])
     * @throws ErrorException 
     * @return mixed
     */
    public function getScalar($sql, $parameters)
    {
        $statement = $this->pdolink->prepare($sql);
        $this->bindValues($statement, $parameters);
        $statement->execute();
        $return_object = $statement->fetchColumn(0);
        $statement->closeCursor();
        return $return_object;
    }
    
    /**
     * Execute une requette et revois le nombre de ligne affectée
     * @param string $sql 
     * Requette à exécuté
     * @param array $parameters 
     * Paramettres de la requettes(ex: [':id'=>(int)$id,':nom'=>(string)$nom])
     * @throws ErrorException 
     * @return mixed
     */
    public function exec($sql,$parameters)
    {
        $statement = $this->pdolink->prepare($sql);
        $this->bindValues($statement, $parameters);
        $statement->execute();
        $rowCount = $statement->rowCount();
        $statement->closeCursor();
        return $rowCount;
    }
    
    /**
     * Execute une requette et revois l'ID de la derniére insertion
     * @param string $sql 
     * Requette à exécuté
     * @param array $parameters 
     * Paramettres de la requettes(ex: [':id'=>(int)$id,':nom'=>(string)$nom])
     * @throws ErrorException 
     * @return string|null
     */
    public function execInsert($sql,$parameters)
    {
        $rowCount = $this->exec($sql,$parameters);
        $lastId=($rowCount>0)?$this->pdolink->lastInsertId():null;
        return $lastId;
    }
    
    
    
    
    
    /**
     * Supprime toutes les données dans la Base de donnée
     * (la structure est conservé)
     */
    public function CLEAR_ALL()
    {
        $this->InTransaction(function(){; 

            $this->pdolink->exec('SET FOREIGN_KEY_CHECKS = 0');
            $this->pdolink->exec('TRUNCATE TABLE variations_stock');
            $this->pdolink->exec('TRUNCATE TABLE elements_commande');
            $this->pdolink->exec('TRUNCATE TABLE variations_stock');
            $this->pdolink->exec('TRUNCATE TABLE commandes');
            $this->pdolink->exec('TRUNCATE TABLE produits');
            $this->pdolink->exec('TRUNCATE TABLE comptes');
            $this->pdolink->exec('SET FOREIGN_KEY_CHECKS = 1');
        });
    }
    
    
    
    
    
    /**
     * Récupere un compte spécifique
     * @param int $id_compte 
     * @throws ErrorException 
     * @return Compte
     */
    public function Compte_Recuperer($id_compte)
    {
        $id_compte = (int)$id_compte;
        
        return $this->InTransaction(function()use($id_compte){
            $compte = $this->getSingleObject(
                'Compte',
                'SELECT 
                    id_compte, 
                    email, 
                    date_creation, 
                    etat 
                FROM comptes 
                WHERE id_compte = :id_compte',
                [
                    ':id_compte' => $id_compte
                ]
            );
            return $compte;
        });
    }
    
    /**
     * Créer un nouveau compte et retourne les informations de celui-ci
     * @param string $email 
     * @param string $password 
     * @throws ErrorException 
     * @return Compte
     */
    public function Compte_Creer($email, $password)
    {
        $email=mb_strimwidth((string)$email,0,255);
        $password=(string)$password;
        
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new ErrorException('Adresse email invalide');
        
        //on hash le mot de passe pour la sécurité
        $password = password_hash($password,PASSWORD_DEFAULT);
        
        
        return $this->InTransaction(function()use($email, $password){
        
            // on vérifie si l'identifiant éxiste déjà
            $compte_exists = $this->getScalar(
                'SELECT COUNT(*) 
                FROM comptes 
                WHERE email = :email',
                [
                    ':email' => $email
                ]
            );
        
            if($compte_exists > 0)
                throw new ErrorException('Erreur de connexion, veuillez vérifier votre email et votre mots de passe.');
        
            //on insert le nouveau compte
            $id_compte = (int)$this->execInsert(
                'INSERT INTO Comptes 
                (
                    email,
                    hash
                )
                VALUES(
                    :email,
                    :hash
                )',
                array(
                    ':email'=>$email,
                    ':hash'=>$password
                )
            );
                
            return $this->Compte_Recuperer($id_compte);
        });
    }
    
    /**
     * Authentifie un compte et retourne les infos de celui-ci'
     * @param string $email 
     * Email du compte à authentifier
     * @param string $password 
     * Mot de passe du compte à authentifier
     * @throws ErrorException 
     * @return Compte
     */
    public function Compte_Authentifier($email, $password)
    {
        $email=mb_strimwidth((string)$email,0,255);
        $password=(string)$password;
        
        return $this->InTransaction(function()use($email, $password){
        
            // on récupére le hash et l'id du compte
            $compte_infos = $this->getSingle(
                'SELECT 
                    id_compte, 
                    hash 
                FROM comptes 
                WHERE etat != \'Désactivé\'
                AND email = :email',
                [
                    ':email'=>$email
                ]
            );
        
            $hash = (string)$compte_infos['hash'];
            $id_compte = (int)$compte_infos['id_compte'];
        
            // on vérifie le hash
            if(!password_verify($password, $hash))
                return null;
        
            // on vérifie si le hash doit être mis à jour
            if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
            
                $this->exec(
                    'UPDATE Comptes 
                    SET hash = :hash 
                    WHERE id_compte = :id_compte',
                    [
                        ':id_compte'=>$id_compte,
                        ':hash'=>$hash
                    ]
                );
            }
        
            return $this->Compte_Recuperer($id_compte);
        });
    }
    
    /**
     * Modifie l'état du compte
     * @param int $id_compte 
     * ID du compte à modifier
     * @param bool $actif 
     * Nouvel état du compte
     * @throws ErrorException 
     * @return Compte
     */
    public function Compte_Modifier_MotDePasse($id_compte, $ancien_mot_de_passe,$nouveau_mot_de_passe)
    {
        $id_compte=(int)$id_compte;
        $ancien_mot_de_passe=(string)$ancien_mot_de_passe;
        $nouveau_mot_de_passe=(string)$nouveau_mot_de_passe;
        
        return $this->InTransaction(function()use($id_compte, $ancien_mot_de_passe, $nouveau_mot_de_passe){
            
            // on récupére le hash et l'id du compte
            $hash = (string)$this->getScalar(
                'SELECT hash 
                FROM comptes 
                WHERE actif = 1 
                AND id_compte = :id_compte',
                [
                    ':id_compte'=>$id_compte
                ]
            );
        
            // on vérifie le hash
            if(!password_verify($ancien_mot_de_passe, $hash))
                return null;
            
            //On met à jour le Motdepasse
            $hash = password_hash($nouveau_mot_de_passe, PASSWORD_DEFAULT);
            
            $this->exec(
                'UPDATE Comptes 
                SET hash = :hash 
                WHERE id_compte = :id_compte',
                [
                    ':id_compte'=>$id_compte,
                    ':hash'=>$hash
                ]
            );
        
            return $this->Compte_Recuperer($id_compte);
        });
    }
    
    /**
     * Modifie l'état du compte
     * @param int $id_compte 
     * ID du compte à modifier
     * @param bool $actif 
     * Nouvel état du compte
     * @throws ErrorException 
     * @return Compte
     */
    public function Compte_Modifier_Etat($id_compte, $etat)
    {
        $id_compte=(int)$id_compte;
        $etat=(string)$etat;
        
        return $this->InTransaction(function()use($id_compte, $etat){
            $this->exec(
                'UPDATE Comptes 
                SET etat = :etat 
                WHERE id_compte = :id_compte',
                [
                    ':id_compte'=>$id_compte,
                    ':etat'=>$etat
                ]
            );
        
            return $this->Compte_Recuperer($id_compte);
        });
    }
    
    
    
    
    /**
     * Récupére un produit spécifique
     * @param int $id_produit
     * ID du produit à récupérer
     * @throws ErrorException 
     * @return Produit
     */
    public function Produits_Recuperer($id_produit)
    {
        $id_produit = (int)$id_produit;
        
        return $this->InTransaction(function()use($id_produit){
            $produit = $this->getSingleObject(
                'Produit',
                'SELECT *
                FROM view_produits 
                WHERE id_produit = :id_produit',
                array(
                    ':id_produit'=>$id_produit
                )
            );
            return $produit;
        });
    }
    
    /**
     * Liste tous les produits
     * @throws ErrorException 
     * @return Produit[]
     */
    public function Produits_Lister()
    {
        return $this->InTransaction(function(){
            $produits = $this->getAllObjects(
                'Produit',
                'SELECT *
                FROM view_produits',
                []
            );
            return $produits ?: [];
        });
    }
    public function Produits_Recuperer_DetailAvecPanier($id_commande,$id_produit)
    {
        $id_commande = (int)$id_commande;
        $id_produit = (int)$id_produit;
        
        return $this->InTransaction(function()use($id_commande,$id_produit){
            
            $produit = $this->getSingleObject(
                'ProduitAvecPanier',
                'SELECT 
	            view_produits.*,
                COALESCE(elements_commande.quantite_commande,0) AS quantite_commande
            FROM view_produits
            LEFT OUTER JOIN elements_commande
	            ON view_produits.id_produit=elements_commande.id_produit
	            AND elements_commande.id_commande = :id_commande
            WHERE view_produits.id_produit = :id_produit',
                array(
                    ':id_commande'=>$id_commande,
                    ':id_produit'=>$id_produit
                )
            );
            return $produit;
        });
    }
    
    public function Produits_Lister_DetailAvecPanier($id_commande,$dispoUniquement)
    {
        $id_commande = (int)$id_commande;
        
        return $this->InTransaction(function()use($id_commande,$dispoUniquement){
            if($dispoUniquement)
            {
                $produits = $this->getAllObjects(
                   'ProduitAvecPanier',
                   'SELECT 
	                view_produits.*,
                    COALESCE(elements_commande.quantite_commande,0) AS quantite_commande
                FROM view_produits
                LEFT OUTER JOIN elements_commande
	                ON view_produits.id_produit=elements_commande.id_produit
	                AND elements_commande.id_commande = :id_commande
                WHERE view_produits.stocks_previsionnel > 0',
                   array(
                       ':id_commande'=>$id_commande
                   )
               );
            }
            else
            {
                $produits = $this->getAllObjects(
                   'ProduitAvecPanier',
                   'SELECT 
	                view_produits.*,
                    COALESCE(elements_commande.quantite_commande,0) AS quantite_commande
                FROM view_produits
                LEFT OUTER JOIN elements_commande
	                ON view_produits.id_produit=elements_commande.id_produit
	                AND elements_commande.id_commande = :id_commande',
                   array(
                       ':id_commande'=>$id_commande
                   )
               );
            }
            return $produits;
        });
    }
    
    /**
     * Récupére un produit spécifique
     * @param string $nom 
     * Nom du produit à créer
     * @param string $description 
     * Description du produit
     * @throws ErrorException 
     * @return Produit
     */
    public function Produits_Creer($nom, $description, $unite)
    {
        $nom = mb_strimwidth((string)$nom,0,100);
        $description = mb_strimwidth((string)$description,0,500);
        
        return $this->InTransaction(function()use($nom, $description, $unite){
        
            // on vérifie si l'identifiant éxiste déjà
            $count = $this->getScalar(
                'SELECT COUNT(*) 
                FROM produits 
                WHERE nom = :nom',
                [
                    ':nom'=>$nom
                ]
            );
        
            if($count>0)
                throw new ErrorException("Le produit existe déjà.");
        
            //On créer le produit
            $id_produit = (int)$this->execInsert(
                'INSERT INTO produits 
                (
                    nom, 
                    description
                )
                VALUES
                (
                    :nom,
                    :description,
                    :unite
                )',
                [
                    ':nom'=>$nom,
                    ':description'=>$description,
                    ':unite'=>$unite
                    
                ]
            );
        
            return $this->Produits_Recuperer($id_produit);
        });
    }
    
    /**
     * Créer une entrée de variation de stock
     * @param int $id_produit 
     * L'id du produit dont le stock varie
     * @param double $variation 
     * Montant de la variation (négative quand le stock baisse)
     * @param string $type 
     * Type de variation : Récolte, Vente, Perte, etc...
     * @param string $remarque 
     * Informations complémentaires sur la variation
     * @throws ErrorException 
     * @return Produit
     */
    public function VariationStock_Ajouter($id_produit,$variation,$type,$remarque)
    {
        $id_produit = (int)$id_produit;
        $variation = (double)$variation;
        $type = (string)$type;
        $remarque = mb_strimwidth((string)$remarque,0,500);
        
        return $this->InTransaction(function()use($id_produit,$variation,$type,$remarque){
            //On créer la variation
            $id_variation_stock = (int)$this->execInsert(
                'INSERT INTO variations_stock
                (
                    id_produit, 
                    variation, 
                    type_variation, 
                    remarque
                )
                VALUES
                (
                    :id_produit, 
                    :variation, 
                    :type, 
                    :remarque
                )',
                [
                    ':id_produit'=>$id_produit,
                    ':variation'=>$variation,
                    ':type'=>$type,
                    ':remarque'=>$remarque
                ]
            );
            return $this->Produits_Recuperer($id_produit);
        });
    }
    
    /**
     * Récupère une commande spécifique
     * @param int $id_commande 
     * ID de la commande à récupérer
     * @throws ErrorException 
     * @return Commande
     */
    public function Commande_Recupere($id_commande)
    {
        $id_commande=(int)$id_commande;
        
        return $this->InTransaction(function()use($id_commande){
            //On cherche la commande
            $commande = $this->getSingleObject(
                'Commande',
                'SELECT 
                    id_commande, 
                    id_compte, 
                    date_creation, 
                    remarque, 
                    etat 
                FROM commandes 
                WHERE id_commande = :id_commande',
                [
                    ':id_commande'=>$id_commande
                ]
            );
            return $commande;
        });
    }
    
    /**
     * Liste les elements d'une commande'
     * @param int $id_commande 
     * ID de la commande à rècupérer
     * @return ElementCommande[]
     */
    public function Commande_lister_Elements($id_commande)
    {
        $id_commande=(int)$id_commande;
        
        return $this->InTransaction(function()use($id_commande){
            $elements = $this->getAllObjects(
                'ElementCommande',
                'SELECT *
                FROM view_elements_commande 
                WHERE id_commande = :id_commande',
                [
                    ':id_commande'=>$id_commande
                ]
            );
            return $elements ?: [];
        });
    }
    
    /**
     * Ajoute, modifie ou supprime un élément dans le panier d'un compte'
     * @param int $id_compte
     * ID du compte dont on modifie le panier
     * @param int $id_produit 
     * ID du produit commandé
     * @param double $quantite 
     * Quantité commandé (si = 0, l'élément est supprimé de la commande)
     * @return ElementCommande
     */
    public function Commande_Modifier_Element($id_commande,$id_produit,$quantite)
    {
        $quantite=(double)$quantite;
        $id_commande=(int)$id_commande;
        $id_produit=(int)$id_produit;
        
        return $this->InTransaction(function()use($id_commande,$id_produit,$quantite){
        
            if($quantite==0)
            {
                //On supprime l'élément de commande
                $this->exec(
                    'DELETE FROM elements_commande
                    WHERE id_commande = :id_commande
                    AND  id_produit = :id_produit',
                    [
                        ':id_commande'=>$id_commande,
                        ':id_produit'=>$id_produit
                    ]
                );
            }
            else
            {
                //On créer/modifie l'élément de commande
                $this->execInsert(
                    'INSERT INTO elements_commande
                    (
                        id_commande, 
                        id_produit, 
                        quantite_commande
                    )
                    VALUES(
                        :id_commande, 
                        :id_produit, 
                        :quantite_commande
                    )
                    ON DUPLICATE KEY UPDATE
                        quantite_commande = :quantite_commande',
                    [
                        ':id_commande'=>$id_commande,
                        ':id_produit'=>$id_produit,
                        ':quantite_commande'=>$quantite
                    ]
                );
            }
        
            $element = $this->getSingleObject(
                'ElementCommande',
                'SELECT *
                FROM view_elements_commande 
                WHERE id_commande = :id_commande
                AND id_produit = :id_produit',
                [
                    ':id_commande'=>$id_commande,
                    ':id_produit'=>$id_produit
                ]
            );
        
            return $element;
        });
    }
    
    /**
     * Valide le panier d'un compte.
     * @param int $id_compte 
     * ID du compte dont on veux valider le panier
     * @return Commande
     */
    public function Commande_Valider($id_commande)
    {   
        $id_commande=(int)$id_commande;
        
        return $this->InTransaction(function()use($id_commande){
            
            $nbElements=(int)$this->getScalar(
                'SELECT COUNT(*)
                FROM elements_commande
                WHERE elements_commande.id_commande = :id_commande',
                [
                    ':id_commande'=>$id_commande
                ]
            );
            if($nbElements==0)
                throw new ErrorException("Le panier est vide.");
            
            $StockInsufisant=(int)$this->getScalar(
                'SELECT COUNT(*)
                FROM elements_commande
                LEFT OUTER JOIN stocks_previsionnel
	                ON elements_commande.id_produit = stocks_previsionnel.id_produit
                WHERE elements_commande.id_commande = :id_commande
                AND elements_commande.quantite_commande>COALESCE(stocks_previsionnel.stock,0)',
                [
                    ':id_commande'=>$id_commande
                ]
            );
            
            if($StockInsufisant>1)
                throw new ErrorException("Il y'as $StockInsufisant produits dont le stock est insufisant.");
            if($StockInsufisant>0)
                throw new ErrorException("Il y'as $StockInsufisant produit dont le stock est insufisant.");
            
            
            //On valide la commande
            $this->exec(
                'UPDATE commandes
                    SET etat = \'Validé\'
                WHERE id_commande = :id_commande 
                    AND etat = \'Création\'',
                [
                    ':id_commande'=>$id_commande
                ]
            );
            
            return $this->Commande_Recupere($id_commande);
        });
    }
     
    /** 
     * Vide le pannier d'un compte.
     * @param int $id_compte 
     * ID du compte dont on veux vider le panier
     * @return Commande
     */
    public function Commande_Vider($id_commande)
    {   
        $id_commande=(int)$id_commande;
        
        return $this->InTransaction(function()use($id_commande){
            //On vide la commande
            $this->exec(
                'DELETE FROM elements_commande
                WHERE id_commande = :id_commande',
                [
                    ':id_commande'=>$id_commande
                ]
            );
            
            return $this->Commande_lister_Elements($id_commande);
        });
    }
    
    public function Commande_Supprimer($id_commande)
    {   
        $id_commande=(int)$id_commande;
        
        return $this->InTransaction(function()use($id_commande){
        
            $this->Commande_Vider($id_commande);
            
            //On supprime la commande
            $this->exec(
                'DELETE FROM commandes
                WHERE id_commande = :id_commande',
                [
                    ':id_commande'=>$id_commande
                ]
            );
            
            return $this->Commande_Recupere($id_commande);
        });
    }
    
    public function PanierAnonyme_Transferer($id_commande,$id_compte)
    {
        $id_commande= (int)$id_commande;
        $id_compte= (int)$id_compte;
        
        return $this->InTransaction(function()use($id_commande,$id_compte){
            
            $panierCompte = $this->Panier_RecupererOuCreer($id_compte);
            
            $elementpanieranonyme = $this->Commande_lister_Elements($id_commande);
            if(count($elementpanieranonyme))
            {
                $this->Commande_Vider($panierCompte->id_commande);
            
                foreach($elementpanieranonyme as $element)
                {
                    $this->Commande_Modifier_Element($panierCompte->id_commande,$element->id_produit,$element->quantite_commande);
                }
                $this->Commande_Supprimer($id_commande);
            }
            return $this->Commande_Recupere($panierCompte->id_commande);
        });
    }
    
    /**
     * récupére l'ID du panier d'un compte, si le panier n'éxiste pas il est créer.
     * @param int|null $id_compte 
     * ID du compte dont veux récupéré l'ID de panier.
     * Si $id_compte est null, on créer un panier anonyme.
     * @return Commande
     */
    public function Panier_RecupererOuCreer($id_compte)
    {
        $id_compte= isset($id_compte)?(int)$id_compte:null;
        
        return $this->InTransaction(function()use($id_compte){
            
            if($id_compte)
            {
                //On cherche une commande existante en création
                $id_commande = (int)$this->getScalar(
                    'SELECT 
                        id_commande
                    FROM commandes 
                    WHERE id_compte = :id_compte 
                    AND etat = \'Création\' 
                    LIMIT 1',
                    [
                        ':id_compte'=>$id_compte
                    ]
                );
                if($id_commande)
                    return $this->Commande_Recupere($id_commande);
            }
            
            $id_commande = (int)$this->execInsert(
                'INSERT INTO commandes 
                (
                    id_compte
                )
                VALUES
                (
                    :id_compte
                )',
                [
                    ':id_compte'=>$id_compte
                ]
            );
                
            return $this->Commande_Recupere($id_commande);
        });
    }
    
}