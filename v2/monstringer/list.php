<?php

$fylker = [];
$monstringer = monstringer_v2::getFylker( API_SEASON );
foreach( $monstringer as $monstring ) {
    $fylker[] = json_export::monstring( $monstring );
}
echo json_encode( $fylker );
die();