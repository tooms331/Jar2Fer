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
    
    
    
    private $transactionCounter = 0;

    public function beginTransaction()
    {
        if (!$this->transactionCounter++) {
            return $this->pdolink->beginTransaction();
        }
        $this->pdolink->exec('SAVEPOINT trans'.$this->transactionCounter);
        return true;
    }

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

    public function rollback()
    {
        if (--$this->transactionCounter) {
            $this->pdolink->exec('ROLLBACK TO SAVEPOINT trans'.($this->transactionCounter + 1));
            return true;
        }
        return $this->pdolink->rollback();
    }
    
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
     * bind les paramettres d'une requettes.
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
        $this->InTransaction(function(){
            $this->pdolink->exec('TRUNCATE TABLE variations_stock');
            $this->pdolink->exec('TRUNCATE TABLE elements_commande');
            $this->pdolink->exec('TRUNCATE TABLE variations_stock');
            $this->pdolink->exec('TRUNCATE TABLE commandes');
            $this->pdolink->exec('TRUNCATE TABLE produits');
            $this->pdolink->exec('TRUNCATE TABLE comptes');
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
                    actif 
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
                return null;
        
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
                WHERE actif = 1 
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
                        ':hash'=>$password
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
    public function Compte_Modifier_Actif($id_compte, $actif)
    {
        $id_compte=(int)$id_compte;
        $actif=(bool)$actif;
        
        return $this->InTransaction(function()use($id_compte, $actif){
            $this->exec(
                'UPDATE Comptes 
                SET actif = :actif 
                WHERE id_compte = :id_compte',
                [
                    ':id_compte'=>$id_compte,
                    ':actif'=>$actif
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
                'SELECT 
                    id_produit, 
                    nom, 
                    description 
                FROM produits 
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
                'SELECT 
                    id_produit, 
                    nom, 
                    description 
                FROM produits',
                []
            );
            return $produits ?: [];
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
    public function Produits_Creer($nom, $description)
    {
        $nom = mb_strimwidth((string)$nom,0,100);
        $description = mb_strimwidth((string)$description,0,500);
        
        return $this->InTransaction(function()use($nom, $description){
        
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
                return null;
        
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
                    :description
                )',
                [
                    ':nom'=>$nom,
                    ':description'=>$description
                ]
            );
        
            return $this->Produits_Recuperer($id_produit);
        });
    }
    
    /**
     * Liste tous les produits dont le stock prévisionnel est positif
     * @throws ErrorException 
     * @return Stock[]
     */
    public function StockPrevisionel_Lister()
    {
        return $this->InTransaction(function(){
            $stocks = $this->getAllObjects(
                'Stock',
                'SELECT 
                    prod.id_produit, 
                    prod.nom, 
                    prod.description, 
                    stocks.stock
                FROM produits as prod
                INNER JOIN stocks_previsionnel as stocks
                    ON prod.id_produit = stocks.id_produit
                WHERE stocks.stock > 0',
                []
            );
            return $stocks ?: [];
        });
    }
    
    /**
     * Liste tous les produits dont le stock est positif
     * @throws ErrorException 
     * @return Stock[]
     */
    public function Stock_Lister()
    {
        return $this->InTransaction(function(){
            $stocks = $this->getAllObjects(
                'Stock',
                'SELECT 
                    prod.id_produit, 
                    prod.nom, 
                    prod.description, 
                    stocks.stock
                FROM produits as prod
                INNER JOIN stocks as stocks
                    ON prod.id_produit = stocks.id_produit
                WHERE stocks.stock > 0',
                []
            );
            return $stocks ?: [];
        });
    }
    
    /**
     * Récupère le détail d'une variation de stock spécifique
     * @param int $id_variation_stock 
     * ID de la variation de stock à récupérer
     * @return VariationStock
     */
    public function VariationStock_Recuperer($id_variation_stock)
    {
        $id_variation_stock=(int)$id_variation_stock;
        
        return $this->InTransaction(function()use($id_variation_stock){
            $variation = $this->getSingleObject(
                'VariationStock',
                'SELECT 
                    id_variation_stock, 
                    id_produit, 
                    date_variation, 
                    variation, 
                    type_variation, 
                    remarque
                FROM variations_stock 
                WHERE id_variation_stock = :id_variation_stock',
                [
                    ':id_variation_stock'=>$id_variation_stock
                ]
            );
            return $variation;
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
     * @return VariationStock
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
            return $this->VariationStock_Recuperer($id_variation_stock);
        });
    }
    
    /**
     * Récupère une commande spécifique
     * @param int $id_commande 
     * ID de la commande à récupérer
     * @throws ErrorException 
     * @return Commande
     */
    public function Commande_Récupere($id_commande)
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
                'SELECT 
                    id_element_commande, 
                    id_commande, 
                    id_produit, 
                    quantite_commande, 
                    quantite_reel 
                FROM elements_commande 
                WHERE id_commande = :id_commande',
                [
                    ':id_commande'=>$id_commande
                ]
            );
            return $elements ?: [];
        });
    }
    
    /**
     * récupére l'ID du panier d'un compte, si le panier n'éxiste pas il est créer.
     * @param int $id_compte 
     * ID du compte dont veux veux récupéré l'ID de panier
     * @return int
     */
    private function PanierID_RecupererOuCreer($id_compte)
    {
        $id_compte=(int)$id_compte;
        
        return $this->InTransaction(function()use($id_compte){
            
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
                return $id_commande;
            
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
                
            return $id_commande;
        });
    }
    
    /**
     * Récupére le panier d'un compte.
     * @param int $id_compte 
     * ID du compte dont on veux obtenir la commande
     * @return Commande
     */
    public function Panier_Récuperer($id_compte)
    {   
        $id_compte=(int)$id_compte;
        
        return $this->InTransaction(function()use($id_compte){
            
            $id_commande = $this->PanierID_RecupererOuCreer($id_compte);
        
            return $this->Commande_Récupere($id_commande);
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
    public function Panier_Modifier_Element($id_compte,$id_produit,$quantite)
    {
        $quantite=(double)$quantite;
        $id_compte=(int)$id_compte;
        $id_produit=(int)$id_produit;
        
        return $this->InTransaction(function()use($id_compte,$id_produit,$quantite){
        
            $id_commande = $this->PanierID_RecupererOuCreer($id_compte);
            
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
                'SELECT 
                    id_element_commande, 
                    id_commande, 
                    id_produit, 
                    quantite_commande, 
                    quantite_reel 
                FROM elements_commande 
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
     * Liste les elements du panier d'un compte'
     * @param int $id_compte 
     * ID du compte dont on veux lister les éléments
     * @return ElementCommande[]
     */
    public function Panier_lister_Elements($id_compte)
    {
        $id_compte=(int)$id_compte;
        
        return $this->InTransaction(function()use($id_compte){
            
            $id_commande = $this->PanierID_RecupererOuCreer($id_compte);
            
            $elements = $this->getAllObjects(
                'ElementCommande',
                'SELECT 
                    id_element_commande, 
                    id_commande, 
                    id_produit, 
                    quantite_commande, 
                    quantite_reel 
                FROM elements_commande 
                WHERE id_commande = :id_commande',
                [
                    ':id_commande'=>$id_commande
                ]
            );
            return $elements ?: [];
        });
    }
    
    /**
     * Valide le panier d'un compte.
     * @param int $id_compte 
     * ID du compte dont on veux valider le panier
     * @return Commande
     */
    public function Panier_Valider($id_compte)
    {   
        $id_compte=(int)$id_compte;
        
        return $this->InTransaction(function()use($id_compte){
        
            $id_commande = $this->PanierID_RecupererOuCreer($id_compte);
            
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
            
            return $this->Commande_Récupere($id_commande);
        });
    }
    
    /**
     * Vide le pannier d'un compte.
     * @param int $id_compte 
     * ID du compte dont on veux vider le panier
     * @return Commande
     */
    public function Panier_Vider($id_compte)
    {   
        $id_compte=(int)$id_compte;
        
        return $this->InTransaction(function()use($id_compte){
        
            $id_commande = $this->PanierID_RecupererOuCreer($id_compte);
            
            //On valide la commande
            $this->exec(
                'DELETE elements_commande
                WHERE id_commande = :id_commande',
                [
                    ':id_commande'=>$id_commande
                ]
            );
            
            return $this->Commande_Récupere($id_commande);
        });
    }
}