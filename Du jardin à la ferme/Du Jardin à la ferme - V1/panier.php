<?php
// test si session ouverte
// On prolonge la session
session_start();
// On teste si la variable de session existe et contient une valeur
if(isset($_SESSION['panier'])) {
	
	  
	  
	  echo 'le panier existe' ;
	  echo $_SESSION['id_client'];


}
else
{
    $panier_existe = false;
	header('Location: identification.php');
}
?>


<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset="utf-8"/>
		<link rel="stylesheet" href="style.css" />
		<title>Panier</title> <!--Ceci est le titre sur le navigateur-->
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



	$nbArticles=count($_SESSION['panier']['produit']);
		if ($nbArticles <= 0) // le panier est vide
		
		{
			
			echo "<tr><td>Votre panier est vide </ td></tr>";
			echo $nbArticles;
			echo $_SESSION['panier']['id_produit'][0];
		}
		else
		{
			echo $_SESSION['panier']['id_produit'][0];
			echo $nbArticles;
			for ($i=0 ;$i < $nbArticles ; $i++)
			{
	
	
	

	// test unite de vente
	
$variete_id= htmlspecialchars($_SESSION['panier']['id_produit'][$i]);
$unite = $bdd->query("SELECT unite_vente FROM a_vendre_commune where id = '".$variete_id."'");
$unite_vente = $unite->fetch();
$unit= htmlspecialchars($_SESSION['panier']['unite_vente'][$i]);
$test="Kg";
		
		
		if (strcmp($unit,$test)!==1)
	{ // vente au kilo	
	
$variete_id= htmlspecialchars($_SESSION['panier']['id_produit'][$i]);
$rep = $bdd->query("SELECT * FROM a_vendre_commune where id = '".$variete_id."'");
while($don = $rep->fetch())
{$maxi = $don['quantite_disponible'];} // recupere la quantite max disponible
$rep->closeCursor();


	
?>	
	<p>

	
	<table Border="1" CELLPADDING="5">
	<tr>
	<th WIDTH="120px">Produit</th>
	<th WIDTH="120px">Variété</th>
	<th WIDTH="120px">Quantité</th>
	<th WIDTH="80px">Tarif</th>
	<th WIDTH="80px">Total</th>
	<th WIDTH="100px">Supprimer</th>
	
	</tr>
	
	<tr>
	<td><?php echo htmlspecialchars($_SESSION['panier']['produit'][$i]);?></td>
	<td WIDTH="50"><?php echo htmlspecialchars($_SESSION['panier']['variete'][$i]);?></td>
	<td WIDTH="40"><form action="modification_panier.php" method="post">
	<input name="quantite_modifie" style="width:50px" class="ajustement" type="number" min="0.1" step="0.1" max=<?php echo $maxi;?> value=<?php echo htmlspecialchars($_SESSION['panier']['quantite_commande'][$i]);?>> <?php echo htmlspecialchars($_SESSION['panier']['unite_vente'][$i]);?>
	<input type="hidden" name="id_produit" value= <?php echo htmlspecialchars($_SESSION['panier']['id_produit'][$i]);?>  >
	<input style="width:20px" type="image" src="image/site/rafraichir.jpg" ></code> 
	</form></td>
	<td WIDTH="30"><?php echo htmlspecialchars($_SESSION['panier']['tarif'][$i]);?> €</td>
	<td WIDTH="30"> <?php echo htmlspecialchars($_SESSION['panier']['total'][$i]) ;?> €</td>
	<td WIDTH="40"><form action="correction_panier.php" method="post"> 
			
			<input type="hidden" name="id_produit" value= <?php echo htmlspecialchars($_SESSION['panier']['id_produit'][$i]);?>  >
		<input style="width:20px" type="image" src="image/site/poubelle.jpg" ></code> 	
		</form></td>
	
	</tr>
	<table><br/>
<p>
	
	

	<?php
}
else
 // vente a l'unité
 {
$variete_id= htmlspecialchars($_SESSION['panier']['id_produit'][$i]);
$rep = $bdd->query("SELECT * FROM a_vendre_commune where id = '".$variete_id."'");
while($don = $rep->fetch())
{$maxi = $don['quantite_disponible'];} // recupere la quantite max disponible

$rep->closeCursor();

	
?>	
	<p>

	
	<table Border="1" CELLPADDING="5">
	<tr>
	<th WIDTH="120px">Produit</th>
	<th WIDTH="120px">Variété</th>
	<th WIDTH="120px">Quantité</th>
	<th WIDTH="80px">Tarif</th>
	<th WIDTH="80px">Total</th>
	<th WIDTH="100px">Supprimer</th>
	
	</tr>
	
	<tr>
	<td><?php echo htmlspecialchars($_SESSION['panier']['produit'][$i]);?></td>
	<td WIDTH="50"><?php echo htmlspecialchars($_SESSION['panier']['variete'][$i]);?></td>
	<td WIDTH="40"><form action="modification_panier.php" method="post">
	<input name="quantite_modifie" style="width:50px" class="ajustement" type="number" min="1" step="1" max=<?php echo $maxi;?> value=<?php echo htmlspecialchars($_SESSION['panier']['quantite_commande'][$i]);?>> <?php echo htmlspecialchars($_SESSION['panier']['unite_vente'][$i]);?>
	<input type="hidden" name="id_produit" value= <?php echo htmlspecialchars($_SESSION['panier']['id_produit'][$i]);?>  >
	<input style="width:20px" type="image" src="image/site/rafraichir.jpg" ></code> 
	</form></td>
	<td WIDTH="30"><?php echo htmlspecialchars($_SESSION['panier']['tarif'][$i]);?> €</td>
	<td WIDTH="30">  <?php echo htmlspecialchars($_SESSION['panier']['total'][$i]) ;?> €</td>
	<td WIDTH="40"><form action="correction_panier.php" method="post"> 
			
			<input type="hidden" name="id_produit" value= <?php echo htmlspecialchars($_SESSION['panier']['id_produit'][$i]);?>  >
		<input style="width:20px" type="image" src="image/site/poubelle.jpg" ></code> 	
		</form></td>
	
	</tr>
	<table><br/>
<p>
	
	

	<?php	 
	 
 }
			}



	

	
	
	
	

	// Affichage total commande


	//Bouton retour liste produit et bouton validation
?>
<p>
	<form action="produit_disponible.php" method="post"> 
			
		<input type="submit" value="Retour à la liste produit" ></code> 
	</form>
		</p>
		
	<form action="envoi_commande.php" method="post">
	<input type="submit" value="Valider ma commande"></code>
	
<?php
}


?>	

</body>
</html>	 



