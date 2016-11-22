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
if(isset($_POST['pg_input']) || isset($_GET['gadz_id'])) // si il y a un trigramme
{
    if(isset($_POST['pg_input']) && $_POST['pg_input']!="")
    {
	/* Requete SQL pour le Gadzarts en cours */
	$req = $bdd->prepare('SELECT * FROM gadzarts WHERE gadzarts_cachalot_id = ?');
	$req->execute(array($_POST['pg_input']));
	if ($req->rowCount() == 1)
	{
	    $gadz=$req->fetch();
	    $req->closeCursor();
	}
	else //si le trigramme n'appartient à aucun compte
	{
	    $req->closeCursor();
	    header('Location: ./?erreur=1');
	    exit;
	}
    }
    elseif(isset($_GET['gadz_id']) && $_GET['gadz_id']!="")
    {
	/* Requete SQL pour le Gadzarts en cours */
	$req = $bdd->prepare('SELECT * FROM gadzarts WHERE gadzarts_id = ?');
	$req->execute(array($_GET['gadz_id']));
	if ($req->rowCount() == 1)
	{
	    $gadz=$req->fetch();
	    $req->closeCursor();
	}
	else //si le trigramme n'appartient à aucun compte
	{
	    $req->closeCursor();
	    header('Location: ./?erreur=1');
	    exit;
	}
    }
    else
    {
    header('Location: ./?erreur=0');
    exit;
    }

}
else
{
    header('Location: ./?erreur=0');
    exit;
}

?>
<html>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<head>
<title> Zacachal'sss</title>
<style type="text/css">

	::selection{ background-color: #E13300; color: white; }
	::moz-selection{ background-color: #E13300; color: white; }
	::webkit-selection{ background-color: #E13300; color: white; }
	
	@font-face { 
		font-family: 'Zagoth'; 
		src: url('./polices/OldLondon.ttf'); 
	}
	
	body {
		background-color: <?=$etage['couleur2']?>;
		margin: 5px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #FD6C9E;
	}
	
	
	#photo_user{
		text-align: center;
		display: block;
		position: absolute;
		width:30%;
		height: auto;

		top:2%;
		left:2%;
	}
	
	
	#photo_user img{
		height:200px;
	}


	
	
	#affichage_nom {
		
		text-align: center;
		font-family: 'Zagoth', Old London, Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 3.5em;
		color: <?=$etage['couleur5']?>;
		background-color: <?=$etage['couleur2']?>;
/*		border: 1px solid #773388;*/
		

		display: block;
		position: absolute;
		width:40%;
		height: 5%;

		top:2%;
		left:34%;
	}
	
	#affichage_donnees {
		text-align: left;
		font-family: 'Zagoth', Old London, Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 2em;
		background-color: <?=$etage['couleur1']?>;
		border: 1px solid #D0D0D0;
		color: <?=$etage['couleur3']?>;
		display: block;
		position: absolute;
		width:39%;
		height: 23%;

		top:9%;
		left:34%;
		padding-left: 1%;
	}
	a:link {color:<?=$etage['couleur3']?>;text-decoration:none;}    /* unvisited link */
	a:visited {color:<?=$etage['couleur3']?>;text-decoration:none;} /* visited link */
	a:hover {color:<?=$etage['couleur3']?>;text-decoration:underline;}   /* mouse over link */
	a:active {color:<?=$etage['couleur3']?>;text-decoration:none;}  /* selected link */

	
	#solde {
		text-align: center;
		font-family: 'Zagoth', Old London, Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 4em;
		<?php
		if($gadz['gadzarts_cachalot_solde']<0){
			echo "background-color:#ff0000;";
			echo "color: #000000;";
		}
		else{
			echo "background-color:#004400;";
			echo "color: #ffffff;";
		}
		?>
		
		

		display: block;
		position: absolute;
		width:20%;
		height: 15%;
		line-height: 200%;

		top:2%;
		left:76%;
	}
	
	#zone_achat {
	
		
		text-align: center;
		font-family: 'Zagoth', Old London, Consolas, Monaco, Courier New, Courier, monospace;
		color: #ffffff;
		background-color:inherit;
		

		display: block;
		position: absolute;
		width:20%;

		top:24%;
		left:76%;
	}

	#choix_id{
		font-family: 'Zagoth', Old London, Consolas, Monaco, Courier New, Courier, monospace;
		text-align: center;
		font-size: 2em;
	}
	
	#produits {
		background-color: #000000;
		border: 1px solid #D0D0D0;
		
		
		/*background-image: url('./photos/amnetb.png');*/
		background-repeat: no-repeat;
		background-position: center;
		background-size: contain;
		
		display: block;
		position: absolute;
		bottom:2%;
		left:2%;
		width:96%;
		height: 64%;
	}
	
	#prodgauche{
		font-size: 2.1em;
		text-align: left;
		font-family: 'Zagoth', Old London, Consolas, Monaco, Courier New, Courier, monospace;
		color: #ffffff;
		
	}
	
	td.touche{
		font-family: Helvetica, Arial, sans-serif;
		color: #FD6C9E;
	}
	
	.flashy{
		font-size: 1em;
		font-family: Helvetica, Arial, sans-serif;
		color:#7FFF00;
	}
	
	.bouton_admin{
	    
		width:21em;
	}
	.bouton_classique{
	    	background-color: <?=$etage['couleur1']?>;
		color: <?=$etage['couleur3']?>;
	}

	</style>

	
	
	<script src="./jquery_min.js"></script>
	
	
</head>
<body>
	<div id="container_id">
		<div id="user">
			<div id="photo_user">
				<img src="images/amdroit.png">
			</div>
			<div id="affichage">
					
				<div id="solde">
					<?=$gadz['gadzarts_cachalot_solde'];?> &euro;
				</div>
				<div id="zone_achat">
				    <?php
				    //si négat'sss de plus de 24h
				    $compte_a_rebours=(24-(time()-strtotime($gadz['gadzarts_cachalot_date_negats']))/3600);
				    if(!$gadz['gadzarts_cachalot_compte_actif'] || $_SESSION['admin'])
				    {
				    ?>
					<form id="" name="" method="post" action="./">
					    <input type="submit" name="" id="choix_id" value="Retour"/>
					</form>
				    <?php
				    }
				    elseif($compte_a_rebours<0 && $gadz['gadzarts_cachalot_solde']<0 && $gadz['gadzarts_cachalot_date_negats']!=0)
				    {
				    ?>
					<form id="" name="" method="post" action="./">
					    <input type="submit" name="" id="choix_id" value="Retour"/>
					</form>
				    <?php
				    }
				    else
				    {
					?>
					
					    <form id="form_bucquage_id" name="" method="post" action="./operation_bucquage.php">
						<input type="text" name="choix_conso" id="choix_id"/>
						<input type="hidden" name="choix_etage" value="<?=$choix_etage;?>"/>
						<input type="hidden" name="pg_id" value="<?=$gadz['gadzarts_id'];?>">
						<input type="hidden" name="date_conso" value="<?php echo date("Y-m-d H:i:s");?>">
					    </form>
					<?php
				    }
				    ?>
				</div>
				<div id="affichage_nom">		
					<?=$gadz['gadzarts_cachalot_affichage'];?>
				</div>
		
				<div id="affichage_donnees">
				    <?php
					if($compte_a_rebours>0 && $compte_a_rebours<24 && $gadz['gadzarts_cachalot_solde']<0)
					{
					    if(floor($compte_a_rebours)==0)
					    {
						if(floor($compte_a_rebours*60)==0)
						{
						    $compte_a_r_afficahge=floor($compte_a_rebours*3600)."sec";
						}
						else
						{
						    $compte_a_r_afficahge=floor($compte_a_rebours*60)."min";
						}
					    }
					    else
					    {
						$compte_a_r_afficahge=floor($compte_a_rebours)."h";
					    }
					    echo "<center>Compte bloqu&eacute; dans ".$compte_a_r_afficahge."</center><br>";
					}
					elseif($compte_a_rebours<0 && $gadz['gadzarts_cachalot_solde']<0 && $gadz['gadzarts_cachalot_date_negats']!=0)
					{
					    echo "<center>Date du n&eacute;gat'sss : ".$gadz['gadzarts_cachalot_date_negats']."</center><br>";
					}
					
					/* Requete SQL pour le Gadzarts en cours */
					$req = $bdd->prepare('SELECT * FROM niveaux WHERE niveau_score_to_get <= ? ORDER BY niveau_ordre DESC LIMIT 1');
					$req->execute(array($gadz['gadzarts_cachalot_volume_alcool']));
					if ($req->rowCount() == 1)
					{
					    $niveau=$req->fetch();
					    $req->closeCursor();
					    $niveau_nom=$niveau['niveau_nom'];
					    $niveau_ordre=$niveau['niveau_ordre'];
					}
					else
					{
					    $req->closeCursor();
					    $niveau_nom="Niveau indisponible";
					    $niveau_ordre="";
					}
				    ?>
				    niveau <?=$niveau_ordre;?> : <?=$niveau_nom;?><br/>
				    <?php
					if($gadz['gadzarts_id']==1 or $gadz['gadzarts_id']==10)
					{
					       echo "mais il n'a pas besoin de niveau...<br/>";
					}
				    ?>
				    <br>
					<center>
				<form id="" method="post" action="./gadz_histo.php">
				    <input type="hidden" name="etage" value="<?=$choix_etage;?>">
				    <input type="hidden" name="gadz_id" value="<?=$gadz['gadzarts_id'];?>">
				    <input type="submit" value="Consulter mon historique" class="bouton_classique" >
				</form>
				</center>
				
				<?php
				    if(!$gadz['gadzarts_cachalot_compte_actif'])
				    {
					echo "<center><h1>Compte Inactif</h1></center>";
				    }
				?>
				</div>
			</div>

		</div>
		
		<div id="produits">
			<div id="produits_gauche">
			    <?php
			    if($_SESSION['admin'])
			    {
			    ?>
				<center><h1>Mode Admin</h1>
				
				
				<?php
				if($_COOKIE['etage']!=$gadz['gadzarts_etage_responsable'])
				{
				    echo "Ce compte ne d&eacute;pend pas de ton &eacute;tage.
				    <br>Tu ne peux donc que annuler des op&eacute;rations faite &agrave; ton &eacute;tage dans son historique ou bucquer une op&eacute;ration sp&eacute;ciale.";
				
				
				?>
				<br><br><br>
				<form id="" method="post" action="./gadz_histo.php">
				    <input type="hidden" name="gadz_id" value="<?=$gadz['gadzarts_id'];?>">
				    <input type="submit" value="G&eacute;rer historique" class="bouton_admin" >
				</form>
				
				<?php
				    if(isset($_SESSION['message_admin']))
				    {
					echo "<h3>".$_SESSION['message_admin']."</h3><br>";
				    }
				    $_SESSION['message_admin']="";
				?>	
				
				<form id="" method="post" action="./admin_operation_sur_gadz.php">
				    Montant &agrave; d&eacute;biter (>0) : <input type="text" name="montant_debit">
				    <input type="text" name="description_debit" value="operation speciale">
				    <input type="hidden" name="gadz_id" id="gadz_id" value="<?=$gadz['gadzarts_id'];?>">
				    <input type="hidden" name="date_debit" value="<?php echo date("Y-m-d H:i:s");?>">
				    <input type="submit" value="D&eacute;biter compte" class="bouton_admin">(Essaye de mettre autre chose que "operation speciale"...)
				</form>
				
				<?php
				}
				else
				{
				?>
				<br><br><br>
				<form id="" method="post" action="./gadz_histo.php">
				    <input type="hidden" name="gadz_id" value="<?=$gadz['gadzarts_id'];?>">
				    <input type="submit" value="G&eacute;rer historique" class="bouton_admin" >
				</form>
				<?php
				    if(isset($_SESSION['message_admin']))
				    {
					echo "<h3>".$_SESSION['message_admin']."</h3><br>";
				    }
				    $_SESSION['message_admin']="";
				?>			
				
				<form id="" method="post" action="./admin_operation_sur_gadz.php">

				    Montant &agrave; cr&eacute;diter (>0) : <input type="text" name="montant_credit">
				    <input type="radio" name="moyen_de_paiement" value="cheque" checked>Ch&egrave;que
				    <input type="radio" name="moyen_de_paiement" value="espece">Esp&egrave;ce
				    <input type="radio" name="moyen_de_paiement" value="virement">Virement
				    <input type="hidden" name="gadz_id" id="gadz_id" value="<?=$gadz['gadzarts_id'];?>">
				    <input type="hidden" name="date_credit" value="<?php echo date("Y-m-d H:i:s");?>">
				    <input type="submit" value="Cr&eacute;diter compte" class="bouton_admin">
				</form>
				
				
				<form id="" method="post" action="./admin_operation_sur_gadz.php">
				    Montant &agrave; d&eacute;biter (>0) : <input type="text" name="montant_debit">
				    <input type="text" name="description_debit" value="operation speciale">
				    <input type="hidden" name="gadz_id" id="gadz_id" value="<?=$gadz['gadzarts_id'];?>">
				    <input type="hidden" name="date_debit" value="<?php echo date("Y-m-d H:i:s");?>">
				    <input type="submit" value="D&eacute;biter compte" class="bouton_admin">(Essaye de mettre autre chose que "operation speciale"...)
				</form>
				
				
				
				<form id="" method="post" action="./admin_operation_sur_gadz.php">
				    <input type="submit" value="Modifier compte" class="bouton_admin" disabled>
				</form>
				
				<form id="" method="post" action="./admin_operation_sur_gadz.php">
				    <input type="submit" value="D&eacute;sactiver compte" class="bouton_admin" disabled>
				</form>
				
				<form id="" method="post" action="./admin_operation_sur_gadz.php">
				    <input type="submit" value="Supprimer compte" class="bouton_admin" disabled>
				</form>
				
				
				
				</center>
			    <?php
				}
			    }
			    else
			    {
			    ?><center>
				<table id="prodgauche"><tr>
				    <?php
				    $req = $bdd->prepare('SELECT * FROM produits WHERE etage = ?');
				    $req->execute(array($choix_etage));
				    $k=0;
				    while ($produit = $req->fetch())
				    {
					$k++;
					    echo "<td>&nbsp;&nbsp;&nbsp;</td><td>".$produit['nom']."</td><td>___</td><td>".$produit['prix']."&euro; </td><td class='touche'>[".$produit['touche']."]</td>";
					    
					    if($k==2)
					    {
						$k=0;
						echo "</tr><tr>";
					    }
					    elseif($k==1)
					    {
						echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;</td>";
					    }
					    
				    }
				    if($k!=0){echo "</tr>";}
				    
				    ?>
				</table></center>
			    <?php
			    }
			    ?>
			</div>
			
			<div id="produits_droite">
				
			</div>
			
		</div>

	</div>

<script type="text/javascript"><!--

$(document).ready(function () {

$('#choix_id').focus();
$('#choix_id').attr('size',2);   


	
	
$('#choix_id').keyup(function(){
        var gadzarts_conso=$(this).val();
        $(this).attr('size',gadzarts_conso.length+1);
	});

<?php
$req = $bdd->prepare('SELECT * FROM produits WHERE etage = ?');
$req->execute(array($choix_etage));
echo "var lettres_produits = [";
while ($produit = $req->fetch())
{
    echo ",'".$produit['touche']."'";	
}
echo "];";
?>


	
$("#choix_id").keyup(function( event )
{
    var str = $("#choix_id").val().toUpperCase();
    var size = $("#choix_id").val().toUpperCase().length;
    var last_car = str[size-1];

    if (jQuery.inArray(last_car,lettres_produits)<0 && size!=0 && str!='RETOUR')
    {
	$("#choix_id").val('');
	alert("Lettre indisponible : commande remise à zéro");
    }
});

});




</script>


</body>
</html>




