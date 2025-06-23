<?php

use UKMNorge\Arrangement\Load;
use UKMNorge\Nettverk\Omrade;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Tools\ObjectTransformer;

require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall(['fylke_id'], [], ['GET', 'POST'], false);

$fylkeIdArg = $handleCall->getArgument('fylke_id');

if (!is_numeric($fylkeIdArg)) {
    $handleCall->sendErrorToClient('Fylke ID må være int', 400);
    return;
}
$fylkeId = intval($fylkeIdArg);

// Hent område for fylket
$omrade = null;
try {
    $omrade = Omrade::getByFylke($fylkeId);
} catch (Exception $e) {
    $handleCall->sendErrorToClient('Ugyldig fylke ID', 400);
    return;
}

$retArrangementer = [];
try{ 
    if($omrade == null) {
        $handleCall->sendErrorToClient('Område finnes ikke', 400);
        return;
    }

    $arrangementer = Load::byOmrade($omrade)->getAll();
    foreach($arrangementer as $arrangement) {
        $retArrangementer[] = ObjectTransformer::arrangement($arrangement);
    }
} catch( Exception $e ) {
    $handleCall->sendErrorToClient('Det har oppstått en serverfeil', 500);
    return;
}

$handleCall->sendToClient(
    $retArrangementer
);
