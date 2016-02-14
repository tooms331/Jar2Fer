<?php
require_once './private/config.php';
require_once './private/bdd.php';

header('Content-Type: text/plain; charset:utf-8');

$bdd = new BDD();
$bdd->CLEAR_ALL();

$compte = $bdd->Compte_Creer("rom.laurent@hotmail.fr","vlroro87*");
echo json_encode($compte,JSON_PRETTY_PRINT| JSON_UNESCAPED_UNICODE);

$compte = $bdd->Compte_Authentifier("rom.laurent@hotmail.fr","vlro87*");
echo json_encode($compte,JSON_PRETTY_PRINT| JSON_UNESCAPED_UNICODE);

$compte = $bdd->Compte_Authentifier("rom.laurent@hotmail.fr","vlroro87*");
echo json_encode($compte,JSON_PRETTY_PRINT| JSON_UNESCAPED_UNICODE);

$compte = $bdd->Compte_Modifier_Actif($compte->id_compte, false);
echo json_encode($compte,JSON_PRETTY_PRINT| JSON_UNESCAPED_UNICODE);

$compte = $bdd->Compte_Modifier_Actif($compte->id_compte, true);
echo json_encode($compte,JSON_PRETTY_PRINT| JSON_UNESCAPED_UNICODE);

$tomate_cerise = $bdd->Produits_Creer("Tomate cerise","Des toute pitite tomat'");
echo json_encode($tomate_cerise,JSON_PRETTY_PRINT| JSON_UNESCAPED_UNICODE);

$concombre = $bdd->Produits_Creer("concombre","concomb'");
echo json_encode($concombre,JSON_PRETTY_PRINT| JSON_UNESCAPED_UNICODE); 

$variation = $bdd->VariationsStock_Ajouter($tomate_cerise->id_produit, 10, 'RECOLTE','Première récolte :-)');
echo json_encode($variation,JSON_PRETTY_PRINT| JSON_UNESCAPED_UNICODE);

$variation = $bdd->VariationsStock_Ajouter($concombre->id_produit, 15, 'RECOLTE','le concombre masqué!');
echo json_encode($variation,JSON_PRETTY_PRINT| JSON_UNESCAPED_UNICODE);

$commande = $bdd->Commande_Récupéré_EnCreation($compte->id_compte);
echo json_encode($commande,JSON_PRETTY_PRINT| JSON_UNESCAPED_UNICODE);

$StockPrevisionel = $bdd->StockPrevisionel_Lister();
echo json_encode($StockPrevisionel,JSON_PRETTY_PRINT| JSON_UNESCAPED_UNICODE);

$elementcommande = $bdd->Commande_Modifier_ElementQuantite($commande->id_commande,$tomate_cerise->id_produit,2);
echo json_encode($elementcommande,JSON_PRETTY_PRINT| JSON_UNESCAPED_UNICODE);

$elementcommande = $bdd->Commande_Modifier_ElementQuantite($commande->id_commande,$concombre->id_produit,4);
echo json_encode($elementcommande,JSON_PRETTY_PRINT| JSON_UNESCAPED_UNICODE);

$StockPrevisionel = $bdd->StockPrevisionel_Lister();
echo json_encode($StockPrevisionel,JSON_PRETTY_PRINT| JSON_UNESCAPED_UNICODE);

$elementcommande = $bdd->Commande_Modifier_ElementQuantite($commande->id_commande, $tomate_cerise->id_produit, 5);
echo json_encode($elementcommande,JSON_PRETTY_PRINT| JSON_UNESCAPED_UNICODE);

$StockPrevisionel = $bdd->StockPrevisionel_Lister();
echo json_encode($StockPrevisionel,JSON_PRETTY_PRINT| JSON_UNESCAPED_UNICODE);

$elementscommande = $bdd->Commande_lister_Elements($commande->id_commande);
echo json_encode($elementscommande,JSON_PRETTY_PRINT| JSON_UNESCAPED_UNICODE);

?>