<?php
    
/* Connexion SQL */
require_once("./connexion_mysql.php");

/* Déclarations des fonctions */
require_once("./fonctions.php");

// remise a zero des comptes des étages
    $req = $bdd->prepare("UPDATE  etages SET solde =  '0'");
    $req->execute();
    $req->closeCursor();

// pour chaque operation dans la BDD qui n'est pas annulée...    
    $req = $bdd->prepare("SELECT * FROM operations WHERE operation_annulee='0' AND operation_type='bucquage'");
    
    $req->execute();
    
    while ($operation = $req->fetch())
    {
    // créditer l'étage d'accueil (il reçoit de l'argent puisqu'on consomme chez lui)
	$reqe = $bdd->prepare('SELECT * FROM etages WHERE etage = ? LIMIT 1');
	$reqe->execute(array($operation['operation_etage']));
	$etage_accueil=$reqe->fetch();
	$reqe->closeCursor();
    
    // débiter l'étage d'origine (il perd de l'argent car il paye pour son gadzart)
    
    // pour cela il faut connaitre l'étage responsable du gadzarts   
	/* Requete SQL pour le Gadzarts en cours */
	$reqg = $bdd->prepare('SELECT * FROM gadzarts WHERE gadzarts_id = ?');
	$reqg->execute(array($operation['operation_gadzarts_id']));
		if ($reqg->rowCount() == 1)
		{
			 $gadz=$reqg->fetch();
			 $reqg->closeCursor();
		}
		else
		{
			 $reqg->closeCursor();
			 echo "probleme avec le gadz de l'operation en cours";
		}
		 
    
	$reqe = $bdd->prepare('SELECT * FROM etages WHERE etage = ? LIMIT 1');
	$reqe->execute(array($gadz['gadzarts_etage_responsable']));
	$etage_origine=$reqe->fetch();
	$reqe->closeCursor();
    
		 if($etage_accueil['etage']!=$etage_origine['etage'])
		 {
			 crediter_etage(abs($operation['operation_montant']),$etage_accueil,$bdd);
			 debiter_etage(abs($operation['operation_montant']),$etage_origine,$bdd);
			 
			 		//requette pour afficher le solde de chaque tbk
					$reqe = $bdd->prepare("SELECT * FROM etages");
					$reqe->execute();

					$total = 0;
					while ($etage = $reqe->fetch())
					{
						$total+=$etage['solde'];
					}
					$reqe->closeCursor();
					if(round($total,1)!=0)
					{
						echo	"[".$operation['operation_id']."] ";
						break;
					}
			 
		 }
    }
    $req->closeCursor();
			    
    



?>
<html>
    <head><title>MAJ balance</title></head>
    <body>
	<table>
	    <tr><th>Etage</th><th>Solde</th></tr>
	    <?php
	    //requette pour afficher le solde de chaque tbk
	    $req = $bdd->prepare("SELECT * FROM etages");
	    $req->execute();
	    
	    $total = 0;
	    while ($etage = $req->fetch())
	    {
		$total+=$etage['solde'];
		echo "<tr><td>".$etage['etage']."</td><td>".$etage['solde']."</td></tr>";
	    }
	    $req->closeCursor();
	    
	    ?>
	</table>
	<?php
	    echo "<h1>Total des soldes : ".$total."</h1>";
	?>
	
    </body>
</html>


