

<?php



try
{
	$bdd = new PDO ('mysql:host=localhost;dbname=djalf', 'root', '');
}
catch (Exception $e)
{
	die('Erreur : ' . $e->getMessage ());
}

// on exporte la table recap_commande


 
 
//Premiere ligne = nom des champs (
$xls_output = "id; id_client; id_produit; jour; quantite_commande; tarif; total; traitement; unite_vente; variete";
$xls_output .= "\n";
 
//Requete SQL
$sth = $bdd->prepare("SELECT * FROM recap_commande"); 
$sth ->execute();


  //Boucle sur les resultats
while($row = $sth->fetch(PDO::FETCH_ASSOC))
{

$xls_output .="$row[id];$row[id_client];$row[id_produit];$row[jour];$row[quantite_commande];$row[tarif];$row[total];$row[traitement];$row[unite_vente];$row[variete]";
$xls_output .= "\n";
}
 

 
$date = date("w") ;
$a = "commande_semaine_" ;
$b = ".csv";
$nom = $a.$date.$b;
header("Content-type: application/vnd.ms-excel");
header("Content-disposition: attachment; filename=$nom ");
print $xls_output;
exit;
?>

