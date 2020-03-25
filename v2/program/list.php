<?php

use UKMNorge\Arrangement\Arrangement;

require_once('UKM/Autoloader.php');

if( !API_MONSTRING ) {
	die('false');
}

$monstring = new Arrangement( API_MONSTRING );
$program = $monstring->getProgram()->getAllInkludertInterne();

$hendelser = [];
foreach( $program as $hendelse ) {
    $hendelser[] = json_export::hendelse( $hendelse );
}

echo json_encode( $hendelser );
die();
