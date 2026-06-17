<?php

use UKMNorge\Geografi\Fylker;
use UKMNorge\Nettverk\Omrade;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Tools\ObjectTransformer;
use UKMNorge\Nettverk\OmradeKontaktpersoner;
use UKMNorge\Database\SQL\Query;
use UKMNorge\Nettverk\Administratorer;
use UKMNorge\Arrangement\Arrangement;


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


$kontaktpersoner = [];

$arrangement = new Arrangement($arrangementId);

try {
    foreach($arrangement->getKontaktpersoner()->getAll() as $kontaktperson) {
        $kontaktpersoner[] = ObjectTransformer::kontaktperson($kontaktperson);
    }
} catch (Exception $e) {
    $handleCall->sendErrorToClient('Det har oppstått en serverfeil', 500);
    return;
}

usort($kontaktpersoner, function($a, $b) {
    $cmp = strcasecmp($a['beskrivelse'] ?? '', $b['beskrivelse'] ?? '');
    if ($cmp !== 0) {
        return $cmp;
    }
    return strcasecmp($a['navn'] ?? '', $b['navn'] ?? '');
});

$handleCall->sendToClient(
    $kontaktpersoner
);
