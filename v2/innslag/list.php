<?php

require_once('UKM/monstringer.class.php');

$monstring = new monstring_v2( API_MONSTRING );

$export = [];
foreach( $monstring->getInnslag()->getAll() as $innslag ) {
    $export[] = json_export::innslag( $innslag );
}

echo json_encode( $export );
die();