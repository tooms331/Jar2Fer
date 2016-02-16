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


$_entier = htmlspecialchars ($_POST['entier']);





try
{
	$bdd = new PDO ('mysql:host=localhost;dbname=djalf', 'root', '');
}
catch (Exception $e)
{
	die('Erreur : ' . $e->getMessage ());
}
    


// verification quantité commande
$variete_id=$_POST['variete_id'] * 1;
$reponse = $bdd->query("SELECT * FROM a_vendre where id = '".$variete_id."'");
while($donnees = $reponse->fetch())
{
	$_max = $donnees['quantite_disponible'];

//si commande superieur au max
}
		if ($_entier> $_max)
{		
			echo 'La quantité saisie est superieure à la quantité maximale disponible'
		
	?>		
		<p>
	<form action="produit_disponible.php" method="post"> 

		<input type="submit" value="Retour à la liste produit" ></code> 
	</form>
		</p>
	<?php
}
	
	else
{	
$reponse->closeCursor();

// definition des variables issues de la selection produit
$variete_id=$_POST['variete_id'] * 1;
$reponse = $bdd->query("SELECT * FROM a_vendre where id = '".$variete_id."'");
while($donnees = $reponse->fetch())
{
	
	$_produit = $donnees['produit'];

    $_variete = $donnees['variete'];

    $_tarif = $donnees['tarif'];

    $_unite = $donnees['unite_vente'];
	
	$_total  = $_POST['entier'] * $donnees['tarif'] ;

   

}
$reponse->closeCursor();

 //Si le produit existe déjà on ajoute seulement la quantité
      $positionProduit = array_search($variete_id,  $_SESSION['panier']['id_produit']);

      if ($positionProduit !== false)
      {
         $_SESSION['panier']['quantite_commande'][$positionProduit] += $_entier ;
		 $_SESSION['panier']['total'][$positionProduit] += $_total ;
      }
      else
      {
         //Sinon on ajoute le produit
         
     array_push ($_SESSION['panier']['id_produit'], $variete_id);
     array_push ( $_SESSION['panier']['identification_client'],$_SESSION['id_client']); 
     array_push ($_SESSION['panier']['produit'], $_produit);
	 array_push ( $_SESSION['panier']['variete'], $_variete);
     array_push ( $_SESSION['panier']['quantite_commande'], $_entier);
	 array_push ( $_SESSION['panier']['tarif'], $_tarif);
	 array_push ( $_SESSION['panier']['unite_vente'], $_unite);
	 array_push ( $_SESSION['panier']['total'], $_total);
	  
	  }
//on enleve la quantite du tableau de session a_vendre
		$positionproduit = array_search($variete_id,  $_SESSION['a_vendre']['id_produit']);
		$_SESSION['a_vendre']['quantite_disponible'][$positionproduit] -= $_entier ;
}
echo $variete_id;
 echo "<pre>";
  print_r($_SESSION['panier']);
  echo "</pre>";
  echo $_SESSION['panier']['id_produit'][0];

				

?>
<p>
<form action="panier.php" method="post">	

<input type="submit" value="Retour" ></code>	
</p>
</body>
</html>	 

