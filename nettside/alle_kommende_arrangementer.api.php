<?php

use UKMNorge\Arrangement\Load;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Tools\ObjectTransformer;
use UKMNorge\Arrangement\Kommende;


require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall([], [], ['GET', 'POST'], false);

// Get current year from date
$currentSeason = (int)date('Y');
$aarFremITid = 2; // Antall år frem i tid vi skal hente arrangementer for


$retArrangementer = [];
try{
    // Sjekker: i fjor, nåverende år - 2 år frem i tid
    for($season = $currentSeason-1; $season <= $currentSeason+$aarFremITid; $season++) {
        $arrangementer = Kommende::bySesong($season)->getAllSynlige();
        if( $typeArg ) {
            $arrangementer = array_filter($arrangementer, function($arrangement) use ($typeArg) {
                return $arrangement->getType() == $typeArg;
            });
        }
        foreach($arrangementer as $arrangement) {
            $retArrangementer[] = ObjectTransformer::arrangement($arrangement);
        }
    }
} catch( Exception $e ) {
    $handleCall->sendErrorToClient('Det har oppstått en serverfeil', 500);
    return;
}

$handleCall->sendToClient(
    $retArrangementer
);