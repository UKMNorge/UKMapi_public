<?php

use UKMNorge\Filmer\UKMTV\Write;
use UKMNorge\Innslag\Media\Filmer;

require_once('UKM/Autoloader.php');

echo '<pre>';

$film = Filmer::getById(9789);

echo "--  SAVE TAGS \r\n";
Write::saveTags( $film );
echo "--  FILM r\n";
var_dump($film);