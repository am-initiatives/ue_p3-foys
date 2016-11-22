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

if(isset($_POST['mot_de_passe']))
{
    if($_POST['mot_de_passe']!="")
    {
        if($_POST['mot_de_passe']==$etage['motdepasse'])
        {
            // administrateur identifie
            $_SESSION['admin']=true;
            header('Location: ./etage_admin.php');
        }
        else
        {
            $_SESSION['admin']=false;
            header('Location: ./administration.php?erreur=2');
            exit;
        }
    }
    else
    {
        $_SESSION['admin']=false;
        header('Location: ./administration.php?erreur=1');
        exit;
    }
}
else
{
    $_SESSION['admin']=false;
    header('Location: ./administration.php?erreur=0');
    exit;
}