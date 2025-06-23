<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Load;
use UKMNorge\Nettverk\Omrade;
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

// Hent område for arrangementet
$arrangement = null;
try {
    $arrangement = new Arrangement($arrangementId);
} catch (Exception $e) {
    $handleCall->sendErrorToClient('Ugyldig kommune ID', 400);
    return;
}

$hendelser = [];
try{ 
    if($arrangement == null) {
        $handleCall->sendErrorToClient('Arrangement finnes ikke', 400);
        return;
    }

    foreach($arrangement->getProgram()->getAll() as $hendelse) {
        $hendelser[] = ObjectTransformer::hendelse($hendelse);
    }

    
} catch( Exception $e ) {
    $handleCall->sendErrorToClient('Det har oppstått en serverfeil', 500);
    return;
}

$handleCall->sendToClient(
    $hendelser
);
