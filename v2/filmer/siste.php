<?php

require_once('UKM/tv_files.class.php');

define('LIMIT', 5);
$files = new tv_files('place', API_MONSTRING);
$files->limit( LIMIT );

$filmer = [];
$counter = 0;
while( $tv = $files->fetch() ) {
    $counter++;
    $filmer[] = json_export::tv( $tv );
    if( $counter >= LIMIT ) {
        break;
    }
}

echo json_encode( $filmer );
die();