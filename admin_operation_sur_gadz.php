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

// GADZ
if(isset($_POST['gadz_id']) || isset($_GET['gadz_id'])) // si il y a un trigramme
{
    if(isset($_POST['gadz_id']) && $_POST['gadz_id']!="")
    {
	/* Requete SQL pour le Gadzarts en cours */
	$req = $bdd->prepare('SELECT * FROM gadzarts WHERE gadzarts_id = ?');
	$req->execute(array($_POST['gadz_id']));
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

// si un CREDIT a été envoyé par formulaire
if(isset($_POST['montant_credit']))
{
    if(is_numeric($_POST['montant_credit']) && $_POST['montant_credit']>0)
    {
	/* Requete SQL pour avoir les données de l'étage en cours*/
	$req = $bdd->prepare('SELECT * FROM operations WHERE operation_etage = ? AND operation_time = ?');
		$req->execute(array($choix_etage,$_POST['date_credit']));
	if ($req->rowCount() > 0 )
	{
	$_SESSION['message_admin'] = "Cette op&eacute;ration a d&eacute;j&agrave; &eacute;t&eacute; effectu&eacute;e en date du ".$_POST['date_credit']."<br>Attention &agrave; ne pas trop r&eacute;actualiser les pages.";
	$req->closeCursor();
	}
	else
	{
	    // on rajoute une opération de credit dans la BDD opération
	    insert_operation("credit",$choix_etage,$_POST['moyen_de_paiement'],"",$gadz['gadzarts_id'],$_POST['montant_credit'],$_POST['date_credit'],$bdd);
	    // on credite le PG
	    crediter_gadz($_POST['montant_credit'],$gadz,$bdd);
	    $req->closeCursor();
    
	    $_SESSION['message_admin'] ="Cr&eacute;dit ok";
	}			
    }
    else
    {
	$_SESSION['message_admin'] ="Le montant n'est pas un nombre ou n'est pas un nombre strictement positif.<br>Il faut utiliser un point en lieu et place de la virgule.";
    }

}
		
		
// si un DEBIT a été envoyé par formulaire
if(isset($_POST['montant_debit']))
{
    if(is_numeric($_POST['montant_debit']) && $_POST['montant_debit']>0)
    {
	// Requete SQL pour verifier si l'operation n'a pas ete bucquee deux fois
	$req = $bdd->prepare('SELECT * FROM operations WHERE operation_etage = ? AND operation_time = ?');
	$req->execute(array($choix_etage,$_POST['date_debit']));
	if ($req->rowCount() > 0 )
	{
		$_SESSION['message_admin'] = "Cette op&eacute;ration a d&eacute;j&agrave; &eacute;t&eacute; effectué&eacute; en date du ".$_POST['date_debit']."<br>Attention &agrave; ne pas trop r&eacute;actualiser les pages.";
		$req->closeCursor();
	}
	else
	{
		// on rajoute une opération de debit dans la BDD opération
		insert_operation("bucquage",$choix_etage,$_POST['description_debit'],"",$gadz['gadzarts_id'],-abs($_POST['montant_debit']),$_POST['date_debit'],$bdd);
		// on debiter le PG
		debiter_gadz($_POST['montant_debit'],$gadz,$bdd);
		$req->closeCursor();
    
    
		crediter_etage(abs($_POST['montant_debit']),$etage,$bdd);
    
		$reqo = $bdd->prepare('SELECT * FROM etages WHERE etage = ? LIMIT 1');
		$reqo->execute(array($gadz['gadzarts_etage_responsable']));
		$etage_origine=$reqo->fetch();
		$reqo->closeCursor();
		debiter_etage(abs($_POST['montant_debit']),$etage_origine,$bdd);
    
		$_SESSION['message_admin'] ="D&eacute;bit ok";
    
	}
    }
    else
    {
	$_SESSION['message_admin'] ="Le montant n'est pas un nombre ou n'est pas un nombre strictement positif.<br>Il faut utiliser un point en lieu et place de la virgule.";
    }

}
		
		
if(isset($_GET['desactiver_compte']))
{
    $req = $bdd->prepare('UPDATE gadzarts SET gadzarts_cachalot_compte_actif = 0 WHERE gadzarts_id =?');
    $req->execute(array($gadz['gadzarts_id']));
    $_SESSION['message_admin'] = "Le compte de ".$gadz['gadzarts_surnom']." ".$gadz['gadzarts_fams']." ".$gadz['gadzarts_tbk']." ".$gadz['gadzarts_proms']." est d&eacute;sactiv&eacute;.";

    header('Location: ./etage_admin.php#'.$gadz['gadzarts_id']);
    exit;
}

if(isset($_GET['activer_compte']))
{
    $req = $bdd->prepare('UPDATE gadzarts SET gadzarts_cachalot_compte_actif = 1 WHERE gadzarts_id =?');
    $req->execute(array($gadz['gadzarts_id']));
    $_SESSION['message_admin'] = "Le compte de ".$gadz['gadzarts_surnom']." ".$gadz['gadzarts_fams']." ".$gadz['gadzarts_tbk']." ".$gadz['gadzarts_proms']." est activ&eacute;.";

    header('Location: ./etage_admin.php#'.$gadz['gadzarts_id']);
    exit;
}


if(isset($_GET['supprimer_compte']))
{
    $req = $bdd->prepare('SELECT * FROM operations WHERE operation_gadzarts_id = ?');
    $req->execute(array($gadz['gadzarts_id']));
    if ($req->rowCount() == 0) // si il n'y a aucune opération enregistrée 
    {
	$req = $bdd->prepare("DELETE FROM gadzarts WHERE gadzarts_id = ?");
	$req->execute(array($gadz['gadzarts_id']));   
	$req->closeCursor();
	
	$_SESSION['message_admin'] = "Tu viens de supprimer un compte.";
    }
    else
    {
	$_SESSION['message_admin'] = "Ce compte ne peut pas être supprim&eacute; : il possède des opérations dans la BDD.
	
	<script type='text/javascript'>
	alert('Ce compte ne peut pas être supprimé : il possède des opérations dans la BDD.');
	</script>";
    }
    
    
    header('Location: ./etage_admin.php#'.$gadz['gadzarts_id']);
    exit;
}
		
header('Location: ./gadz_accueil.php?gadz_id='.$gadz['gadzarts_id']);
