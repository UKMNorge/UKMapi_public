<?php

use UKMNorge\Geografi\Fylker;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Tools\ObjectTransformer;

require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall([], [], ['GET', 'POST'], false);

$retKommuner = [];
try{
    $fylker = Fylker::getAll();
    foreach($fylker as $fylke) {
        foreach($fylke->getKommuner()->getAll() as $kommune) {
            $retKommuner[] = ObjectTransformer::kommune($kommune);
        }
    }
} catch( Exception $e ) {
    $handleCall->sendErrorToClient('Det har oppstått en serverfeil', 500);
    return;
}

$handleCall->sendToClient(
    $retKommuner
);