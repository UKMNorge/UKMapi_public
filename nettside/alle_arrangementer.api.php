<?php

use UKMNorge\Arrangement\Load;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Tools\ObjectTransformer;

require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall(['season'], [], ['GET', 'POST'], false);

$seasonArg = $handleCall->getArgument('season');

if (!is_numeric($seasonArg)) {
    $handleCall->sendErrorToClient('Sesong må være int', 400);
    return;
}
$season = intval($seasonArg);

if( $season < 2020 ) {
    $handleCall->sendErrorToClient('Ugyldig sesong', 400);
    return;
}

$retArrangementer = [];
try{ 
    $arrangementer = Load::bySesong($season)->getAll();
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
