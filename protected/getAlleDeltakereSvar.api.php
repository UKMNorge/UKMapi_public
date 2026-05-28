<?php

use UKMNorge\Arrangement\Oppgave\Oppgave;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Videresending\VideresendingNominasjon;
use UKMNorge\Arrangement\UKMFestival;
use UKMNorge\Arrangement\Skjema\Skjema;
use UKMNorge\Arrangement\Skjema\SvarSett;
use UKMNorge\Database\SQL\Query;


require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall(['oppgave_id', 'sporsmal_id'], ['skjema_id'], ['GET', 'POST'], false, false, true);

$arrangement = null;
try{
    $arrangement = UKMFestival::getCurrentUKMFestival();
    if(!$arrangement) {
        $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
    }
} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

$oppgaveId = (int) $handleCall->getArgument('oppgave_id');
$skjemaId = (int) $handleCall->getOptionalArgument('skjema_id');
$sporsmalId = (int) $handleCall->getArgument('sporsmal_id');

var_dump($oppgaveId, $skjemaId, $sporsmalId);

if($sporsmalId == null || $sporsmalId < 1) {
    $handleCall->sendErrorToClient('Ugyldig sporsmal_id', 400);
}
$oppgave = null;
if($oppgaveId < 1) {
    $handleCall->sendErrorToClient('Ugyldig oppgave_id', 400);
}
try {
    $oppgave = new Oppgave($oppgaveId);
} catch(Exception $e) {
    $handleCall->sendErrorToClient('Fant ikke oppgaven', 404);
}

$skjema = null;
foreach($oppgave->getSkjemaKjede() as $s) {
    if($s->getSkjema()->getId() == $skjemaId) {
        $skjema = $s->getSkjema();
        break;
    }
}

if($skjema === null) {
    $handleCall->sendErrorToClient('Fant ikke skjemaet', 404);
}

$alleRespondenter = $skjema->getRespondenter()->getAll();
$retArr = [];


foreach (VideresendingNominasjon::getAlleTilArrangement($arrangement->getId())->getAll() as $vNominasjon) {
    $personNominasjon = $vNominasjon->getPerson();
    if(!$personNominasjon) {
        continue;
    }
    
    $personIds = getPersonIdsByMobil($personNominasjon->getMobil());
    foreach($personIds as $personId) {
        try {
            $svarsett = getSvarSett($skjema, $personId, $alleRespondenter);
            $svarText = $svarsett->get($sporsmalId)->getValue();
        } catch (Exception $e) {
            $svarText = 'fant ikke svarsett';
        }
        
        $arrangementFra = $vNominasjon->getArrangementFra();
        try {
            $innslag = $arrangementFra->getInnslag()->get($vNominasjon->getBId());
        } catch(Exception $e) {
            $innslag = null;
        }
    
        if($svarText == null && isset($retArr[$vNominasjon->getPId()])) {
            continue;
        }

        $retArr[$vNominasjon->getPId()] = [
            'person_id' => $personNominasjon->getId(),
            'fornavn' => $personNominasjon->getFornavn(),
            'etternavn' => $personNominasjon->getEtternavn(),
            'mobil' => $personNominasjon->getMobil(),
            'fylke' => $arrangementFra->getFylke()->getNavn(),
            'innslag' => $innslag ? $innslag->getNavn() : null,
            'innslag_type' => $innslag ? $innslag->getType()->getNavn() : null,
            'svar' => $svarText,
        ];
    }
}

function getPersonIdsByMobil(string $mobil): array {
   
    $sql = new Query(
        "SELECT p_id FROM `smartukm_participant`
                    WHERE `p_phone` = '#mobil'",
        ['mobil' => $mobil],
    );
    $res = $sql->run();
    $personIds = [];
    while ($row = Query::fetch($res)) {
        $personIds[] = $row['p_id'];
    }
    return $personIds;
}

function getSvarSett(Skjema $skjema, int $personId, array &$alleRespondenter) {
    try {
        $respondent = null;
        foreach($alleRespondenter as $r) {
            if($r->getId() == $personId) {
                $respondent = $r;
                break;
            }
        }
        if(!$respondent) {
            return SvarSett::getPlaceholder('person', $personId, $skjema->getId());
        }
        return $respondent->getSvar();
    } catch (Exception $e) {
        if ($e->getCode() == 163003) {
            return SvarSett::getPlaceholder('person', $personId, $skjema->getId());
        }
        throw $e;
    }
}

$handleCall->sendToClient($retArr);