<?php

use UKMNorge\Arrangement\Aktivitet\Aktivitet;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Tools\ObjectTransformer;

require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall(['aktivitet_id'], [], ['GET', 'POST'], false);

$aktivitetIdArg = $handleCall->getArgument('aktivitet_id');

if (!is_numeric($aktivitetIdArg)) {
    $handleCall->sendErrorToClient('Aktivitet ID må være tall', 400);
    return;
}

$aktivitetId = intval($aktivitetIdArg);

try{ 
    $aktivitet = new Aktivitet($aktivitetId);
    if($aktivitet == null) {
        $handleCall->sendErrorToClient('Aktivitet finnes ikke', 404);
        return;
    }

    $handleCall->sendToClient(
        ObjectTransformer::aktivitet($aktivitet)
    );

} catch( Exception $e ) {
    $handleCall->sendErrorToClient('Det har oppstått en serverfeil', 500);
    return;
}
