<?php

use UKMNorge\Filmer\UKMTV\CloudflareFilm;
use UKMNorge\Filmer\UKMTV\Film;
use UKMNorge\OAuth2\HandleAPICall;
// use UKMNorge\Geografi\Kommune;
// use UKMNorge\Arrangement\Arrangement;
// use UKMNorge\Geografi\Fylker;
// use UKMNorge\Nettverk\Omrade;


require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall(['cloudflare_id'], [], ['GET', 'POST'], false, false, true);

$cloudflare_id = $handleCall->getArgument('cloudflare_id') ?? '';

if (empty($cloudflare_id)) {
    $handleCall->sendErrorToClient('Ingen cloudflare_id oppgitt', 400);
}

$cloudflareFilm = null;

try{ 
    $cloudflareFilm = CloudflareFilm::getById($cloudflare_id);
} catch (Exception $e) {
    $handleCall->sendErrorToClient('Fant ikke film med id ' . $cloudflare_id, 404);
}


$convertedFilm = Film::convertFromCloudflare($cloudflareFilm);

$handleCall->sendToClient([
    'status' => $convertedFilm->getId() == $cloudflareFilm->getId(),
]);

// $kommuneNavn = $handleCall->getArgument('kommune');
// $kommuneId = $handleCall->getArgument('kommunenummer');


// $tilgjengelige_arrangementer = [];
// try {
//     $kommune = new Kommune($kommuneId);
    

//     // If Oslo, returner alle arrangementer i alle bydeler
//     if( $kommuneId == '0301') {
//         $osloFylke = Fylker::getById(3);
        
//         foreach( $osloFylke->getKommuner()->getAll() as $kommune ) {
//             $alle_arrangementer = Omrade::getByKommune($kommune->getId())->getKommendeArrangementer()->getAll();
//             foreach( $alle_arrangementer as $arrangement ) {
//                 $tilgjengelige_arrangementer[] = _generateArrangement($arrangement);
//             }
//         }
//     }
//     else {
//         $alle_arrangementer = Omrade::getByKommune($kommune->getId())->getKommendeArrangementer()->getAll();
//     }
    
//     foreach ($alle_arrangementer as $arrangement) {
//         $tilgjengelige_arrangementer[] = _generateArrangement($arrangement);
//     }

//     $handleCall->sendToClient([
//         'status' => true,
//         'arrangementer' => $tilgjengelige_arrangementer,
//     ]);

// } catch( Exception $e ) {
//     $handleCall->sendErrorToClient('Det har oppstått en serverfeil', 500);
// }


// // Private funksjoner
// function _generateArrangement(Arrangement $arrangement) {
//     return [
//         'id' => $arrangement->getId(),
//         'navn' => $arrangement->getNavn(),
//         'url' => $arrangement->getLink(),
//         'dato' => $arrangement->getStart()
//     ];
// }