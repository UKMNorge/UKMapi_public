<?php

use UKMNorge\Arrangement\Arrangement;

require_once('UKM/Autoloader.php');

$monstring = new Arrangement( API_MONSTRING );

$personer = [];
foreach( $monstring->getInnslag()->getAll() as $innslag ) {
    foreach( $innslag->getPersoner()->getAll() as $person ) {
        $personer[] = json_export::person( $person );
    }
}

echo json_encode( $personer );
die();