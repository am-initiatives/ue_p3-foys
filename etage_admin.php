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
    }
    else
    {
        // mode admin INactif
	$_SESSION['admin']=false;
	//echo "admin Nok";
	header('Location: ./administration.php');
    }
    
}
else
{
    $_SESSION['admin']=false;
    header('Location: ./administration.php');
}

?>
<html>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<head>
<title>Zdt zacachal's</title>
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
		left:10%;
		width:80%;
		height: auto;
	}
	
	#cachalot_div_id{
		display: block;
		position: absolute;
		top:25%;
		left:30%;
		width:40%;
		height: 180px;
		vertical-align: bottom;
		text-align: left;
		font-family: 'Zagoth', Old London, Consolas, Monaco, Courier New, Courier, monospace;
		/*font-size: 6em;*/
		text-align: center;
		background-color: <?=$etage['couleur1']?>;
		border: 1px solid #D0D0D0;
		color: <?=$etage['couleur3']?>;
	}
        
        #cachalot_div_id_tableau1{
            width: 100%;
            text-align: center;
        }
        
        #cachalot_div_id_tableau2{
            width: 100%;
        }
    
        #cachalot_div_id_tableau3{
            width: 100%;
            text-align: center;
        }
        
        #cachalot_div_id_tableau1 td{
        border-width:1px; 
        border-style:solid; 
        text-align: center;
        vertical-align: middle;
        }
        
        #cachalot_div_id_tableau2 td{
        border-width:1px; 
        border-style:solid; 
        text-align: center;
        vertical-align: middle;
        }
        #cachalot_div_id_tableau3 td,#cachalot_div_id_tableau3 th{
        border-width:1px; 
        border-style:solid; 
        text-align: center;
        vertical-align: middle;
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
	
	#pg_input {
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
		top:10px;
		right:10px;
	}
	
	.barred{
		text-decoration: line-through;
	}
	.centered{
		text-align: center;
                width: 100%;
	}
	</style>

	
	<script src="jquery_min.js"></script>
	<link rel="stylesheet" href="jquery-ui.css">
	<script src="jquery-ui.js"></script>
</head>
<body>
	
	
	
	<div id="container_id">

		<div id="info_id">

		</div>
		<div id="bucquage_input_id">
		    

			<form action="./gadz_accueil.php" method="post" id="pg_input_form">

			    <center>
				Aller administrer un PG avec le trigramme : <br>
			    <?php
			    if(isset($_GET['erreur']))
			    {
				if(is_numeric($_GET['erreur']))
				{
				    switch ($_GET['erreur'])
				    {
					case 0:
					echo "erreur0";
					break;
				    
					case 1:
					echo "erreur1";
					break;
				    
					case 2:
					echo "erreur2";
					break;
					
					default:
					echo "erreur3";
				    }
				}
			    }
			    ?>
                            <input type="password" name="pg_input" id="pg_input">
			    </center>
			</form>

		</div>
		<div id="cachalot_div_id">
			<span class="gros1">
			<br>
                        zident<br/><br/>
			<?php
			if($etage['nom_du_foys']!=""){
				echo $etage['nom_du_foys'];	
			}
			else{
				echo "Zacachal'sss (".$choix_etage."e)";
			}	
			
			?>
			<br><br><br>
			</span>

		</div>
		
		<div id="hcachalot_id">


                </div>

		<div id="operation_id">
                    <?php
                    if(isset($_SESSION['message_admin']) and $_SESSION['message_admin']!="")
                    {
                        echo "<center><h2>".$_SESSION['message_admin']."</h2></center><hr><hr>";
			$_SESSION['message_admin']="";
                    }
                    ?>
		    <center>
                    <br>
                    <form action='./etage_histo.php' method='post'>
			<input type='submit' value="Historique de l'&eacute;tage">
                        
                    </form>
                    <form action='./trouver_gadz.php' method='post'>
			<input type='submit' value="Retrouver le compte d'un Gadz">
                    </form>
		    
                    <br>
		    </center>
		    
                    <hr><hr>
                    <br>
                    <table id="cachalot_div_id_tableau1">
                        <caption>Produits disponible &agrave; l'&eacute;tage <?=$choix_etage;?> : </caption>
                        <tr>
                            <th>Nom produit</th><th>Volume (cl)</th><th>Pourcentage d'alcool (%)</th><th>Prix (&euro;)</th><th>Touche</th><th>Actions</th>
                        </tr>
                        <?php
				    $req = $bdd->prepare('SELECT * FROM produits WHERE etage = ?');
				    $req->execute(array($choix_etage));
				    while ($produit = $req->fetch())
				    {
					    echo "<tr><td>".$produit['nom']."</td><td>".$produit['volume']."</td> <td>".$produit['pourcentage_alcool']."</td><td>".$produit['prix']."</td><td>".$produit['touche']."</td>
                                            <td>
                                               <form action='./admin_operation.php' method='post'>
                                                    <input type='submit' value='modifier' disabled>
                                                </form> 
                                               <form action='./admin_operation.php' method='post'>
                                                    <input type='hidden' name='supprimer_produit' value='".$produit['id']."' >
                                                    <input type='submit' value='supprimer'>
                                                </form>
                                            </td></tr>";
				    }
                        
                        
                        ?>
                        <tr>
                            <form action="./admin_operation.php" method="post">
                                <td><input type="text" name="nom_produit" class="centered"></td>
                                <td><input type="text" name="volume" class="centered"></td>
                                <td><input type="text" name="pourcentage_alcool" class="centered"></td>
                                <td><input type="text" name="prix" class="centered"></td>
                                <td><input type="text" name="touche" class="centered"></td>
                                <td><input type="submit" name="nouveau_produit" value="Ajouter produit"></td>
                            </form>
                        </tr>
                    </table>
		    
                    <br>
<hr>
    <?php
    if($choix_etage=="-1")
    {
	?><a id="facture-inter-tbk"></a>
                    <table id="cachalot_div_id_tableau1">
                        <caption> Factures pour corriger la balance inter-TBK</caption>
                        <tr>
                            <th>Date</th><th>n° facture</th><th>Etage</th><th>Montant</th><th>Autres infos</th>
                        </tr>
                        <?php
				    $req = $bdd->prepare("SELECT * FROM operations WHERE operation_type = 'facture_tbk_balance'");
				    $req->execute(array($choix_etage));
				    while ($operation = $req->fetch())
				    {
					    echo "<tr><td>".$operation['operation_time']."</td><td>".$operation['operation_libelle']."</td> <td>".$operation['operation_etage']."</td><td>".$operation['operation_montant']."</td><td>".$operation['operation_description']."</td>
                                            <td>
                                               <form action='./admin_operation.php#facture-inter-tbk' method='post'>
                                                    <input type='hidden' name='supprimer_facture' value='".$operation['operation_id']."' >
                                                    <input type='submit' value='supprimer'>
                                                </form>
                                            </td></tr>";
				    }
                        
                        
                        ?>
                        <tr>
                            <form action="./admin_operation.php#facture-inter-tbk" method="post">
                                <td><input type="text" name="operation_time" value="<?php echo date("Y-m-d H:i:s");?>" class="centered"></td>
                                <td><input type="text" name="operation_libelle" class="centered"></td>
                                <td><select name="operation_etage" size=1>
                                               <option>1</option>
                                               <option>2</option>
                                               <option>3</option>
                                               <option>4</option>
                                               <option>5</option>
                                               <option selected>6</option>
                                            </select></td>
                                <td><input type="text" name="operation_montant" class="centered"></td>
                                <td><input type="text" name="operation_description" class="centered"></td>
                                <td><input type="submit" name="nouvelle_facture_inter_tbk" value="Ajouter facture inter-TBK"></td>
                            </form>
                        </tr>
                    </table>
		    <br>
		    <hr><hr>
		    <br>
		    <?php
    }
    else
    {
    ?>
    
    
    <hr><br>
                    <table id="cachalot_div_id_tableau2">
                        <caption>Comptes dont l'&eacute;tage <?=$choix_etage;?> est responsable :</caption>
                        <tr>
                            <th>trigramme</th><th>Bucque fam'sss prom'sss</th><th>Solde</th><th>id BDD</th>
                        </tr>
                        <?php
                        $req = $bdd->prepare('SELECT * FROM gadzarts WHERE gadzarts_etage_responsable = ? ORDER BY gadzarts_cachalot_solde');
                        $req->execute(array($choix_etage));
                        while ($gadz = $req->fetch())
                        {
			    
			    echo "<tr id='".$gadz['gadzarts_id']."' ";
                            if(!$gadz['gadzarts_cachalot_compte_actif'])
                            {
                                echo " class='barred' ";
                            }
                        
                            echo " title='".$gadz['gadzarts_nom']." ".$gadz['gadzarts_prenom']." : ".$gadz['gadzarts_mail']." ".$gadz['gadzarts_nums_blairal']."'><td >[<a href='./gadz_accueil.php?gadz_id=".$gadz['gadzarts_id']."'>".$gadz['gadzarts_cachalot_id']."</a>]</td><td>".$gadz['gadzarts_surnom']." ".$gadz['gadzarts_fams']." ".$gadz['gadzarts_tbk']." ".$gadz['gadzarts_proms']."</td><td>".$gadz['gadzarts_cachalot_solde']."</td><td>".$gadz['gadzarts_id']."</td><td>";
			    
			    if(!$gadz['gadzarts_cachalot_compte_actif'])
                            {
                                echo "<a href='admin_operation_sur_gadz.php?activer_compte=1&gadz_id=".$gadz['gadzarts_id']."'><img src='images/lecture.png' alt='Activer'></a>";
			    }
                            else
                            {
				echo "<a href='admin_operation_sur_gadz.php?desactiver_compte=1&gadz_id=".$gadz['gadzarts_id']."'><img src='images/pause.png' alt='D&eacute;sactiver'></a>";
			    }
			   echo " 
			    <a href='./etage_admin.php#".$gadz['gadzarts_id']."'><img src='images/modifier.png' alt='Modifier'></a>
			    <a href='admin_operation_sur_gadz.php?supprimer_compte=1&gadz_id=".$gadz['gadzarts_id']."' onClick=\"return confirm('Supprimer ce compte ?');\"><img src='images/supprimer.png' alt='Supprimer'></a>
			    </td></tr>";
                        }
                        ?>
                        
                    </table>
		    <br>
			
		    <br>
<center>
			<h3>Créer un nouveau compte</h3>
    <form action="./admin_operation.php" method="post">
	
	<table>
	    <tr><td style="text-align: right">Nom </td><td><input type="text" name="nom_gadz" class="centered"> </td> </tr>
	    <tr><td style="text-align: right">Pr&eacute;nom  </td><td> <input type="text" name="prenom_gadz" class="centered"> </td> </tr>
	    <tr><td style="text-align: right">Mail  </td><td> <input type="text" name="mail_gadz" class="centered"></td></tr>
	    <tr><td style="text-align: right">Bucque  </td><td> <input type="text" name="bucque_gadz" class="centered"> </td> <td>(la vraie)</td></tr>
	    <tr><td style="text-align: right">Fam's  </td><td> <input type="text" name="fams_gadz" class="centered"> </td> </tr>
	    <tr><td style="text-align: right">tbk  </td><td> <select name="tbk_gadz" size=1>
                                               <option>Ka</option>
                                               <option selected>An</option>
                                               <option>Li</option>
                                               <option>Cl</option>
                                               <option>Ch</option>
                                               <option>Bo</option>
                                               <option>Me</option>
                                               <option>KIN</option>
                                            </select></td> </tr>
	    <tr><td style="text-align: right">Prom's  </td><td><select name="proms_gadz" size=1>
                                                <?php
                                                for ($i = 150; $i <= 250; $i++) {
                                                    if($i!=212)
                                                    {
                                                        echo "<option>".$i;
                                                    }
                                                    else
                                                    {
                                                        echo "<option selected>".$i;
                                                    }
                                                }
                                                ?>
                                            </select></td> </tr>
	    <tr><td style="text-align: right">Bucque affich&eacute;e  </td><td> <input type="text" name="affichage_gadz" class="centered"></td> <td>(sur l'interface de bucquage)</td> </tr>
	    <tr><td style="text-align: right">Blairal   </td><td> <input type="text" name="blairal_gadz" class="centered"> </td> <td>(facultatif)</td></tr>
	    <tr><td style="text-align: right">Adresse   </td><td> <textarea name="adresse_gadz"> Rez P3</textarea></td> <td>(facultatif)</td></tr>
	    <tr><td style="text-align: right">trigramme </td><td><input type="text" name="trigramme_gadz" id="trigramme_gadz_id" class="centered"> </td><td>(lettre de a &agrave; z en minuscule et/ou chiffres,<br> pas de caract&egrave;res sp&eacute;ciaux)</td> </tr>
	</table>
	
	<input type="submit" name="creer_compte_gadz" value="Cr&eacute;er ce compte">
    </form>

</center>
<hr>
      <?php
    }
    ?>
<center>
<table id="cachalot_div_id_tableau3">
	    <tr><th>Etage</th><th>Balance des consos</th><th>Paiements pour balance</th><th>Balance r&eacute;actualis&eacute;e</th></tr>
	    <?php
	    //requette pour afficher le solde de chaque tbk
	    $reqe = $bdd->prepare("SELECT * FROM etages ORDER BY etage DESC");
	    $reqe->execute();

	    $total = 0;
	    $total_balances = 0;
	    while ($etage = $reqe->fetch())
	    {
	    	$req = $bdd->prepare("SELECT * FROM operations WHERE operation_type = 'facture_tbk_balance' AND operation_etage = ?");
		$req->execute(array($etage['etage']));
		$total_facture_balance = 0;
		while ($operation_tbk_balance = $req->fetch())
		{
		    $total_facture_balance+=$operation_tbk_balance['operation_montant'];
		}
		$req->closeCursor();
		$total_balances+=$total_facture_balance;
		$total+=$etage['solde'];
		if($etage['etage']=='-1')
		{
		    echo "<tr><td>".$etage['etage']."</td><td>".$etage['solde']."</td><td>".$total_facture_balance."</td><td>".($etage['solde']-$total_balances)."*</td></tr>";    
		}
		else
		{
		    echo "<tr><td>".$etage['etage']."</td><td>".$etage['solde']."</td><td>".$total_facture_balance."</td><td>".($etage['solde']+$total_facture_balance)."</td></tr>";    
		}

	    }
	    $reqe->closeCursor();
	    
	    ?>
	</table>
*si valeur n&eacute;gative : le -1 doit de l'argent (en ce qui concerne les balances)
	<?php
	    echo "<h1>Total des soldes : ".round($total,1)."</h1>";
?>

    
</center>
		</div>
		
		<div id="settings">
                    <a href='./deconnect_admin.php'><img src='./images/logout.png' alt="Se déconnecter"></a>
		</div>

	</div>



<script type="text/javascript"><!--


$(document).ready(function () {
   
	$('#pg_input').focus(); //-->
	
	
	$( "#pg_input" ).keyup(function( event ) {
	

		if (($("#pg_input").val().length>=3) || $("#pg_input").val()=='=') {
			$("#pg_input_form").submit();	
		}
	
	});
var droit_De_valider = false;
<?php

echo "var membres_cachalot = [";
$req = $bdd->prepare('SELECT gadzarts_cachalot_id FROM gadzarts');
$req->execute(array($choix_etage));
while ($row = $req->fetch())
{
    echo ",'".$row['gadzarts_cachalot_id']."'";						
}
echo "];";

?>


$( "#trigramme_gadz_id" ).submit(function( event ) {
    
    if(droit_De_valider)
    {
	$("#creation_compte_id").submit();	
    }
    else
    {    
    event.preventDefault();
    }    
    
});

$( "#trigramme_gadz_id" ).keyup(function( event ) {

	if ( jQuery.inArray($("#trigramme_gadz_id").val(),membres_cachalot)<0 && $("#trigramme_gadz_id").val()!='') {  //si l'identifiant a 3 caractères n'existe pas
                droit_De_valider = true;
	}
        else                                                                   // sinon
        {
                droit_De_valider = false;
		$("#trigramme_gadz_id").val('');
        }
    
});
    
});
</script>

</body>
</html>


