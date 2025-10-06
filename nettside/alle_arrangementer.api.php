<?php

use UKMNorge\Arrangement\Load;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Tools\ObjectTransformer;

require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall(['season'], ['type'], ['GET', 'POST'], false);

$seasonArg = $handleCall->getArgument('season');
$typeArg = $handleCall->getOptionalArgument('type') ?? null;

if (!is_numeric($seasonArg)) {
    $handleCall->sendErrorToClient('Sesong må være tall', 400);
    return;
}

if($typeArg != null && !in_array($typeArg, ['kommune', 'fylke', 'land'])) {
    $handleCall->sendErrorToClient('Ugyldig type', 400);
    return;
}

$season = intval($seasonArg);

if( $season < 2020 ) {
    $handleCall->sendErrorToClient('Ugyldig sesong', 400);
    return;
}

$retArrangementer = [];
try{ 
    $arrangementer = Load::bySesong($season)->getAllSynlige();
    if( $typeArg ) {
        $arrangementer = array_filter($arrangementer, function($arrangement) use ($typeArg) {
            return $arrangement->getType() == $typeArg;
        });
    }
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
