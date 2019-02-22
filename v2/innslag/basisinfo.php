<?php

require_once('UKM/monstringer.class.php');

$monstring = new monstring_v2( API_MONSTRING );

$innslag = $monstring->getInnslag()->get( API_FINDBY_ID );
$export = json_export::innslag( $innslag );
echo json_encode( $export );
die();
