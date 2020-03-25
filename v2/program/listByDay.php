<?php

use UKMNorge\Arrangement\Arrangement;

require_once('UKM/Autoloader.php');

if( !API_MONSTRING ) {
	die('false');
}

$monstring = new Arrangement( API_MONSTRING );

$program = $monstring->getProgram()->sorterPerDag( $monstring->getProgram()->getAllInkludertInterne() );

$dager = [];
$hendelser = [];
foreach( $program as $date => $day ) {
	
	$dag = new stdClass();
	$dag->id = $date;
	$dag->dato = json_export::dato( $day->date );
	$dag->hendelser = [];
	
	foreach( $day->forestillinger as $hendelse ) {
		$dag->hendelser[] = $hendelse->getId();
		$hendelser[] = json_export::hendelse( $hendelse );
	}
	
	$dager[] = $dag;
}

$data = new stdClass();
$data->dager = $dager;
$data->hendelser = $hendelser;

echo json_encode( $data );
die();
