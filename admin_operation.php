<?php
session_start();
/* Connexion SQL */
require_once("./connexion_mysql.php");
require_once("./fonctions.php");
$_SESSION['message_admin']="";
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
	echo "admin Nok";
	header('Location: ./administration.php');
    }
    
}
else
{
    $_SESSION['admin']=false;
    header('Location: ./administration.php');
}


//./etage_admin.php#".$gadz['gadzarts_id']."


if(isset($_POST['nouveau_produit']))
{
    // on vérifie si les champs sont corrects (chiffre, limite de taille)
    if($_POST['nom_produit']!="" && is_numeric($_POST['volume']) && is_numeric($_POST['pourcentage_alcool']) && is_numeric($_POST['prix']) && is_string($_POST['touche']))
    {
    
        /* Requete SQL pour vérififier l'unicité de la touche proposée */
        $req = $bdd->prepare('SELECT * FROM produits WHERE touche = ? AND etage = ?');
        $req->execute(array($_POST['touche'],$choix_etage));
        if ($req->rowCount() == 0)
        {
        /* Requete SQL pour ajouter nouveau produit*/
        $req2 = $bdd->prepare("INSERT INTO produits (id, nom, volume, pourcentage_alcool, prix, etage, touche) VALUES (NULL, ?, ?, ?, ?, ?, ?)");
        $req2->execute(array($_POST['nom_produit'],$_POST['volume'],$_POST['pourcentage_alcool'],$_POST['prix'],$choix_etage,strtoupper($_POST['touche'])));
        $req2->closeCursor();
        }
        else
        {
            $_SESSION['message_admin']="Lettre d&eacute;j&agrave; utilis&eacute;e";
        }
        $req->closeCursor();
        
    }
    else
    {
            $_SESSION['message_admin']="Ajout de produit impossible : un des champs n'est pas valide. <br>(mettre un point pour faire une virgule)";
    }
}

if(isset($_POST['supprimer_produit']))
{
    $req = $bdd->prepare("DELETE FROM produits WHERE id = ?");
    $req->execute(array($_POST['supprimer_produit']*1));   
    $req->closeCursor();
}


if(isset($_POST['nouvelle_facture_inter_tbk'])) // si nouvelle facture du tonton -1 inter tbk	
{
    

    
    
  $req = $bdd->prepare("INSERT INTO operations (
		       operation_id,
		       operation_type,
		       operation_etage,
		       operation_libelle,
		       operation_description,
		       operation_gadzarts_id,
		       operation_time,
		       operation_montant,
		       operation_annulee,
		       operation_comptabilisee)
    VALUES (
			NULL ,
			'facture_tbk_balance',
			?,
			?,
			?,
			0,
			?,
			?,
			0,
			0)");
    
				    $req->execute(array($_POST['operation_etage'],
							$_POST['operation_libelle'],
							$_POST['operation_description'],
							$_POST['operation_time'],
							$_POST['operation_montant']));
				    $req->closeCursor();
				    
				    $_SESSION['message_admin'] = "Nouvelle facture inter_TBK ok (n°".$_POST['operation_libelle'].")";
}

if(isset($_POST['supprimer_facture'])) // supprimer facture
{
    $req = $bdd->prepare("DELETE FROM operations WHERE operation_id = ?");
    $req->execute(array($_POST['supprimer_facture']*1));      
    $req->closeCursor();
}


if(isset($_POST['creer_compte_gadz']))
{
    
    if($_POST['nom_gadz']!="")
    {
	if($_POST['prenom_gadz']!="")
	{
	    if($_POST['mail_gadz']!="")
	    {
		if($_POST['bucque_gadz']!="")
		{
		    if($_POST['fams_gadz']!="")
		    {
			if($_POST['affichage_gadz']!="")
			{
			    $regex="&^[a-z0-9]{3}$&";
			    if($_POST['trigramme_gadz']!="" && preg_match($regex,$_POST['trigramme_gadz']) )
			    {
				// on vérifie si le trigramme n'est pas déjà existant dans la BDD (malgré la protection en javascript)
				
				$req = $bdd->prepare('SELECT * FROM gadzarts WHERE gadzarts_cachalot_id = ?');
				$req->execute(array($_POST['trigramme_gadz']));
				if ($req->rowCount() == 0) // si il n'y en a aucun
				{

				    $req2 = $bdd->prepare("INSERT INTO gadzarts (gadzarts_id ,gadzarts_nom ,gadzarts_prenom ,gadzarts_mail ,gadzarts_surnom ,
    gadzarts_cachalot_affichage ,gadzarts_fams ,gadzarts_proms ,gadzarts_tbk ,gadzarts_cachalot_compte_actif ,gadzarts_cachalot_solde ,gadzarts_cachalot_date_negats ,
    gadzarts_cachalot_avatar ,gadzarts_cachalot_volume_alcool ,gadzarts_nums_blairal ,gadzarts_adresse ,gadzarts_cachalot_id ,gadzarts_etage_responsable)
    VALUES (NULL ,?,?,?,?,?,?,?,?,'1','0','0000-00-00 00:00:00','','0',?,?,?,?)");
    
				    $req2->execute(array($_POST['nom_gadz'],$_POST['prenom_gadz'],
							 $_POST['mail_gadz'],$_POST['bucque_gadz'],
							 $_POST['affichage_gadz'],$_POST['fams_gadz'],
							 $_POST['proms_gadz'],$_POST['tbk_gadz'],
							 $_POST['blairal_gadz'],$_POST['adresse_gadz'],
							 $_POST['trigramme_gadz'],$choix_etage));
				    $req2->closeCursor();
				    
				    $_SESSION['message_admin'] = "Cr&eacute;ation du compte ok !";
				    
				    
				    
				    
				    
				    $to  = $_POST['mail_gadz']; 
			       
				    // Sujet
				    $subject = "Obtention d'un compte foy'sss a la rez P3";
			       
				    // message
				    $messageMAIL = "
				    <html>
				     <head>
				      <title>Identifiant & r&egrave;gles </title>
				     </head>
				     <body>
					    <h1><p>Sal'sss ".$_POST['bucque_gadz']." ".$_POST['fams_gadz']." ".$_POST['tbk_gadz']." ".$_POST['proms_gadz']." !</p></h1>
    
					    Tu viens d'obtenir un nouveau compte sur Foysss.fr (application en ligne de bucquage de la Rez P3).<br/>
					    Pour te bucquer une conso il te suffira de taper ton trigramme sur la page d'accueil.<br/>
					    <br>
					    <h3>Ton trigramme est : ".$_POST['trigramme_gadz']."</h3><br>
					    <br>
					    Pour te bucquer une conso il suffira d'appuyer sur la lettre correspondante autant de fois que tu prendras cette conso.<br>
					    <br>
					    Les n&eacute;gat'sss ne sont pas autoris&eacute;s &agrave; P3, tu auras 24h pour repasser en posit'sss apr&egrave;s une soir&eacute;e si tu ne veux pas que ton compte soit bloqu&eacute;.
					    <br><br>
					    
					    Bonne cuvance,<br>
					    Fraternellement,<br>
					    <br>
					    <b>Les Zi Foy'sss.</b>
					    <br>&nbsp;
					    <br>&nbsp;<br>
					    AI : Gorgu is watching you...
				     </body>
				    </html>
				    ";
			       
				    // Pour envoyer un mail HTML, l'en-tete Content-type doit etre defini
				    $headers  = 'MIME-Version: 1.0' . "\r\n";
				    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			       
				    // En-tetes additionnels
				    $headers .= "From: gorgu@clocheton.an <gorgu@clocheton.an>\r\n";
				    $headers .= 'Bcc: joris.guerry@gadz.org' . "\r\n";
				    // Envoi
				    if(mail($to, $subject, $messageMAIL, $headers))
				    {
					$_SESSION['message_admin'].="<br>Mail envoy&eacute;";
				    }
				    else
				    {
					$_SESSION['message_admin'].="<br>Erreur lors de l'envoi du mail.";
				    }
				
				}
				else
				{
				    $_SESSION['message_admin']="Cr&eacute;ation de compte annul&eacute;e";
				    $_SESSION['message_admin'].="</h1><br>Trigramme d&eacute;j&agrave; existant";
				}
				$req->closeCursor();
				
			    }
			    else
			    {
				$_SESSION['message_admin']="Cr&eacute;ation de compte annul&eacute;e";
				$_SESSION['message_admin'].="</h1><br>Trigramme vide ou incorrect (minuscules sans accent et chiffres seulement)";
			    }
			}
			else
			{
			    $_SESSION['message_admin']="Cr&eacute;ation de compte annul&eacute;e";
			    $_SESSION['message_admin'].="</h1><br>Bucque d'affichage vide";
			}
		    }
		    else
		    {
			$_SESSION['message_admin']="Cr&eacute;ation de compte annul&eacute;e";
			$_SESSION['message_admin'].="</h1><br>fams vide";
		    }
		}
		else
		{
		    $_SESSION['message_admin']="Cr&eacute;ation de compte annul&eacute;e";
		    $_SESSION['message_admin'].="</h1><br>Bucque vide";
		}
	    }
	    else
	    {
		$_SESSION['message_admin']="Cr&eacute;ation de compte annul&eacute;e";
		$_SESSION['message_admin'].="</h1><br>Mail vide";
	    }
	}
	else
	{
	    $_SESSION['message_admin']="Cr&eacute;ation de compte annul&eacute;e";
	    $_SESSION['message_admin'].="</h1><br>Pr&eacute;nom vide";
	}
    }
    else
    {
	$_SESSION['message_admin']="Cr&eacute;ation de compte annul&eacute;e";
	$_SESSION['message_admin'].="</h1><br>Nom vide";
    }
}



	header('Location: ./etage_admin.php');