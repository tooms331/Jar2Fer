<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset="utf-8"/>
		<link rel="stylesheet" href="style.css" />
	</head>
	<body>
		<p>Veuillez entrer le mot de passe qui vous a été fourni pour accéder aux commandes en ligne :</p>
		<form action="produit_disponible.php" method="post">
			<p>
			<input type="password" name="mot_de_passe" />
			<input type="submit" value="Valider" />
			</p>
		</form>
	<?php // supression table panier
	
	try
{
	$bdd = new PDO ('mysql:host=localhost;dbname=djalf', 'root', '');
}
catch (Exception $e)
{
	die('Erreur : ' . $e->getMessage ());
}


			// test si variable panier_existe = true or false

			$sql = 'SHOW TABLES FROM djalf LIKE "panier"';
			$req = $bdd->query($sql);

		if($req->rowCount() > 0)
	{ 
			$panier_existe = true;
	}
		else
	{
			$panier_existe = false;
	}
			$req->closeCursor();

// effacement table panier si existante

		if ($panier_existe == true)
{
		try
	{
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	
	$sql ="DROP  TABLE panier ";
	 
    // use exec() because no results are returned
    $bdd->exec($sql);
  
	}  
catch(PDOException $er)
	{
	echo $sql."<br>".$er->getMessage();
	}	
}
	
		try
	{
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	
	$sql ="TRUNCATE  TABLE a_vendre ";
	 
    // use exec() because no results are returned
    $bdd->exec($sql);
  
	}  
catch(PDOException $er)
	{
	echo $sql."<br>".$er->getMessage();
	}	

	
	// remplir table a_vendre a partir de a_vendre_commune
	
	$reponse = $bdd->query("SELECT * FROM a_vendre_commune" );
    
	while($donnees = $reponse->fetch())
{
	$req1 = $bdd->prepare('INSERT INTO a_vendre(id, produit, quantite_disponible, tarif, unite_vente, variete) VALUES(:id, :produit, :quantite_disponible, :tarif, :unite_vente, :variete)');

$req1->execute(array(

    'id' => $donnees['id'],
	
	'produit' => $donnees['produit'],

    'quantite_disponible' => $donnees['quantite_disponible'],

    'tarif' => $donnees['tarif'],

    'unite_vente' => $donnees['unite_vente'],
	
	'variete' => $donnees['variete']
	
	 ));
}	
	
	
	
	
	
	?>
		</body>
	</html>