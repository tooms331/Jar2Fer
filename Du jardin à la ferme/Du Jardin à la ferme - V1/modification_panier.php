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



try
{
	$bdd = new PDO ('mysql:host=localhost;dbname=djalf', 'root', '');
}
	catch (Exception $e)
{
	die('Erreur : ' . $e->getMessage ());
}
	$variete_id = $_POST['id_produit'] * 1 ;
	$quantite_modifie = $_POST['quantite_modifie'] * 1;
	
	$reponse = $bdd->query("SELECT * FROM a_vendre_commune where id = '".$variete_id."'");
while($donnees=$reponse->fetch())
    {
		$max = $donnees['quantite_disponible'];
	}
	
// modification quantite_commande dans panier
	//reperage du produit
	$positionproduit = array_search($variete_id,  $_SESSION['panier']['id_produit']);
	$positionProduit = array_search($variete_id,  $_SESSION['a_vendre']['id_produit']);
	// si la quantite est identique
		if ($quantite_modifie != $_SESSION['panier']['quantite_commande'][$positionproduit])
	{
		//si la quantité est augmenté
		if ($quantite_modifie > $_SESSION['panier']['quantite_commande'][$positionproduit])
		{
			$_SESSION['panier']['quantite_commande'][$positionproduit] = $quantite_modifie ;
			$_SESSION['a_vendre']['quantite_disponible'][$positionProduit] = $max - $quantite_modifie ;
		}
		
		// si la quantité est diminué
		else
		{
			$_SESSION['panier']['quantite_commande'][$positionproduit] = $quantite_modifie ;
			$_SESSION['a_vendre']['quantite_disponible'][$positionProduit] = $max - $quantite_modifie ;
			
		}
		
		// modification total dans panier
			$tarif = $_SESSION['panier']['tarif'][$positionproduit];
			$qtte = $_SESSION['panier']['quantite_commande'][$positionproduit];
			$_SESSION['panier']['total'][$positionproduit] = $tarif * $qtte;
	
	
	}
	header('Location: panier.php');
?>
</body>
</html>	 

