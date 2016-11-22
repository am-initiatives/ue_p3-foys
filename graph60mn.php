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
	$MAINetage=$req->fetch();
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

require_once( 'jpgraph/jpgraph.php' );
require_once( 'jpgraph/jpgraph_line.php' );


class MyAquaTheme extends AquaTheme
{
    function __construct() {
        $this->font_color       = '#000000';
        $this->background_color = '#FD6C9E';
        $this->axis_color       = '#000000';
        $this->grid_color       = '#000000';
    }
  
    function SetupGraph($graph) {
        parent::SetupGraph($graph);
        $graph->yaxis->HideLine(false);
        $graph->yaxis->HideTicks(false,false);
    }
}


// Setup the graph
$graph = new Graph( 600, 250 );
$graph->SetScale( "textlin" );


$theme_class = new UniversalTheme;

$graph->SetTheme( $theme_class );
$graph->img->SetAntiAliasing( false );
//$graph->title->Set( 'Lattomètre' );
$graph->SetBox( false );

$graph->img->SetAntiAliasing();


$graph->yaxis->SetTitle("cL d'alcool pur");
$graph->yaxis->HideZeroLabel();
$graph->yaxis->HideLine( false );
$graph->yaxis->HideTicks( false, false );

$graph->xgrid->Show();
$graph->xgrid->SetLineStyle( "solid" );


$labelx = array();

for( $i = 60; $i > 0 ; $i-- )
{
		$date_inf = new DateTime;
		$date_inf->modify( "-$i minute" );
		$date_inf_req = $date_inf->format( 'H:i' );
$labelx[]=$date_inf_req;
}	

$graph->xaxis->SetTickLabels( $labelx );
$graph->xgrid->SetColor( '#E3E3E3' );
$graph->xaxis->SetTextLabelInterval(5);

$graph->xaxis->SetColor($MAINetage['couleur2']); 
$graph->yaxis->SetColor($MAINetage['couleur2']); 



$datay = array();
$reqe = $bdd->prepare( "SELECT * FROM etages ORDER BY etage DESC" );
$reqe->execute();
while( $etage = $reqe->fetch() )
{
	for( $i = 60; $i > 0 ; $i-- ) // une boucle par heure
	{
		$j=$i-1;
		$date_inf = new DateTime;
		$date_inf->modify( "-$i minute" );
		$date_inf_req = $date_inf->format( 'Y-m-d H:i:s' );
		$date_sup = new DateTime;
		$date_sup->modify( "-$j minute" );
		$date_sup_req = $date_sup->format( 'Y-m-d H:i:s' );



		$req = $bdd->prepare( "SELECT * FROM operations WHERE operation_type = 'bucquage' AND operation_etage = ? AND operation_annulee = 0 AND operation_time >= ? AND operation_time < ?" );


		$req->execute( array( $etage['etage'], $date_inf_req, $date_sup_req));

		$total_alcool = 0;
		
		//echo "Etage ".$etage['etage']." between ".$date_inf_req." and ".$date_sup_req."<br>";
		while( $operation = $req->fetch() ) // pour toutes les opérations dans l'heure
		{
			//echo "Etage ".$etage['etage']." between ".$date_inf_req." and ".$date_sup_req."<br>";
			//echo $operation['operation_description']."<br>";
			$total_alcool += floatval( $operation['operation_description'] );
		}
		$req->closeCursor();
		
		$datay[] = $total_alcool; // on ajoute la valeur au tableau

	
	}
	//on a ici un tableau qui contient toutes les valeurs de conso par heure
	
	// Create the line
	$p = new LinePlot( $datay );
	$graph->Add( $p );
	$p->SetWeight( 2 );
	$p->SetColor( $etage['couleur2']);
	$p->SetLegend( $etage['tbk_responsable'] );
	$p->mark->SetType(MARK_FILLEDCIRCLE,'',1.0);
	$p->mark->SetColor($etage['couleur1']);
	$p->mark->SetFillColor($etage['couleur1']);
	$p->SetCenter();
	$datay = array();
}
$reqe->closeCursor();







$graph->SetFrame(true,'black',2);

$graph->legend->SetColumns(7);
$graph->legend->SetFrameWeight(4);
$graph->SetMarginColor($MAINetage['couleur1']);
// Output line
$graph->Stroke();

?>
