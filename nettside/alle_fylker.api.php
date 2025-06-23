<?php

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Geografi\Fylker;

require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall([], [], ['GET', 'POST'], false);

$retFylker = [];
try{ 
    $fylker = Fylker::getAll();
    foreach($fylker as $fylke) {
        $retFylker[] = $fylke;
    }
} catch( Exception $e ) {
    $handleCall->sendErrorToClient('Det har oppstått en serverfeil', 500);
    return;
}

$handleCall->sendToClient(
    $retFylker
);
