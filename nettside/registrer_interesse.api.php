<?php

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Interesse\Interesse;
use UKMNorge\Interesse\Write;

require_once('UKM/Autoloader.php');

$requiredArguments = [
    'navn',
    'beskrivelse',
];
$optionalArguments = [
    'arrangor_interesse',
    'kommuner',
    'epost',
    'mobil',
];

$handleCall = new HandleAPICall($requiredArguments, $optionalArguments, ['POST'], false);

// Returns early if rate limit is exceeded for this endpoint and same IP
$handleCall->limitRequestsFromIP('nettside:registrer_interesse');

$navn = $handleCall->getArgument('navn');
$beskrivelse = $handleCall->getArgument('beskrivelse');
$epost = $handleCall->getOptionalArgument('epost') ?? null;
$mobil = $handleCall->getOptionalArgument('mobil') ?? null;
$arrangorInteresse = $handleCall->getOptionalArgument('arrangor_interesse') ?? false;
$kommuner = $handleCall->getOptionalArgument('kommuner') ?? [];

if(!$post && !$mobil) {
    $handleCall->sendErrorToClient('Du må oppgi enten epost eller mobilnummer for å registrere din interesse.', 400);
    return;
}

$interesse = new Interesse(
    -1,
    $navn,
    $beskrivelse,
    $epost,
    $mobil,
    $arrangorInteresse,
    $kommuner
);

$interesse_id = Write::saveOrCreateInteresse($interesse);

if(!$interesse_id) {
    $handleCall->sendErrorToClient('Det oppstod en feil under lagring av din interesse. Vennligst prøv igjen senere.', 500);
    return;
}

$handleCall->sendToClient([
    'status' => 'lagret',
]);