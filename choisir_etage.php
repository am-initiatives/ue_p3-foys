<?php
session_start();

if(isset($_GET['etage']))
{
	$choix_etage=$_GET['etage']; 
	if(($choix_etage<=7 AND $choix_etage>=1) OR $choix_etage==-1)
	{
		setcookie('etage', $choix_etage , time() + 365*24*3600, null, null, false, true);
		header('Location: ./');
	}
	else
	{
		header('Location: ./choisir_etage.php?erreur=1');
		exit;
	}
}




//ADMIN
if(isset($_SESSION['admin']))
{
    if($_SESSION['admin']==true)
    {
	header('Location: ./index.php');
    }
    
}

?>

<html><head><meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>cachalot</title>
<style type="text/css">

	body {
		background-color: #800080;
		margin: 5px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}
	
	#cachalot_div_id{
		display: block;
		margin-left: auto;
		margin-right: auto;
		margin-top: 100px;
		width: 500px;
		vertical-align: bottom;
		text-align: left;
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 1em;
		text-align: center;
		background-color: #DE9816;
		border: 1px solid #D0D0D0;
		color: #ffffff;
	}
	
	#valider_id {
		font-family: Old London, Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 21px;
		color: #000000;
		background-color: #ffffff;
		margin: 3px;
	}

	</style>
</head>
<body>
	
	
	
	<div id="container_id">
		
		<div id="cachalot_div_id">

			<!--<a href="./choisir_etage.php?etage=6"><input type="submit" value="6" width="21"></a>
			<a href="./choisir_etage.php?etage=5"><input type="submit" value="5" width="21"></a>
			<a href="./choisir_etage.php?etage=4"><input type="submit" value="4" width="21"></a>
			<a href="./choisir_etage.php?etage=3"><input type="submit" value="3" width="21"></a>
			<a href="./choisir_etage.php?etage=2"><input type="submit" value="2" width="21"></a>
			<a href="./choisir_etage.php?etage=1"><input type="submit" value="1" width="21"></a>
			<a href="./choisir_etage.php?etage=-1"><input type="submit" value="-1" width="21"></a>-->
			<a href="./choisir_etage.php?etage=7"><input type="submit" value="7" width="21"></a>
			<a href="./administration.php"><input type="submit" value="administrateur" width="21"></a>

			
		</div>


	</div>








</body></html>
