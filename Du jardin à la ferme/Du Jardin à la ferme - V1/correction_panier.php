<?php
session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset="utf-8"/>
		<link rel="stylesheet" href="style.css" />
		<title>Produits disponibles</title> <!--Ceci est le titre sur le navigateur-->
	</head>
	<body>


<?php

	$variete_id = $_POST['id_produit'] * 1 ;
// creation panier temporaire
$tmp=array();
     $tmp['id_produit'] = array();
     $tmp['identification_client'] = array();
	 $tmp['produit'] = array();
     $tmp['variete'] = array();
     $tmp['quantite_commande'] = array();
	 $tmp['tarif'] = array();
	 $tmp['unite_vente'] = array();
	 $tmp['total'] = array();
	 
	// affectation de toute les valeurs du panier sauf le produit supprimé

		for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++)
		{
         if ($_SESSION['panier']['id_produit'][$i] !== $variete_id)
         {
            array_push( $tmp['id_produit'],$_SESSION['panier']['id_produit'][$i]);
            array_push( $tmp['identification_client'],$_SESSION['panier']['identification_client'][$i]);
            array_push( $tmp['produit'],$_SESSION['panier']['produit'][$i]);
			array_push( $tmp['variete'],$_SESSION['panier']['variete'][$i]);
			array_push( $tmp['quantite_commande'],$_SESSION['panier']['quantite_commande'][$i]);
			array_push( $tmp['tarif'],$_SESSION['panier']['tarif'][$i]);
			array_push( $tmp['unite_vente'],$_SESSION['panier']['unite_vente'][$i]);
			array_push( $tmp['total'],$_SESSION['panier']['total'][$i]);
         }

      }	
	  //On remplace le panier en session par notre panier temporaire à jour
      $_SESSION['panier'] =  $tmp;
      //On efface notre panier temporaire
      unset($tmp);
	 // on remet la quantite dans la table a vendre 
	try
{
	$bdd = new PDO ('mysql:host=localhost;dbname=djalf', 'root', '');
}
	catch (Exception $e)
{
	die('Erreur : ' . $e->getMessage ());
}
	
	
	$reponse = $bdd->query("SELECT * FROM a_vendre_commune where id = '".$variete_id."'");
while($donnees=$reponse->fetch())
    {
		$max = $donnees['quantite_disponible'];
	}	
	$positionProduit = array_search($variete_id,  $_SESSION['a_vendre']['id_produit']);
	$_SESSION['a_vendre']['quantite_disponible'][$positionProduit] = $max ;
		
header('Location: panier.php');
?>
</body>
</html>	 