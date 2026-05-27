<?php

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Arrangement\UKMFestival;
use UKMNorge\Database\SQL\Delete;


require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall(['person_id'], [], ['GET', 'POST'], false, false, true);

$personId = $handleCall->getArgument('person_id');
$sporsmalId = 64; // Filopplasting
$skjemaId = 33; // Skjema

// Slett fra bilder
$delete = new Delete(
    'ukm_videresending_skjema_svar',
    [
        'p_fra' => $personId,
        'sporsmal' => $sporsmalId,
        'skjema' => $skjemaId
    ]
);

$res = 0;
try {
    $res = $delete->run();
} catch(Exception $e) {
    $handleCall->sendErrorToClient($e->getMessage(), $e->getCode() ?: 500);
}

$retArr = [
    'success' => $res > 0,
    'message' => $res > 0 ? 'Deltaker svar slettet' : 'Kunne ikke slette deltaker svar',
];

$handleCall->sendToClient($retArr);