<?php

$files = new tv_files('place', API_MONSTRING);

$filmer = [];
while( $tv = $files->fetch() ) {
    $filmer[] = json_export::tv( $tv );
}

echo json_encode( $filmer );
die();