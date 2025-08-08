<?php

use UKMNorge\Geografi\Fylker;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Tools\ObjectTransformer;
use UKMNorge\Nettverk\OmradeKontaktpersoner;


require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall([], [], ['GET', 'POST'], false);


$retOmradeKontakpersoner = [];

// For hvert fylke, legg til kontaktpersoner
foreach(Fylker::getAll() as $fylke) {
    $omradeId = $fylke->getId();
    $omradeType = 'fylke';
    
    $oKFylke = new OmradeKontaktpersoner($omradeId, $omradeType);
    
    if(!isset($retOmradeKontakpersoner[$omradeType. '_' .$omradeId])) {
        $retOmradeKontakpersoner[$omradeType. '_' .$omradeId] = [
            'omrade_id' => $omradeId,
            'omrade_type' => $omradeType,
            'omrade_navn' => $fylke->getNavn(),
            'kontaktpersoner' => []
        ];
    }

    foreach($oKFylke->getAll() as $kontaktperson) {
        $retOmradeKontakpersoner[$omradeType. '_' .$omradeId]['kontaktpersoner'][] = ObjectTransformer::kontaktperson($kontaktperson);
    }

    // For hver kommune i fylket, legg til kontaktpersoner
    foreach($fylke->getKommuner()->getAll() as $kommune) {
        $kommuneId = $kommune->getId();
        $kommuneType = 'kommune';
        
        if(!isset($retOmradeKontakpersoner[$kommuneType. '_' .$kommuneId])) {
            $retOmradeKontakpersoner[$kommuneType. '_' .$kommuneId] = [
                'omrade_id' => $kommuneId,
                'omrade_type' => $kommuneType,
                'omrade_navn' => $kommune->getNavn(),
                'fylke_id' => $fylke->getId(),
                'fylke_navn' => $fylke->getNavn(),
                'kontaktpersoner' => []
            ];
        }
        
        $oKKommune = new OmradeKontaktpersoner($kommuneId, $kommuneType);
        
        foreach($oKKommune->getAll() as $kontaktperson) {
            $retOmradeKontakpersoner[$kommuneType. '_' .$kommuneId]['kontaktpersoner'][] = ObjectTransformer::kontaktperson($kontaktperson);
        }
    }
}

$handleCall->sendToClient(
    $retOmradeKontakpersoner
);
