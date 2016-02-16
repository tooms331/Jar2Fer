<?php 
require_once './private/config.php';
require_once './private/bdd.php';
require_once './private/unittest.php';

header('Content-Type: text/plain; charset:utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Expires: 0');
header('Pragma: no-cache'); 
header('Access-Control-Allow-Origin: *');

$UT_BDD = new UnitTest('Crèation du lien BDD',[],function (){
    return new BDD(); 
});
$UT_BDD->RunCheck();

$UT_CLEARBDD = new UnitTest('Vidage de la BDD',[$UT_BDD],function ($bdd){
    $bdd->CLEAR_ALL();
    return $bdd;
});
$UT_CLEARBDD->RunCheck();

$UT_CreerCompteInvalidEmail = new UnitTest('Création d\'un compte (email invalide)',[$UT_CLEARBDD],function ($bdd){
    $compte=null;
    try{
        $compte = $bdd->Compte_Creer("rom.laurenthotmail.fr","vlroro87*");
        throw new ErrorException ("la création ne doit pas réussir!");
    }
    catch(Exception $ex)
    {
        return $compte;
    }
});
$UT_CreerCompteInvalidEmail->RunCheck();

$UT_CreerCompte = new UnitTest('Création d\'un compte',[$UT_CLEARBDD],function ($bdd){
    $compte = $bdd->Compte_Creer("rom.laurent@hotmail.fr","vlroro87*");
    if($compte->id_compte!==1)
        throw new ErrorException ("L'id du compte est incorrect");
    if($compte->email!=='rom.laurent@hotmail.fr')
        throw new ErrorException ("L'email du compte est incorrect");
    if($compte->etat!=='Nouveau')
        throw new ErrorException ("Le compte doit être activé");
    return $compte;
});
$UT_CreerCompte->RunCheck();

$UT_CreerCompteExistant = new UnitTest('Création d\'un compte Existant',[$UT_CLEARBDD,$UT_CreerCompte],function ($bdd){
    $compte=null;
    try{
        $compte = $bdd->Compte_Creer("rom.laurenthotmail.fr","vlroro");
        throw new ErrorException ("la création ne doit pas réussir!");
    }
    catch(Exception $ex)
    {
        return $compte;
    }
});
$UT_CreerCompteExistant->RunCheck();

$UT_InvalidAuth = new UnitTest('Authentification d\'un compte (echec)',[$UT_CLEARBDD,$UT_CreerCompte],function ($bdd,$compte){
    try{
        $compte = $bdd->Compte_Authentifier("rom.laurent@hotmail.fr","vlro87*");
        throw new ErrorException ("L'authentification ne doit pas réussir");
    }
    catch(Exception $ex)
    {
        return $compte;
    }
});
$UT_InvalidAuth->RunCheck();


$UT_ValidAuth = new UnitTest('Authentification d\'un compte (succes)',[$UT_CLEARBDD,$UT_CreerCompte],function ($bdd,$compte){
    $compte = $bdd->Compte_Authentifier("rom.laurent@hotmail.fr","vlroro87*");
    if($compte===null)
        throw new ErrorException ("L'authentification doit réussir");
    if($compte->id_compte!==1)
        throw new ErrorException ("L'id du compte est incorrect");
    if($compte->email!=='rom.laurent@hotmail.fr')
        throw new ErrorException ("L'email du compte est incorrect");
    return $compte;
});
$UT_ValidAuth->RunCheck();


$UT_DésactiveCompte = new UnitTest('Modification de l\'état d\'un compte : Désactivé',[$UT_CLEARBDD,$UT_CreerCompte],function ($bdd,$compte){
    $compte = $bdd->Compte_Modifier_Etat($compte->id_compte,'Désactivé');
    if($compte->etat!=='Désactivé')
        throw new ErrorException ("Le compte doit être Désactivé");
    return $compte;
});
$UT_DésactiveCompte->RunCheck();

$UT_AdminCompte = new UnitTest('Modification de l\'état d\'un compte : Admin',[$UT_CLEARBDD,$UT_CreerCompte],function ($bdd,$compte){
    $compte = $bdd->Compte_Modifier_Etat($compte->id_compte,'Admin');
    if($compte->etat!=='Admin')
        throw new ErrorException ("Le compte doit être Admin");
    return $compte;
});
$UT_AdminCompte->RunCheck();

$UT_InvildEtatCompte = new UnitTest('Modification de l\'état d\'un compte : Admin',[$UT_CLEARBDD,$UT_CreerCompte],function ($bdd,$compte){
    
    try{
        $compte = $bdd->Compte_Modifier_Etat($compte->id_compte,'BAD');
        throw new ErrorException ("La modification ne doit pas réussir");
    }
    catch(Exception $ex)
    {
        return $compte;
    }
});
$UT_InvildEtatCompte->RunCheck();

$UT_CreerProduitTomates = new UnitTest('Création d\'un produit : Tomates cerise',[$UT_CLEARBDD],function ($bdd,$compte){
    $produit = $bdd->Produits_Creer("Tomate cerise","Des toute pitite tomat'");
    if(!$produit)
        throw new ErrorException ("Le produit doit être créer");
    return $produit;
});
$UT_CreerProduitTomates->RunCheck();

$UT_CreerProduitConcombre = new UnitTest('Création d\'un produit : concombre',[$UT_CLEARBDD],function ($bdd,$compte){
    $produit = $bdd->Produits_Creer("Concombre","concombre masqué");
    if(!$produit)
        throw new ErrorException ("Le produit doit être créer");
    return $produit;
});
$UT_CreerProduitConcombre->RunCheck();

$UT_VariationStockTomate = new UnitTest('Variation du stock de Tomates',[$UT_CLEARBDD,$UT_CreerProduitTomates],function ($bdd,$tomate_cerise){
    $variation = $bdd->VariationStock_Ajouter($tomate_cerise->id_produit, 10, 'RECOLTE','Première récolte :-)');
    if(!$variation)
        throw new ErrorException ("la variation de stock doit être créer");
    return $variation;
});
$UT_VariationStockTomate->RunCheck();

$UT_VariationStockConcombre = new UnitTest('Variation du stock de Concombre',[$UT_CLEARBDD,$UT_CreerProduitConcombre],function ($bdd,$concombre){
    $variation = $bdd->VariationStock_Ajouter($concombre->id_produit, 15, 'RECOLTE','nos premier concombre! un peu chétifs mais vendables!');
    if(!$variation)
        throw new ErrorException ("la variation de stock doit être créer");
    return $variation;
});
$UT_VariationStockConcombre->RunCheck();

$UT_ListerStock = new UnitTest('Liste du stock',[$UT_CLEARBDD,$UT_VariationStockTomate,$UT_VariationStockConcombre],function ($bdd){
    $Stock = $bdd->Stock_Lister();
    if(!$Stock)
        throw new ErrorException ("le stock doit exister");
    if(count($Stock)!=2)
        throw new ErrorException ("le stock contenir 2 produits");
    return $Stock;
});
$UT_ListerStock->RunCheck();

/*

echo "\n\nRécupération/Création de la commande en création du compte\n";
$commande = $bdd->Panier_Récuperer($compte->id_compte);
echoJSON($commande);

echo "\n\ncommande de 2u du premier produit\n";
$elementcommande = $bdd->Panier_Modifier_Element($compte->id_compte,$tomate_cerise->id_produit,2);
echoJSON($elementcommande);

echo "\n\ncommande de 4u du second produit\n";
$elementcommande = $bdd->Panier_Modifier_Element($compte->id_compte,$concombre->id_produit,4);
echoJSON($elementcommande);

echo "\n\nliste des éléments de la commande\n";
$elementscommande = $bdd->Panier_lister_Elements($compte->id_compte);
echoJSON($elementscommande);

echo "\n\ncommande de 5u du premier produit\n";
$elementcommande = $bdd->Panier_Modifier_Element($compte->id_compte, $tomate_cerise->id_produit, 5);
echoJSON($elementcommande);

echo "\n\ncommande de 0u du second produit\n";
$elementcommande = $bdd->Panier_Modifier_Element($compte->id_compte, $concombre->id_produit, 0);
echoJSON($elementcommande);

echo "\n\nliste des éléments du panier\n";
$elementscommande = $bdd->Panier_lister_Elements($compte->id_compte);
echoJSON($elementscommande);

echo "\n\nRécupération des stocks prévisionnels\n";
$StockPrevisionel = $bdd->StockPrevisionel_Lister();
echoJSON($StockPrevisionel);

echo "\n\nValidation du panier\n";
$StockPrevisionel = $bdd->Panier_Valider($compte->id_compte);
echoJSON($StockPrevisionel);

echo "\n\nRécupération des stocks prévisionnels\n";
$StockPrevisionel = $bdd->StockPrevisionel_Lister();
echoJSON($StockPrevisionel);

echo "\n\nRécupération des commandes Valides\n";
$CommandesValides = $bdd->Commande_Lister_Valide();
echoJSON($CommandesValides);
*/