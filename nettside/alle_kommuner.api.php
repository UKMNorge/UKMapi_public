<?php

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Geografi\Kommuner;

require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall([], [], ['GET', 'POST'], false);

$retKommuner = [];
try{
    $retKommuner = Kommuner::getKeyValArray();
} catch( Exception $e ) {
    $handleCall->sendErrorToClient('Det har oppstått en serverfeil', 500);
    return;
}

$handleCall->sendToClient(
    $retKommuner
);
