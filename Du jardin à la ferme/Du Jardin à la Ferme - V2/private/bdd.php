<?php
require_once 'config.php';
require_once 'password.php';
require_once 'entities.php';

/**
 * bdd short summary.
 *
 * bdd description.
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
        $this->pdolink->exec("SET NAMES 'UTF8'");
    }
    
    private function bindValues(PDOStatement $statement, $parameters)
    {
        foreach($parameters as $name => $value )
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
    
    public function getAllObjects($class,$sql,$parameters)
    {
        $statement = $this->pdolink->prepare($sql);
        $this->bindValues($statement, $parameters);
        $statement->execute();
        $return_objects = $statement->fetchAll(PDO::FETCH_CLASS,$class);
        $statement->closeCursor();
        if(!$return_objects)$return_objects=null;
        return $return_objects;
    }
    
    public function getAll($sql,$parameters)
    {
        $statement = $this->pdolink->prepare($sql);
        $this->bindValues($statement, $parameters);
        $statement->execute();
        $return_objects = $statement->fetchAll();
        $statement->closeCursor();
        if(!$return_objects)$return_objects=null;
        return $return_objects;
    }
    
    public function getSingleObject($class,$sql,$parameters)
    {
        $statement = $this->pdolink->prepare($sql);
        $this->bindValues($statement, $parameters);
        $statement->execute();
        $return_object = $statement->fetchObject($class);
        $statement->closeCursor();
        if(!$return_object)$return_object=null;
        return $return_object;
    }
    
    public function getSingle($sql,$parameters)
    {
        $statement = $this->pdolink->prepare($sql);
        $this->bindValues($statement, $parameters);
        $statement->execute();
        $return_object = $statement->fetch();
        $statement->closeCursor();
        if(!$return_object)$return_object=null;
        return $return_object;
    }
    
    public function getScalar($sql,$parameters)
    {
        $statement = $this->pdolink->prepare($sql);
        $this->bindValues($statement, $parameters);
        $statement->execute();
        $return_object = $statement->fetchColumn(0);
        $statement->closeCursor();
        if(!$return_object)$return_object=null;
        return $return_object;
    }
    
    public function exec($sql,$parameters)
    {
        $statement = $this->pdolink->prepare($sql);
        $this->bindValues($statement, $parameters);
        $statement->execute();
        $rowCount = $statement->rowCount();
        $statement->closeCursor();
        return $rowCount;
    }
    
    public function execInsert($sql,$parameters)
    {
        $rowCount = $this->exec($sql,$parameters);
        $lastId=($rowCount>0)?$this->pdolink->lastInsertId():null;
        return $lastId;
    }
    
    public function CLEAR_ALL()
    {
        $this->pdolink->exec('TRUNCATE TABLE variations_stock');
        $this->pdolink->exec('TRUNCATE TABLE elements_commande');
        $this->pdolink->exec('TRUNCATE TABLE variations_stock');
        $this->pdolink->exec('TRUNCATE TABLE commandes');
        $this->pdolink->exec('TRUNCATE TABLE produits');
        $this->pdolink->exec('TRUNCATE TABLE comptes');
    }
    
    /**
     * Récupere un compte spécifique
     * @param int $id_compte 
     * @throws ErrorException 
     * @return Compte
     */
    public function Compte_Recuperer($id_compte)
    {
        $compte = $this->getSingleObject(
            'Compte',
            'SELECT 
                id_compte, 
                email, 
                date_creation, 
                actif 
            FROM comptes 
            WHERE id_compte = :id_compte',
            array(
                ':id_compte' => $id_compte
            )
        );
        return $compte;
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
        //on hash le mot de passe pour la sécurité
        $password = password_hash($password,PASSWORD_DEFAULT);
        
        // on vérifie si l'identifiant éxiste déjà
        $compte_exists = $this->getScalar(
            'SELECT COUNT(*) 
            FROM comptes 
            WHERE email = :email',
            array(
                ':email' => $email
            )
        );
        
        if($compte_exists>0)
            throw new ErrorException("Le compte éxiste déjà");
        
        //on insert le nouveau compte
        $id_compte = (int) $this->execInsert(
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
    }
    
    /**
     * Authentifie un compte et retourne les infos de celui-ci'
     * @param string $email 
     * @param string $password 
     * @throws ErrorException 
     * @return Compte
     */
    public function Compte_Authentifier($email, $password)
    {
        // on récupére le hash et l'id du compte
        $compte_infos = $this->getSingle(
            'SELECT 
                id_compte, 
                hash 
            FROM comptes 
            WHERE actif = 1 
            AND email = :email',
            array(
                ':email'=>$email
            )
        );
        
        $hash = $compte_infos['hash'];
        $id_compte = $compte_infos['id_compte'];
        
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
                array(
                    ':id_compte'=>$id_compte,
                    ':hash'=>$password
                )
            );
        }

        return $this->Compte_Recuperer($id_compte);
    }
    
    /**
     * Modifie l'état du compte'
     * @param int $id_compte 
     * @param bool $actif 
     * @throws ErrorException 
     * @return Compte
     */
    public function Compte_Modifier_Actif($id_compte, $actif)
    {
        $this->exec(
            'UPDATE Comptes 
            SET actif = :actif 
            WHERE id_compte = :id_compte',
            array(
                ':id_compte'=>$id_compte,
                ':actif'=>$actif
            )
        );
        
        return $this->Compte_Recuperer($id_compte);
    }
    
    /**
     * Récupére un produit spécifique
     * @param int $id_produit 
     * @throws ErrorException 
     * @return Produit
     */
    public function Produits_Recuperer($id_produit)
    {
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
    }
    
    /**
     * Liste tous les produits
     * @throws ErrorException 
     * @return array
     */
    public function Produits_Lister()
    {
        $produits = $this->getAllObjects(
            'Produit',
            'SELECT 
                id_produit, 
                nom, 
                description 
            FROM produits',
            array()
        );
        return $produits;
    }
    
    /**
     * Récupére un produit spécifique
     * @param string $nom 
     * @param string $description 
     * @throws ErrorException 
     * @return Produit
     */
    public function Produits_Ajouter($nom, $description)
    {
        // on vérifie si l'identifiant éxiste déjà
        $statement = $this->pdolink->prepare('
            SELECT COUNT(*) 
            FROM produits 
            WHERE nom = :nom
        ');
        $statement->bindValue(':nom',$nom,PDO::PARAM_STR);
        $statement->execute();
        if($statement->fetchColumn(0)>0)
            throw new ErrorException('Le produit existe déjà');
        $statement->closeCursor();
        
        //On créer le produit
        $statement = $this->pdolink->prepare('
            INSERT INTO produits 
            (
                nom, 
                description
            )
            VALUES
            (
                :nom,
                :description
            )
        ');
        $statement->bindValue(':nom',$nom,PDO::PARAM_STR);
        $statement->bindValue(':description',$description,PDO::PARAM_STR);
        $statement->execute();
        $id_produit = (int)$this->pdolink->lastInsertId();
        $statement->closeCursor();
        
        return $this->Produits_Recuperer($id_produit);
    }
    
    /**
     * Liste tous les produits dont le stock prévisionnel est positif
     * @throws ErrorException 
     * @return array
     */
    public function StockPrevisionel_Lister()
    {
        $statement = $this->pdolink->prepare('
            SELECT 
                prod.id_produit, 
                prod.nom, 
                prod.description, 
                stocks.stock
            FROM produits as prod
            INNER JOIN stocks_previsionnel as stocks
                ON prod.id_produit = stocks.id_produit
            WHERE stocks.stock > 0
        ');
        $statement->execute();
        $stocks = $statement->fetchAll(PDO::FETCH_CLASS,"Stock");
        $statement->closeCursor();
        return $stocks;
    }
    
    /**
     * Liste tous les produits dont le stock est positif
     * @throws ErrorException 
     * @return array
     */
    public function Stock_Lister()
    {
        $statement = $this->pdolink->prepare('
            SELECT 
                prod.id_produit, 
                prod.nom, 
                prod.description, 
                stocks.stock
            FROM produits as prod
            INNER JOIN stocks as stocks
                ON prod.id_produit = stocks.id_produit
            WHERE stocks.stock > 0
        ');
        $statement->execute();
        $stocks = $statement->fetchAll(PDO::FETCH_CLASS,"Stock");
        $statement->closeCursor();
        return $stocks;
    }
    
    /**
     * Summary of VariationsStock_Ajouter
     * @param int $id_produit 
     * @param double $variation 
     * @param string $type 
     * @param string $remarque 
     * @throws ErrorException 
     */
    public function VariationsStock_Ajouter($id_produit,$variation,$type,$remarque)
    {
        //On créer la variation
        $id_produit = $this->execInsert(
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
            array(
                ':id_produit'=>$id_produit,
                ':variation'=>$variation,
                ':type'=>$type,
                ':remarque'=>$remarque
            )
        )
    }
    
    /**
     * Summary of Commande_Récupere
     * @param int $id_commande 
     * @throws ErrorException 
     * @return Commande
     */
    public function Commande_Récupere($id_commande)
    {
        //On cherche la commande
        $statement = $this->pdolink->prepare('
            SELECT 
                id_commande, 
                id_compte, 
                date_creation, 
                remarque, 
                etat 
            FROM commandes 
            WHERE id_commande = :id_commande
        ');
        $statement->bindValue(':id_commande',$id_commande,PDO::PARAM_INT);
        $statement->execute();
        $commande = $statement->fetchObject("Commande");
        $statement->closeCursor();
        return $commande;
    }
    /**
     * Récupére la commande en cours de création du compte, ou en créer une nouvelle.
     * @param int $id_compte 
     * @return Commande
     */
    public function Commande_Récupéré_Creer_Creation($id_compte)
    {
        
        //On cherche une commande existante en création
        $statement = $this->pdolink->prepare('SELECT id_commande, id_compte, date_creation, remarque, etat FROM commandes WHERE id_compte = :id_compte AND etat = \'Création\' LIMIT 1');
        $statement->bindValue(':id_compte',$id_compte,PDO::PARAM_STR);
        $statement->execute();
        $commande = $statement->fetchObject("Commande");
        if($commande)
        {
            $statement->closeCursor();
            return $commande;
        }
        
        //On créer la commande
        $statement = $this->pdolink->prepare('INSERT INTO commandes (id_compte)VALUES(:id_compte)');
        $statement->bindValue(':id_compte',$id_compte,PDO::PARAM_INT);
        $statement->execute();
        if($statement->rowCount()!=1)
            throw new ErrorException('Création de la commande échoué');
        $id_commande = (int)$this->pdolink->lastInsertId();
        $statement->closeCursor();
        
        return $this->Commande_Récupere($id_commande);
        
    }
    
    /**
     * Liste les elements d'une commande'
     * @param int $id_commande 
     * @return array
     */
    public function Commande_lister_Elements($id_commande)
    {
        $statement = $this->pdolink->prepare('
            SELECT 
                id_element_commande, 
                id_commande, 
                id_produit, 
                quantite_commande, 
                quantite_reel 
            FROM elements_commande 
            WHERE id_commande = :id_commande
        ');
        $statement->bindValue(':id_commande',$id_commande,PDO::PARAM_INT);
        $statement->execute();
        $elements = $statement->fetchAll(PDO::FETCH_CLASS,"ElementCommande");
        $statement->closeCursor();
        return $elements;
    }
    /**
     * Summary of Commande_Modifier_ElementQuantite
     * @param int $id_commande 
     * @param int $id_produit 
     * @param float $quantite 
     * @return ElementCommande
     */
    public function Commande_Modifier_ElementQuantite($id_commande,$id_produit,$quantite)
    {
        
        //On créer le produit
        $statement = $this->pdolink->prepare('
            INSERT INTO elements_commande 
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
                quantite_commande = :quantite_commande
        ');
        $statement->bindValue(':id_commande',$id_commande,PDO::PARAM_INT);
        $statement->bindValue(':id_produit',$id_produit,PDO::PARAM_INT);
        $statement->bindValue(':quantite_commande',$quantite,PDO::PARAM_STR);
        $statement->execute();
        $statement->closeCursor();
        
        $statement = $this->pdolink->prepare('
            SELECT 
                id_element_commande, 
                id_commande, 
                id_produit, 
                quantite_commande, 
                quantite_reel 
            FROM elements_commande 
            WHERE id_commande = :id_commande
            AND id_produit = :id_produit
        ');
        $statement->bindValue(':id_commande',$id_commande,PDO::PARAM_INT);
        $statement->bindValue(':id_produit',$id_produit,PDO::PARAM_INT);
        $statement->execute();
        $element = $statement->fetchObject("ElementCommande");
        $statement->closeCursor();
        return $element;
    }
}
