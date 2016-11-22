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
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>ZACACHAL'SSS Bucquer ch&egrave;que d'un PG (Admin)</title>
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
		color: #4F5155;
	}

	#container_id{}




	#cachalot_div_id{
		display: block;
		position: absolute;
		top:10%;
		left:20%;
		width:60%;
		vertical-align: bottom;
		text-align: left;
		font-family: 'Zagoth', Old London, Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 2em;
		background-color: <?=$etage['couleur1']?>;
		border: 1px solid #D0D0D0;
		color: <?=$etage['couleur3']?>;
		padding: 15px;
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

	#password_input_id {
		font-family: 'Zagoth', Old London, Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 50px;
		color: <?=$etage['couleur3']?>;
		background-color: <?=$etage['couleur4']?>;
		text-align: center;
		margin: 3px;
		width:80%;
	}

	#settings {

		display: block;
		position: absolute;
		right:0px;
	}

	.choix_gestion {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
	}
.lien_trigramme{
            text-decoration: underline;
            background-color: <?=$etage['couleur3']?>;
            color: <?=$etage['couleur1']?>;
}
        #resultats,ul{
                margin: 0;
                padding: 0;
		background-color: <?=$etage['couleur3']?>;
		color: <?=$etage['couleur1']?>;
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
                line-height: 1.5em;
        }
        #resultats{
            border: 1px solid <?=$etage['couleur3']?>;
        }

        #resultats li{
            list-style-type: none;
            margin: 0;
            padding: 0;
        }


        #resultats ul li span{
            text-decoration: none;
            background-color: <?=$etage['couleur3']?>;
            color: <?=$etage['couleur1']?>;
            display: block;
        }

        #resultats ul li span:hover
        {
		background-color: <?=$etage['couleur1']?>;
		color: <?=$etage['couleur3']?>;
        }
        #gadz_selection_id{
            text-align: center;
        }


	</style>


	<script src="./jquery-1.10.2.min"></script>
</head>
<body>



	<div id="container_id">


		<div id="cachalot_div_id">






                        Recherche : <input type="text" name="recherche" id="recherche_id" value=""><input type='submit' value='Effacer' id='clear'> <br/>- &eacute;crire en minuscule<br/>- respecter les espaces<br/>- pour rechercher un "id" taper "id" suivi du num&eacute;ro et terminer par di&egrave;se : id21# par exemple)</br></br>



                        <form action="#" method="" id="buquer_cheque_PG">

                            <center>PG :<br><br>
                            <div id="resultats">
                                <ul>
                                    <?php
				    $nb_de_pg=0;
                        $req = $bdd->prepare('SELECT * FROM gadzarts  ORDER BY gadzarts_fams');
                        $req->execute(array($choix_etage));
                        while ($gadzarts = $req->fetch())
			{
                                                    echo "<li><span id='".$gadzarts['gadzarts_id']."'>".strtolower($gadzarts['gadzarts_surnom'])." ".$gadzarts['gadzarts_fams']." ".$gadzarts['gadzarts_tbk']." ".$gadzarts['gadzarts_proms']." (<a class='lien_trigramme' href='./gadz_accueil.php?gadz_id=".$gadzarts['gadzarts_id']."'>".$gadzarts['gadzarts_cachalot_id']."</a>) id".$gadzarts['gadzarts_id']."#</span></li>";
                                                }
                                                //$nb_de_pg+=($query->num_rows());


                                    ?>
                                </ul>
                            </div>
                            <br>
                            </center>


			</form>

		</div>

		<div id="settings">
                	<a href='./deconnect_admin.php'><img src='./images/logout.png' alt="Se déconnecter"></a>
			<a href='./index.php'><img src='./images/home.png' alt="Accueil"></a>
		</div>


	</div>


<script type="text/javascript"><!--
$('#recherche_id').focus();


$(document).ready(function () {

    $('#recherche_id').keyup(function () {



        var input_content = $.trim($(this).val());


        if (!input_content) {
            $('ul>li').show();
        } else {
            $('ul>li').show().not(':contains(' + input_content  + ')').hide();
        }
    });

    $('span').click(function(){
        var gadzarts_id=$(this).attr('id');
        var gadzarts_selection=$(this).text();
        $('#gadz_selection_id').val(gadzarts_selection);
        $('#gadz_selection_id').attr('size',gadzarts_selection.length+5);
        $('#recherche_id').val(gadzarts_selection);
        $('#recherche_id').attr('size',gadzarts_selection.length+5);
        $('#recherche_id').keyup();
        $('#gadzarts_id').val(gadzarts_id);
        $('#montant_operation_id').focus();
    });

    $('#clear').click(function(){
        $('#recherche_id').val('');
        $('#recherche_id').keyup();
        $('#gadzarts_id').val('');
        $('#gadz_selection_id').val('');
	event.preventDefault();
        $('#recherche_id').focus();

     });


});
</script>


</body>
</html>




