<?php
    
/* Connexion SQL */
require_once("./connexion_mysql.php");
			    
    



?>
<html>
    <head><title>MAJ balance</title></head>
    <body>
	<table>
	    <tr><th>Etage</th><th>Solde</th></tr>
	    <?php
	    //requette pour afficher le solde de chaque tbk
	    $req = $bdd->prepare("SELECT * FROM etages");
	    $req->execute();
	    
	    $total = 0;
	    while ($etage = $req->fetch())
	    {
		$total+=$etage['solde'];
		echo "<tr><td>".$etage['etage']."</td><td>".$etage['solde']."</td></tr>";
	    }
	    $req->closeCursor();
	    
	    ?>
	</table>
	<?php
	    echo "<h1>Total des soldes : ".round($total,1)."</h1>";
	?>
	
    </body>
</html>


