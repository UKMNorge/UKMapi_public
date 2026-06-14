<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Innslag\Innslag;
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

$arrangement = null;
try {
    $arrangement = new Arrangement($arrangementId);
} catch (Exception $e) {
    $handleCall->sendErrorToClient('Ugyldig arrangement ID', 400);
    return;
}

$filmer = [];

foreach($arrangement->getProgram()->getAll() as $h) {
    $hendelseObj = ObjectTransformer::hendelseNew($h);
    foreach($hendelseObj['items'] as $item) {
        if($item['object_type'] == 'innslag') {
            $innslag = Innslag::getById($item['id']);
            foreach($innslag->getFilmer($arrangementId)->getAll() as $film) {
                try {
                    if(isset($filmer[$film->getId()]) && !empty($filmer[$film->getId()])) {
                        $filmer[$film->getId()]['context']['hendelser'][$hendelseObj['id']] = $hendelseObj;
                    }
                    else {
                        $filmer[$film->getId()]['film'] = ObjectTransformer::film($film);
                        $filmer[$film->getId()]['context']['innslag'] = ObjectTransformer::innslag($innslag);
                        $filmer[$film->getId()]['context']['hendelser'][$hendelseObj['id']] = $hendelseObj;
                        $filmer[$film->getId()]['context']['omraade'] = $innslag->getOmradeNavn();
                        $filmer[$film->getId()]['context']['kommune'] = ObjectTransformer::kommune($innslag->getKommune());
                        $filmer[$film->getId()]['context']['fylke'] = ObjectTransformer::fylke($innslag->getFylke());
                    }
                } catch (Exception $e) {
                    // Do nothing
                }
            }
        }
    }
}

// Legg til reportasjefilmer som ikke er knyttet til innslag
foreach($arrangement->getFilmer()->getAll() as $film) {
    if(isset($filmer[$film->getId()])) {
        continue;
    }
    if($film->getInnslagId() > 0) {
        continue;
    }
    try {
        $filmer[$film->getId()]['film'] = ObjectTransformer::film($film);
        $filmer[$film->getId()]['context']['hendelser'] = [];
    } catch (Exception $e) {
        // Do nothing
    }
}

$handleCall->sendToClient(
    $filmer
);
