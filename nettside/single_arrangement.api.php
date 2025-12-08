<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Load;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Tools\ObjectTransformer;
use UKMNorge\Arrangement\Kommende;
use UKMNorge\Database\SQL\Query;



require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall(['path'], [], ['GET', 'POST'], false);

$path = $handleCall->getArgument('path');

$retArrangement = null;
try{
    $arrangement = Arrangement::getByPath($path);
} catch( Exception $e ) {
    $handleCall->sendErrorToClient('Det har oppstått en serverfeil', 500);
    return;
}

if( $arrangement == null ) {
    $handleCall->sendErrorToClient('Fant ikke arrangementet', 404);
    return;
}

$handleCall->sendToClient(
    ObjectTransformer::arrangement($arrangement)
);
