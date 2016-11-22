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

?>

<html>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<head>

<title>Zacachal'sss, pour la professionalisation de la latte</title>
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
		color: #E0FFFF;
	}
	
	#container_id{}
	
	#info_id{
		text-align: left;
		height: auto;
		display: block;
		position: absolute;
		top:5%;
		left:2%;
		width:25%;
		height: 50%;
		color: <?=$etage['couleur5']?>;
		background-color: <?=$etage['couleur2']?>;
	}
	
	#bucquage_input_id{
		text-align: left;
		display: block;
		position: absolute;
		top:5%;
		left:25%;
		width:50%;
		height: 50%;
		color:<?=$etage['couleur5']?>;
	}

	#hcachalot_id{
		text-align: left;
		background-color: <?=$etage['couleur2']?>;
		display: block;
		position: absolute;
		top:5%;
		right:2%;
		width:25%;
		height: 50%;
		color: <?=$etage['couleur5']?>;
	}
	
	#operation_id {
		text-align: left;
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #000000;
		border: 1px solid #D0D0D0;
		color: #ffffff;
		display: block;
		position: absolute;
		top:60%;
		left:20%;
		width:60%;
		height: auto;
	}
	
	#cachalot_div_id{
		display: block;
		position: absolute;
		top:25%;
		left:30%;
		width:40%;
		height: 150px;
		vertical-align: bottom;
		text-align: left;
		font-family: 'Zagoth', Old London, Consolas, Monaco, Courier New, Courier, monospace;
		/*font-size: 6em;*/
		text-align: center;
		background-color: <?=$etage['couleur1']?>;
		border: 1px solid #D0D0D0;
		color: <?=$etage['couleur3']?>;
	}
	.gros1
	{
		font-size: 6em;
	}
	
	#valider_id {
		font-family: 'Zagoth', Old London, Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 21px;
		color: #000000;
		background-color: #ffffff;
		margin: 3px;
		width:20%;
		height: 15%;
	}
	
	#mot_de_passe_id {
		font-family: 'Zagoth',Old London, Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 4em;
		height: 70px;
		color: <?=$etage['couleur3']?>;
		background-color: <?=$etage['couleur4']?>;
		text-align: center;
		width:80%;
	}

	
	#settings {

		display: block;
		position: absolute;
		bottom:10px;
		right:10px;
	}
	
	</style>

	
	<script src="jquery_min.js"></script>
</head>
<body>
	
	
	
	<div id="container_id">

		<div id="bucquage_input_id">
		    

			<form action="./admin_verif_code.php" method="post" id="pg_input_form">

			    <center>
			    <?php
			    if(isset($_GET['erreur']))
			    {
				if(is_numeric($_GET['erreur']))
				{
				    switch ($_GET['erreur'])
				    {
					case 0:
					echo "Mot de passe inexistant.";
					break;
				    
					case 1:
					echo "Mot de passe vide...";
					break;
					
					default:
					echo "Mot de passe incorrect";
				    }
				}
			    }
			    ?>
			   <br><br> Mot de passe administrateur :<br>
                            <input type="password" name="mot_de_passe" id="mot_de_passe_id">
			    </center>
			</form>

		</div>
		<div id="cachalot_div_id">
			<span class="gros1">
			<br><br>
			<?php
			
			if($etage['nom_du_foys']!=""){
				echo $etage['nom_du_foys'];	
			}
			else{
				echo "Zacachal'sss (".$choix_etage."e)";
			}	
			
			?> &nbsp;(zdt)
			<br>
				<br>
			</span>

		</div>
		

		
		<div id="settings">
		<a href='./administration.php?etage=<?=$choix_etage;?>'><img src='./images/settings.png' alt="Administration" ></a>
		<a href='./choisir_etage.php'><img src='./images/home.png' alt="Accueil"></a>
		</div>

	</div>



<script type="text/javascript"><!--


$(document).ready(function () {
   
	$('#mot_de_passe_id').focus(); //-->

});
</script>


</body>
</html>
