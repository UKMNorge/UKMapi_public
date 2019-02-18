<?php

require_once('UKM/monstringer.class.php');

$monstring = new monstring_v2( API_MONSTRING );

$personer = [];
foreach( $monstring->getInnslag()->getAll() as $innslag ) {
    foreach( $innslag->getPersoner()->getAll() as $person ) {
        $personer[] = json_export::person( $person );
    }
}

echo json_encode( $personer );
die();