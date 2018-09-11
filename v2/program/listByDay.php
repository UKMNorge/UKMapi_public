<?php

require_once('UKM/monstring.class.php');

if( !API_ID ) {
	die('false');
}

$monstring = new monstring_v2( API_MONSTRING );

$program = $monstring->getProgram()->sorterPerDag( $monstring->getProgram()->getAllInkludertInterne() );

$dager = [];
foreach( $program as $date => $day ) {
	
	$dag = new stdClass();
	$dag->id = $date;
	$dag->dato = json_export::dato( $day->date );
	$dag->hendelser = [];
	
	foreach( $day->forestillinger as $hendelse ) {
		$dag->hendelser[] = json_export::hendelse( $hendelse );
	}
	
	$dager[] = $dag;
}

echo json_encode( $dager );
die();
