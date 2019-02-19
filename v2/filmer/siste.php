<?php

require_once('UKM/tv_files.class.php');

$files = new tv_files('place', API_MONSTRING);
$files->limit( 5 );

$filmer = [];
foreach( $files->getVideos() as $tv ) {
    var_dump( json_export::tv( $tv ) );
    $filmer[] = json_export::tv( $tv );
}

echo json_encode( $filmer );
die();