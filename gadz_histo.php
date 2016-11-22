<?php
session_start();
/* Connexion SQL */
require_once("./connexion_mysql.php");
require_once("./fonctions.php");


// ETAGE
if(isset($_COOKIE['etage']))
{
    $choix_etage=$_COOKIE['etage'];
    $_SESSION['etage']=$_COOKIE['etage'];
    if(($choix_etage<=7 AND $choix_etage>=1) OR $choix_etage==-1)
    {
	/* Requete SQL pour avoir les données de l'étage en cours*/
	$req = $bdd->prepare('SELECT * FROM etages WHERE etage = ?');
	$req->execute(array($choix_etage));
	$etage=$req->fetch();
	$req->closeCursor();
    }
    else
    {
	$_SESSION['etage']="";
        header('Location: ./choisir_etage.php?erreur=1');
	exit;
    }
}
else
{
    /*si pas de variable étage alors on redirige vers le choix de l'étage*/
    $_SESSION['etage']="";
    header('Location: ./choisir_etage.php?erreur=0');
    exit;
}

//ADMIN
if(isset($_SESSION['admin']))
{
    if($_SESSION['admin']==true)
    {
        // mode admin actif
	//echo "admin ok";
    }
    else
    {
        // mode admin INactif
	$_SESSION['admin']=false;
	//echo "admin Nok";
    }
    
}
else
{
    $_SESSION['admin']=false;
}



// GADZ
if(isset($_POST['gadz_id'])) // si il y a un ID de PG
{
    /* Requete SQL pour le Gadzarts en cours */
    $req = $bdd->prepare('SELECT * FROM gadzarts WHERE gadzarts_id = ?');
    $req->execute(array($_POST['gadz_id']));
    if ($req->rowCount() == 1)
    {
	$gadz=$req->fetch();
	$req->closeCursor();
    }
    else
    {
	$req->closeCursor();
	header('Location: ./?erreur=1');
    }
}
else
{
    header('Location: ./?erreur=0');
    exit;
}


$message="";


/* récupération de la variable de l'operation à annuler*/
if(isset($_POST['operation_a_annuler']) && is_numeric($_POST['operation_a_annuler']))
{
    $operation_a_annuler=$_POST['operation_a_annuler'];
    
    $req = $bdd->prepare('SELECT * FROM operations WHERE operation_id = ?');
    $req->execute(array($operation_a_annuler));
    if ($req->rowCount() == 1)
    {
	$operation=$req->fetch();
	$req->closeCursor();
	$compte_a_rebours=(120-(time()-strtotime($operation['operation_time'])));
	if($operation['operation_annulee'])
	{
	    $message="Op&eacute;ration [".$operation['operation_libelle']."] dat&eacute;e du ".$operation['operation_time']." d&eacute;j&agrave; annul&eacute;e";
	    $req->closeCursor();
	}
	elseif( ($compte_a_rebours>0 && $compte_a_rebours<120 && !$operation['operation_annulee']) || ($_SESSION['admin'] && $choix_etage==$operation['operation_etage']))
	{

	    
	    
	    if($operation['operation_type']=="bucquage")
	    {
		$req->closeCursor();
		$req = $bdd->prepare('UPDATE operations SET operation_annulee = 1 WHERE operation_id = ?');
		$req->execute(array($operation_a_annuler));
		$req->closeCursor();
		
		// on recrédite le gadz
		crediter_gadz(abs($operation['operation_montant']),$gadz,$bdd);
		// on lui retire l'alcool gagné (pour le respect des niveaux)
		if(is_numeric($operation['operation_description']))
		{
		    debiter_gadz_alcool($operation['operation_description'],$gadz,$bdd);
		}
		
		// on débite le tbk d'accueil qui avait reçu les brousoufs
			/* Requete SQL pour avoir les données de l'étage en cours*/
			$req = $bdd->prepare('SELECT * FROM etages WHERE etage = ?');
			$req->execute(array($operation['operation_etage']));
			$etage=$req->fetch();
			$req->closeCursor();
		debiter_etage(abs($operation['operation_montant']),$etage,$bdd);
		
		// on récrédite le tbk du gadz
			/* Requete SQL pour avoir les données de l'étage en cours*/
			$req = $bdd->prepare('SELECT * FROM etages WHERE etage = ?');
			$req->execute(array($gadz['gadzarts_etage_responsable']));
			$etage=$req->fetch();
			$req->closeCursor();
		crediter_etage(abs($operation['operation_montant']),$etage,$bdd);
		
		$message="Op&eacute;ration [".$operation['operation_libelle']."] dat&eacute;e du ".$operation['operation_time']." vient d'&ecirc;tre annul&eacute;e";
	    }
	    elseif($operation['operation_type']=="credit")
	    {
		$req->closeCursor();
		$req = $bdd->prepare('UPDATE operations SET operation_annulee = 1 WHERE operation_id = ?');
		$req->execute(array($operation_a_annuler));
		$req->closeCursor();
		
		// on débite le gadz du montant crédité précedemment
		debiter_gadz(abs($operation['operation_montant']),$gadz,$bdd);

		$message="Op&eacute;ration [".$operation['operation_libelle']."] dat&eacute;e du ".$operation['operation_time']." vient d'&ecirc;tre annul&eacute;e";
	    }
	    else
	    {
		$message="Ce type d'op&eacute;ration ne peut pas &ecirc;tre annul@eacute;e<br> Contacte le webmaster du site pour arranger ce probl&egrave;me.";
	    }
	}
	else
	{
	    $message="Op&eacute;ration [".$operation['operation_libelle']."] dat&eacute;e du ".$operation['operation_time']." ne peut plus &ecirc;tre annul&eacute;e. Passe par le zident";
	    $req->closeCursor();   
	}
    }
    else
    {
	$req->closeCursor();
	$message="Erreur pour annuler l'op&eacute;ration";
    }    

}

?>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>cachalot</title>
<style type="text/css">

	body {
		background-color: #000159;
		margin: 5px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #fff;
	}
	
	table,td,tr,th {
	border-width:1px; 
	border-style:solid; 
	border-color:#fff;
	text-align: center;
	}
	#cachalot_div_id{
		display: block;
		margin-left: auto;
		margin-right: auto;
		margin-top: 100px;
		vertical-align: bottom;
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 1em;
		background-color: #990000;
		border: 1px solid #D0D0D0;
		color: #ffffff;
	}	
	#pg_input{
		text-align: left;
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 5em;
		text-align: center;
		background-color: #fff;
		color: #000;
                width:3em;
                height:1em;
	}
	

	</style>

	<script src="jquery_min.js"></script>
</head>
<body>
	
	
	
	<div id="container_id">
		
		<div id="cachalot_div_id">
                    <center>
                        <h1><?=$gadz['gadzarts_cachalot_affichage'];?></h1><br>
			Etage en cours : <?=$choix_etage;?>
			<?php
			if($_SESSION['admin'])
			{
			    echo " (MODE ADMINISTRATEUR) ";
			}
			?>
			<br>
			Etage responsable du compte : <?=$gadz['gadzarts_etage_responsable'];?><br><br>
			<form action='./gadz_accueil.php' method='post'>
			    <input type='hidden' name='pg_input' value='<?=$gadz['gadzarts_cachalot_id'];?>'>
			    <input type='hidden' name='choix_etage' value='<?=$choix_etage;?>'>
			    <?php
			    if($_SESSION['admin'])
			    {
				?>
				
                            <input type="hidden" name="admin_motdepasse" value="<?=$admin_motdepasse;?>">
                            <input type="hidden" name="admin_etage" value="<?=$choix_etage;?>">
				<?php
			    }
			    ?>
			    
			    <input type='submit' id="retour_id" value='Retour au bucquage'>
			</form>
			
			<?php
			
			echo "<b>".$message."</b><br><br><br>";
			?>
                    </center>
                    <hr>
                    <br>
			<center>
		    <table id="">
                        <caption>Historique</caption>
                        <tr>
                            <th>type</th><th>&eacute;tage</th><th>Libell&eacute;</th><th>montant</th><th>date</th><th>Annulation</th>
                        </tr>
			
                        <?php
                        $req = $bdd->prepare('SELECT * FROM operations WHERE operation_gadzarts_id = ? ORDER BY operation_time DESC');
                        $req->execute(array($gadz['gadzarts_id']));
                        while ($operation = $req->fetch())
                        {
				$compte_a_rebours=(120-(time()-strtotime($operation['operation_time'])));
                                echo "<tr><td>".$operation['operation_type']."</td><td>".$operation['operation_etage']."<sup>e</sup> </td><td>
				".$operation['operation_libelle']."</td><td>".$operation['operation_montant']."&euro;</td><td>".$operation['operation_time']."</td><td>";
                                    
				    if($compte_a_rebours>0 && $compte_a_rebours<120 && !$operation['operation_annulee'])
                                    {
					?>
					<form id="" method="post" action="./gadz_histo.php">
					    <input type="hidden" name="etage" value="<?=$choix_etage;?>">
					    <input type="hidden" name="gadz_id" value="<?=$gadz['gadzarts_id'];?>">
					    <input type="hidden" name="operation_a_annuler" value="<?=$operation['operation_id'];?>">
					    <input type="submit" value="Annuler cette op&eacute;ration" >
					</form>
					<?php
				    }
				    elseif($_SESSION['admin'] && !$operation['operation_annulee'] && $operation['operation_etage']==$choix_etage)
				    {
					?>
					<form id="" method="post" action="./gadz_histo.php">
					    <input type="hidden" name="etage" value="<?=$choix_etage;?>">
					    <input type="hidden" name="gadz_id" value="<?=$gadz['gadzarts_id'];?>">
					    <input type="hidden" name="operation_a_annuler" value="<?=$operation['operation_id'];?>">
					    <input type="hidden" name="admin_motdepasse" value="<?=$admin_motdepasse;?>">
					    <input type="hidden" name="admin_etage" value="<?=$choix_etage;?>">
					    <input type="submit" value="Annuler cette op&eacute;ration (Administrateur)" >
					</form>
					<?php
				    }
                                    elseif($operation['operation_annulee'])
                                    {
                                        echo " op&eacute;ration annul&eacute;e";
                                    }
				    else
				    {
					echo "";
				    }
				    echo "</td></tr>";
				    
			    
                        }
                        ?>
			</table>
			</center>
			<br>
		</div>


	</div>

<script type="text/javascript"><!--


$(document).ready(function () {
    $('#retour_id').focus();
});
</script>




</body></html>