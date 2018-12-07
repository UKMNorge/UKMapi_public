<?php

require_once('UKM/monstringer.class.php');
$fylker = [];
$monstringer = monstringer_v2::getFylkerInkludertFalske( API_SEASON );

foreach( $monstringer as $monstring ) {
    $fylker[] = json_export::monstring( $monstring );
}
echo json_encode( $fylker );
die();
