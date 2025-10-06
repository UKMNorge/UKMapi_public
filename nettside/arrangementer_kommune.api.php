<?php

use UKMNorge\Arrangement\Load;
use UKMNorge\Nettverk\Omrade;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Tools\ObjectTransformer;

require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall(['kommune_id'], [], ['GET', 'POST'], false);

$kommuneIdArg = $handleCall->getArgument('kommune_id');

if (!is_numeric($kommuneIdArg)) {
    $handleCall->sendErrorToClient('Kommune ID må være int', 400);
    return;
}
$kommuneId = intval($kommuneIdArg);

// Hent område for kommunen
$omrade = null;
try {
    $omrade = Omrade::getByKommune($kommuneId);
} catch (Exception $e) {
    $handleCall->sendErrorToClient('Ugyldig kommune ID', 400);
    return;
}

$retArrangementer = [];
try{ 
    if($omrade == null) {
        $handleCall->sendErrorToClient('Område finnes ikke', 400);
        return;
    }

    $arrangementer = Load::byOmrade($omrade)->getAllSynlige();
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
