<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Tools\ObjectTransformer;

require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall(['arrangement_id'], [], ['GET', 'POST'], false);

$arrangementIdArg = $handleCall->getArgument('arrangement_id');

if (!is_numeric($arrangementIdArg)) {
    $handleCall->sendErrorToClient('Arrangement ID må være int', 400);
    return;
}
$arrangementId = intval($arrangementIdArg);


$arrangement = null;

if (!is_numeric($arrangementIdArg)) {
    $handleCall->sendErrorToClient('Arrangement ID må være int', 400);
    return;
}

$arrangementId = intval($arrangementIdArg);

try {
    $arrangement = new Arrangement($arrangementId);
} catch (Exception $e) {
    $handleCall->sendErrorToClient('Ugyldig arrangement ID', 400);
    return;
}

$retBilder = [];

foreach($arrangement->getInnslag()->getAll() as $innslag) {   
    $bilder = $innslag->getBilder()->getAll();

    if(count($bilder) > 0) {
        foreach($bilder as $bilde) {
            $retBilder[] = ObjectTransformer::bilde($bilde);
        }
    }

}

$handleCall->sendToClient(
    $retBilder
);
