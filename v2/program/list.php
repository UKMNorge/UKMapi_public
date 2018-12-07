<?php

require_once('UKM/monstring.class.php');

if( !API_MONSTRING ) {
	die('false');
}

$monstring = new monstring_v2( API_MONSTRING );
$program = $monstring->getProgram()->getAllInkludertInterne();

$hendelser = [];
foreach( $program as $hendelse ) {
    $hendelser[] = json_export::hendelse( $hendelse );
}

echo json_encode( $hendelser );
die();
