<?php

require_once('UKM/tv_files.class.php');

$files = new tv_files('place', API_MONSTRING);

$filmer = [];
while( $tv = $files->getVideos() ) {
    $filmer[] = json_export::tv( $tv );
}

echo json_encode( $filmer );
die();