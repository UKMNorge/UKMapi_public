<?php

use UKMNorge\Arrangement\Oppgave\Oppgave;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Videresending\VideresendingNominasjon;
use UKMNorge\Arrangement\UKMFestival;
use UKMNorge\Arrangement\Skjema\Skjema;
use UKMNorge\Arrangement\Skjema\SvarSett;

require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall(
    ['oppgave_id', 'sporsmal_id', 'skjema_id'],
    [],
    ['GET', 'POST'],
    false,
    false,
    true
);

try {
    $arrangement = UKMFestival::getCurrentUKMFestival();
    if (!$arrangement) {
        $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
    }
} catch (Exception $e) {
    if ($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

$oppgaveId = (int) $handleCall->getArgument('oppgave_id');
$skjemaId = (int) $handleCall->getArgument('skjema_id');
$sporsmalId = (int) $handleCall->getArgument('sporsmal_id');

if ($oppgaveId < 1) {
    $handleCall->sendErrorToClient('Ugyldig oppgave_id', 400);
}
if ($skjemaId < 1) {
    $handleCall->sendErrorToClient('Ugyldig skjema_id', 400);
}
if ($sporsmalId < 1) {
    $handleCall->sendErrorToClient('Ugyldig sporsmal_id', 400);
}

try {
    $oppgave = new Oppgave($oppgaveId);
} catch (Exception $e) {
    $handleCall->sendErrorToClient('Fant ikke oppgaven', 404);
}

$valgtSkjema = null;
foreach ($oppgave->getSkjemaKjede() as $skjemaItem) {
    $kjedelinkId = (int) $skjemaItem->getId();
    $faktiskSkjemaId = (int) $skjemaItem->getSkjema()->getId();

    if ($kjedelinkId === $skjemaId || $faktiskSkjemaId === $skjemaId) {
        $valgtSkjema = $skjemaItem;
        break;
    }
}

if ($valgtSkjema === null) {
    $handleCall->sendErrorToClient('Fant ikke skjemaet', 404);
}

$retArr = [];

foreach (VideresendingNominasjon::getAlleTilArrangement($arrangement->getId())->getAll() as $vNominasjon) {
    $person = $vNominasjon->getPerson();
    if (!$person) {
        continue;
    }

    $arrangementFra = $vNominasjon->getArrangementFra();
    $innslag = $arrangementFra->getInnslag()->get($vNominasjon->getBId());
    if (!$innslag) {
        continue;
    }

    $svarsett = getSvarSett($valgtSkjema->getSkjema(), $person->getId());

    $svarText = null;
    try {
        $svarObj = $svarsett->get($sporsmalId);
        $svarText = $svarObj ? $svarObj->getValue() : null;
    } catch (Exception $e) {
        $svarText = null;
    }

    $retArr[$vNominasjon->getPId()] = [
        'person_id' => $person->getId(),
        'fornavn' => $person->getFornavn(),
        'etternavn' => $person->getEtternavn(),
        'mobil' => $person->getMobil(),
        'fylke' => $arrangementFra->getFylke()->getNavn(),
        'innslag' => $innslag->getNavn(),
        'innslag_type' => $innslag->getType()->getNavn(),
        'svar' => $svarText,
    ];
}

function getSvarSett(Skjema $skjema, int $personId): SvarSett
{
    try {
        $respondent = $skjema->getRespondenter()->get($personId);
        return $respondent->getSvar();
    } catch (Exception $e) {
        if ($e->getCode() == 163003) {
            return SvarSett::getPlaceholder('person', $personId, $skjema->getId());
        }
        throw $e;
    }
}

$handleCall->sendToClient($retArr);