<?php
session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset="utf-8"/>
		<link rel="stylesheet" href="style.css" />
		<title>Produits disponibles</title> <!--Ceci est le titre sur le navigateur-->
	</head>
	<body>
	
	<?php
// On prolonge la session

// On teste si la variable de session existe et contient une valeur
if(isset($_SESSION['panier'])) {
	 
	  
	  $panier_existe = true;
	  echo 'le panier existe' ;
}

else
{
    $panier_existe = false;
	echo 'le panier existe pas';
}

	try
{
	$bdd = new PDO ('mysql:host=localhost;dbname=djalf', 'root', '');
}
catch (Exception $e)
{
	die('Erreur : ' . $e->getMessage ());
}
 

	
// affectation variable autorisation_entrer du mot de passe 
			if (isset($_POST['mot_de_passe']))
			{
	$mot_de_passe= htmlspecialchars($_POST['mot_de_passe']);
	$reponse = $bdd->query("SELECT * FROM base_client where passe = '".$mot_de_passe."'");

  
	$result = $reponse->fetch();
	$id_client = $result;
		if (empty($result))
			{
				$autorisation_entrer = false; // le mot de passe est faux
				$reponse->closeCursor ();	
			}
		else // le mot de passe est vrai
			{
				$autorisation_entrer = true; // le mot de passe est vrai	
				$reponse->closeCursor ();
			}
			}
	
if ($panier_existe == true OR $autorisation_entrer == true)
	{
	
	
	
	
	if ($panier_existe == false)
	
	{
		
	// creation de la session

	$reponse = $bdd->query("SELECT * FROM base_client where passe = '".$mot_de_passe."'");
while($donnees=$reponse->fetch())
{
	$_SESSION['id_client'] = $donnees['id'];
	$_SESSION['passe'] = htmlspecialchars($_POST['mot_de_passe']);
	$_SESSION['prenom'] = $donnees['prenom'];
	$_SESSION['nom'] = $donnees['nom'];
	
}	
	
	$reponse->closeCursor (); 

	// creation du panier
	
	$_SESSION['panier']=array();
      $_SESSION['panier']['id_produit'] = array();
      $_SESSION['panier']['identification_client'] = array();
	  $_SESSION['panier']['produit'] = array();
      $_SESSION['panier']['variete'] = array();
      $_SESSION['panier']['quantite_commande'] = array();
	  $_SESSION['panier']['tarif'] = array();
	  $_SESSION['panier']['unite_vente'] = array();
	  $_SESSION['panier']['total'] = array();
	  
	  // creation table produit disponible a_vendre
	 $_SESSION['a_vendre']=array();
		$_SESSION['a_vendre']['id_produit']= array();
		$_SESSION['a_vendre']['produit']= array();
		$_SESSION['a_vendre']['variete']= array();
		$_SESSION['a_vendre']['tarif']= array();
		$_SESSION['a_vendre']['unite_vente']= array();
		$_SESSION['a_vendre']['quantite_disponible']= array();
		
		// remplissage a_vendre
	$reponse = $bdd->query("SELECT * FROM a_vendre_commune");
		while($donnees=$reponse->fetch())
{
		array_push ($_SESSION['a_vendre']['id_produit'], $donnees['id']);
		array_push ($_SESSION['a_vendre']['produit'], $donnees['produit']);
		array_push ($_SESSION['a_vendre']['variete'], $donnees['variete']);
		array_push ($_SESSION['a_vendre']['tarif'], $donnees['tarif']);
		array_push ($_SESSION['a_vendre']['unite_vente'], $donnees['unite_vente']);
		array_push ($_SESSION['a_vendre']['quantite_disponible'], $donnees['quantite_disponible']);
		
}
	}
	echo "<pre>";
  print_r($_SESSION['a_vendre']);
  echo "</pre>";
	?>
	<header>
	<h1> Nos produits fraichements cueillies </h1>
	
	
	
	
		<p class="bonjour"> Bonjour <em><?php echo $_SESSION['prenom'];?> <?php echo $_SESSION['nom'];?></em>, bienvenue sur le support de commande.</p>
		 <p class="introduction"> Voici les produits que nous vous proposons cette semaine : </p>
		 
		 
	</header>
<?php		

 
  
  
	  
  

?>
<?php


try
{
	$bdd = new PDO('mysql:host=localhost;dbname=djalf', 'root', '');
}
catch (Exception $e)
{
        die('Erreur : ' . $e->getMessage());
}

			$nbArticles=count($_SESSION['a_vendre']['produit']);
		for ($i=0 ;$i < $nbArticles ; $i++)
			{

		
		



?>
<section>
 <p class="produit">
  
   <?php echo htmlspecialchars($_SESSION['a_vendre']['produit'][$i]);?> <?php echo htmlspecialchars($_SESSION['a_vendre']['variete'][$i]); ?><br />
   Prix : <?php echo htmlspecialchars($_SESSION['a_vendre']['tarif'][$i]); ?> euros par <?php echo htmlspecialchars($_SESSION['a_vendre']['unite_vente'][$i]);?> <br />
   Quantité disponible : <?php echo htmlspecialchars($_SESSION['a_vendre']['quantite_disponible'][$i]); ?><br />
   
   <?php $im=htmlspecialchars($_SESSION['a_vendre']['id_produit'][$i]);
         $a="image/produit/";
		 $b=".jpg";
		 $image= $a.$im.$b;
		
	?>
   <img src=<?php echo $image;?> alt="aubergine"/>
   
   <?php 
   if (htmlspecialchars($_SESSION['a_vendre']['quantite_disponible'][$i]) == 0 )
		{
			echo 'Désolé, ce produit a été victime de son succès';
		}
		    else
		{
	?>
   
	<form method="post" action="selection_produit.php" >
                    <input type="hidden" name="variete_id" value= <?php echo htmlspecialchars($_SESSION['a_vendre']['id_produit'][$i]);?>  >
					<input type="submit" name="Submit" value="Commander">
                </form>
	<?php
		}
	?>
</p>
</section>
<?php
			}
			

?>
<nav>
<p>
	<form action="panier.php" method="post"> 
			  
		<input type="submit" value="Aller au panier" ></code> 
</p>
</nav>
<?php
}
		
// mot de passe faux
else
{ echo 'Le mot de passe est incorrect';}
?>
</body>
</html>




