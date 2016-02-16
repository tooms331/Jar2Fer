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
    
$traitement = 0;
$date = date("Y-m-d") ;
	





// remplissage table numero_commande
$req = $bdd->prepare('INSERT INTO numero_commande(id_client, jour) VALUES(:id_client, :jour)');

$req->execute(array(

    'id_client' => $_SESSION['panier']['identification_client'][0],

    'jour' => $date
	));
	


$req1 = $bdd->query(" SELECT id FROM numero_commande WHERE id = (SELECT MAX(id) FROM numero_commande)");
$id = $req1->fetch();
		if (empty($id['id']))
		{$new_id = 1; }
	    else
		{$new_id = $id['id'] + 1;}

	
// remplissage de la base de donnees archive_panier	
	
	for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++)
	{	
$req2 = $bdd->prepare('INSERT INTO archive_panier(id, id_produit, id_client, variete, quantite_commande, tarif, unite_vente, total, jour, traitement) VALUES(:id,:id_produit, :id_client, :variete, :quantite_commande, :tarif, :unite_vente, :total, :jour, :traitement)');

$req2->execute(array(

    'id' => $new_id,

    'id_produit' => $_SESSION['panier']['id_produit'][$i],
	
	'id_client' => $_SESSION['panier']['identification_client'][$i],
	
	 'variete' => $_SESSION['panier']['variete'][$i],

    'quantite_commande' => $_SESSION['panier']['quantite_commande'][$i],

    'tarif' => $_SESSION['panier']['tarif'][$i],

    'unite_vente' => $_SESSION['panier']['unite_vente'][$i],
	
	'total' => $_SESSION['panier']['total'][$i],
	
	'jour' => $date,
	
	'traitement' => $traitement

    ));
	}



// modification de la base de donnees a_vendre_commune

$reponse = $bdd->query("SELECT * FROM archive_panier WHERE id = '".$new_id."'");
while($donnees = $reponse->fetch())
{
	$variete_id = $donnees['id_produit'] * 1;
	$com = $bdd->query("SELECT quantite_disponible FROM a_vendre_commune WHERE id = '".$variete_id."'");
	$quantite = $com->fetch();
	$quantite_commune = $quantite['quantite_disponible'];
	$com->closeCursor();
	
		$req =$bdd->prepare("UPDATE a_vendre_commune SET quantite_disponible = :quantite_disponible WHERE id = :variete_id ");
		$req->execute(array(
		'quantite_disponible' => $quantite_commune - $donnees['quantite_commande'],
		'variete_id' => $donnees['id_produit']
		));
	$req->closeCursor();
}

// remplissage table recap_commande

for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++)
	{	
$req3 = $bdd->prepare('INSERT INTO recap_commande(id, id_produit, id_client, variete, quantite_commande, tarif, unite_vente, total, jour, traitement) VALUES(:id,:id_produit, :id_client, :variete, :quantite_commande, :tarif, :unite_vente, :total, :jour, :traitement)');

$req3->execute(array(

    'id' => $new_id,

    'id_produit' => $_SESSION['panier']['id_produit'][$i],
	
	'id_client' => $_SESSION['panier']['identification_client'][$i],
	
	 'variete' => $_SESSION['panier']['variete'][$i],

    'quantite_commande' => $_SESSION['panier']['quantite_commande'][$i],

    'tarif' => $_SESSION['panier']['tarif'][$i],

    'unite_vente' => $_SESSION['panier']['unite_vente'][$i],
	
	'total' => $_SESSION['panier']['total'][$i],
	
	'jour' => $date,
	
	'traitement' => $traitement

    ));
	}


// Vider la table a_vendre

$bdd->exec("DELETE FROM a_vendre");

// affichage remerciement et bouton retour accueil

echo 'Merci de votre commande, elle vous sera livré à domicile mercredi';

// On détruit les variables de notre session
session_unset ();

// On détruit notre session
session_destroy ();
?>

<p>
	<form action="index.html" method="post"> 
    <input type="submit" value="Retour au site" ></code> 
	</form>
		</p>

</body>
</html>
