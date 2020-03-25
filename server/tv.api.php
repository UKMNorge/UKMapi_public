<?php

use UKMNorge\Filmer\UKMTV\Server\Server;

require_once('UKM/Autoloader.php');

echo !!Server::getActiveCacheUrl() ? 'true' : 'false';

die();