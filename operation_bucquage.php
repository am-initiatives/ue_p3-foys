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


// GADZ
if(isset($_POST['pg_id'])) // si il y a un ID de PG
{
    /* Requete SQL pour le Gadzarts en cours */
    $req = $bdd->prepare('SELECT * FROM gadzarts WHERE gadzarts_id = ?');
    $req->execute(array($_POST['pg_id']));
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



/* Traitement des données*/
$commande=strtoupper($_POST['choix_conso']);
$commande = preg_split('//', $commande, -1, PREG_SPLIT_NO_EMPTY);
$volume_alcool_de_la_commande=0;

$i=0;
$requette_finale="";
$lettres_inutiles="";
$prix_commande=0;
foreach ($commande as $lettre) // pour chaque produit
{
    $i++;
    
    echo "<br>".$i." :";
    $req = $bdd->prepare('SELECT * FROM produits WHERE touche = ? AND etage = ? LIMIT 1');
    $req->execute(array($lettre,$choix_etage));

    if ($req->rowCount() == 1)
    {
        $produit=$req->fetch();
        $req->closeCursor();
        
        
        echo " ".$produit['prix']."&euro;";
        
        
        $requette_finale.=$lettre;
        $prix_commande+=$produit['prix'];
        $volume_alcool_de_la_commande+=$produit['volume']*$produit['pourcentage_alcool']/100;
    }
    else
    {
        $req->closeCursor();
        $lettres_inutiles.=$lettre;
        
    }
}

if($prix_commande>0)
{
    // insertion d'une nouvelle ligne dans la table opération pour garder un historique
    insert_operation("bucquage",$choix_etage,$requette_finale,$volume_alcool_de_la_commande,$gadz['gadzarts_id'],-$prix_commande,$_POST['date_conso'],$bdd);
    
    // débiter le gadz
    debiter_gadz($prix_commande,$gadz,$bdd);
    // aumgenter le niveau du gadz
    crediter_gadz_alcool($volume_alcool_de_la_commande,$gadz,$bdd);
    
    // créditer l'étage d'accueil
    crediter_etage($prix_commande,$etage,$bdd);
    
    // débiter l'étage d'origine
        $req = $bdd->prepare('SELECT * FROM etages WHERE etage = ? LIMIT 1');
        $req->execute(array($gadz['gadzarts_etage_responsable']));
        $etage_origine=$req->fetch();
        $req->closeCursor();
    debiter_etage($prix_commande,$etage_origine,$bdd);    
}

if($requette_finale != "")
{
    $retour="?bucquage=".$requette_finale;
    
    
    if($lettres_inutiles!="")
    {
	$retour.="&lettres_inutiles=".$lettres_inutiles;
    }
}
else
{
    if($lettres_inutiles!="")
    {
	$retour.="?lettres_inutiles=".$lettres_inutiles;
    }
}





header('Location: ./'.$retour);
exit;
