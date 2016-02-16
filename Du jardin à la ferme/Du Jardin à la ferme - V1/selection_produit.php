<?php
// test si session ouverte
// On prolonge la session
session_start();
// On teste si la variable de session existe et contient une valeur
if(isset($_SESSION['panier'])) {
	 
	  
	  $panier_existe = true;
	  echo 'le panier existe' ;


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
		<title>Produits disponibles</title> <!--Ceci est le titre sur le navigateur-->
	</head>
	<body>




<?php

// appel des produits disponibles


try
{
	$bdd = new PDO ('mysql:host=localhost;dbname=djalf', 'root', '');
}
catch (Exception $e)
{
	die('Erreur : ' . $e->getMessage ());
}
$variete_id=$_POST['variete_id'] * 1;
$reponse = $bdd->query("SELECT * FROM a_vendre where id = '".$variete_id."'");




while($donnees = $reponse->fetch())
{
	?>
<p>
	
	<table>
	<tr>
	<td>Produit</td>
	<td>Variété</td>
	<td>Tarif</td>
	<td>Unité de vente</td>
	</tr>
	
	<tr>
	<td><?php echo $donnees['produit'];?></td>
	<td><?php echo $donnees['variete'];?></td>
	<td><?php echo $donnees['tarif'];?></td>
	<td><?php echo $donnees['unite_vente'];?></td>
	</tr>
	<table><br/>
<?php
	//Si le produit existe déjà on modifie la quantité max
      $positionProduit = array_search($variete_id,  $_SESSION['panier']['id_produit']);

      if ($positionProduit !== false)
	  {$maxi = $donnees['quantite_disponible'] - $_SESSION['panier']['quantite_commande'][$positionProduit]; }
		else
	  {$maxi = $donnees['quantite_disponible'];}
  ?>
	<strong>Quantité maximale : <?php echo $maxi;?></strong>

	<?php
	
	
$reponse->closeCursor ();

$unite = $bdd->query("SELECT unite_vente FROM a_vendre where id = '".$variete_id."'");
$unite_vente = $unite->fetch();
$unit= ($unite_vente['unite_vente']);
$test="Kg";
		
		
		if (strcmp($unit,$test)!==1)	
	
{ 
			$unite->closeCursor ();
				
	// formulaire affichage produit
	$max = $bdd->query("SELECT quantite_disponible FROM a_vendre where id = '".$variete_id."'");
$m = $max->fetch();
$maxi = $m['quantite_disponible'];
	?>
	<form action="calcul_panier.php" method="post"> 
	    <p>
			<label for="commande" >Quantité</label> : <input type="number" step="0.1" min="0.2" max=<?php echo $maxi;?> name="entier" id="entier" style="width: 40px; height:20px ;" />
			<?php 
			
				$unite = $bdd->query("SELECT unite_vente FROM a_vendre where id = '".$variete_id."'");
				$unite_vente = $unite->fetch();
				$unit= ($unite_vente['unite_vente']);     // ajout unit� vente sur formulaire
					echo $unite_vente['unite_vente'];?>
				<?php	// ajout formulaire cach�
				$reponse = $bdd->query("SELECT id FROM a_vendre where id = '".$variete_id."'");
				$donnees = $reponse->fetch(); ?>
				
				<input type="hidden" name="variete_id" value= <?php echo $donnees['id'];?>  >
				
	    </p>
			<input type="submit" value="Ajouter au panier" ></code> 
</p>
</form>


	
<?php
$reponse->closeCursor();
}
          else
{
$max = $bdd->query("SELECT quantite_disponible FROM a_vendre where id = '".$variete_id."'");
$m = $max->fetch();
$maxi = $m['quantite_disponible'];
	?>
	<form action="calcul_panier.php" method="post">
		<p>
			<label for="commande">Quantité</label> : <input type="number" step="1" min="1" max=<?php echo $maxi;?> name="entier" id="entier" style="width: 40px; height:20px ;" />
			<?php 
			
				$unite = $bdd->query("SELECT unite_vente FROM a_vendre where id = '".$variete_id."'");
				$unite_vente = $unite->fetch();
				$unit= ($unite_vente['unite_vente']); // ajout unit� vente sur formulaire
					echo $unite_vente['unite_vente'];?>
			<?php	// ajout formulaire cach�
				$reponse = $bdd->query("SELECT id FROM a_vendre where id = '".$variete_id."'");
				$donnees = $reponse->fetch(); ?>
				<input type="hidden" name="variete_id" value= <?php echo $donnees['id'];?>  >	
				
		 </p> 
			<input type="submit" value="Ajouter au panier" ></code> unité(s)
</p>
</form>
	<?php				
}	
$unite->closeCursor ();
}
// bouton retour
?>  
<p>
<form action="produit_disponible.php" method="post">	

<input type="submit" value="Retour" ></code>	
</p>

</body>
</html>	 

