<?php

use UKMNorge\Arrangement\Aktivitet\Aktivitet;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Tools\ObjectTransformer;

require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall(['pl_id'], [], ['GET', 'POST'], false);

$arrangementIdArg = $handleCall->getArgument('pl_id');

if (!is_numeric($arrangementIdArg)) {
    $handleCall->sendErrorToClient('Sesong må være tall', 400);
    return;
}

$plId = intval($arrangementIdArg);

$retAktiviteter = [];
try{ 
    $tilPublikum = true; // Dette skal altid være true, da dette er en API for nettsiden
    foreach(Aktivitet::getAllByArrangement($plId) as $aktivitet) {
        $retAktiviteter[] = $aktivitet->getArrObj($tilPublikum);
    }
} catch( Exception $e ) {
    $handleCall->sendErrorToClient('Det har oppstått en serverfeil', 500);
    return;
}

$handleCall->sendToClient(
    $retAktiviteter
);
