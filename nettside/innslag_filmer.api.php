<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Innslag\Innslag;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Tools\ObjectTransformer;

require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall(['innslag_id'], ['arrangement_id'], ['GET', 'POST'], false);

$innslagId = $handleCall->getArgument('innslag_id');
$arrangementIdArg = $handleCall->getOptionalArgument('arrangement_id');

if (!is_numeric($innslagId)) {
    $handleCall->sendErrorToClient('Innslag ID må være int', 400);
    return;
}
$innslagId = intval($innslagId);


$arrangement = null;
if($arrangementIdArg) {
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
}

$retFilmer = [];
try{ 
    $innslag = Innslag::getById($innslagId);
    
    $filmer = $innslag->getFilmer($arrangement ? $arrangement->getId() : null)->getAll();
    if(count($filmer) > 0) {
        foreach($filmer as $film) {
            $retFilmer[] = ObjectTransformer::film($film);
        }
    }

} catch( Exception $e ) {
    $handleCall->sendErrorToClient('Det har oppstått en serverfeil', 500);
    return;
}

$handleCall->sendToClient(
    $retFilmer
);
