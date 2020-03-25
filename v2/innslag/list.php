<?php

use UKMNorge\Arrangement\Arrangement;

require_once('UKM/Autoloader.php');

$monstring = new Arrangement( API_MONSTRING );

$export = [];
foreach( $monstring->getInnslag()->getAll() as $innslag ) {
    $export[] = json_export::innslag( $innslag );
}

echo json_encode( $export );
die();