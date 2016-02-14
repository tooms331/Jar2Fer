<?php 
require_once './private/config.php';
require_once './private/bdd.php';

header('Content-Type: text/plain; charset:utf-8');

function echoJSON($obj)
{
    echo json_encode($obj,JSON_PRETTY_PRINT| JSON_UNESCAPED_UNICODE);
}

$bdd = new BDD();

echo "\n\nNétoyage de la BDD\n";
$bdd->CLEAR_ALL();

echo "\n\nCreation d'un compte\n";
$compte = $bdd->Compte_Creer("rom.laurent@hotmail.fr","vlroro87*");
echoJSON($compte);

echo "\n\nAuthentification d'un compte (echec)\n";
$compte = $bdd->Compte_Authentifier("rom.laurent@hotmail.fr","vlro87*");
echoJSON($compte);

echo "\n\nAuthentification d'un compte (succes)\n";
$compte = $bdd->Compte_Authentifier("rom.laurent@hotmail.fr","vlroro87*");
echoJSON($compte);

echo "\n\nDésactivation d'un compte\n";
$compte = $bdd->Compte_Modifier_Actif($compte->id_compte, false);
echoJSON($compte);

echo "\n\nActivation d'un compte\n";
$compte = $bdd->Compte_Modifier_Actif($compte->id_compte, true);
echoJSON($compte);

echo "\n\nCréation d'un produit\n";
$tomate_cerise = $bdd->Produits_Creer("Tomate cerise","Des toute pitite tomat'");
echoJSON($tomate_cerise);

echo "\n\nCréation d'un second produit\n";
$concombre = $bdd->Produits_Creer("concombre","concomb'");
echoJSON($concombre); 

echo "\n\nVariation de stock du premier produit\n";
$variation = $bdd->VariationStock_Ajouter($tomate_cerise->id_produit, 10, 'RECOLTE','Première récolte :-)');
echoJSON($variation);

echo "\n\nVariation de stock du second produit\n";
$variation = $bdd->VariationStock_Ajouter($concombre->id_produit, 15, 'RECOLTE','nos premier concombre! un peu chétifs mais vendables!');
echoJSON($variation);

echo "\n\nRécupération des stocks\n";
$StockPrevisionel = $bdd->Stock_Lister();
echoJSON($StockPrevisionel);

echo "\n\nVariation de stock du premier produit\n";
$variation = $bdd->VariationStock_Ajouter($tomate_cerise->id_produit, -2, 'PERTE','un rongeur est passé par la :-(');
echoJSON($variation);

echo "\n\nRécupération des stocks\n";
$StockPrevisionel = $bdd->Stock_Lister();
echoJSON($StockPrevisionel);

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

echo "\n\nRécupération des stocks prévisionnels\n";
$StockPrevisionel = $bdd->StockPrevisionel_Lister();
echoJSON($StockPrevisionel);

echo "\n\ncommande de 5u du premier produit\n";
$elementcommande = $bdd->Panier_Modifier_Element($compte->id_compte, $tomate_cerise->id_produit, 5);
echoJSON($elementcommande);

echo "\n\ncommande de 0u du second produit\n";
$elementcommande = $bdd->Panier_Modifier_Element($compte->id_compte, $concombre->id_produit, 0);
echoJSON($elementcommande);

echo "\n\nliste des éléments de la commande\n";
$elementscommande = $bdd->Panier_lister_Elements($compte->id_compte);
echoJSON($elementscommande);

echo "\n\nRécupération des stocks prévisionnels\n";
$StockPrevisionel = $bdd->StockPrevisionel_Lister();
echoJSON($StockPrevisionel);

echo "\n\nValidation de la commande\n";
$StockPrevisionel = $bdd->Panier_Valider($compte->id_compte);
echoJSON($StockPrevisionel);

?>