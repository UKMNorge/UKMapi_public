<?php

use UKMNorge\Arrangement\Arrangement;

require_once('UKM/Autoloader.php');

$monstring = new Arrangement( API_MONSTRING );

$innslag = $monstring->getInnslag()->get( API_FINDBY_ID );
$export = json_export::innslag( $innslag );
echo json_encode( $export );
die();
