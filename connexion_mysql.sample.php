<?php

try
{
// On se connecte à MySQL, penser à modifier les valeurs de host / dbname / password
$bdd = new PDO('mysql:host=server;dbname=dbname', 'dbname', 'password');
}
catch(Exception $e)
{
// En cas d'erreur, on affiche un message et on arrête tout
die('Erreur : '.$e->getMessage());
}


?>