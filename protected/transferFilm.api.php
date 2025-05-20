<?php

use UKMNorge\Filmer\UKMTV\Film;
use UKMNorge\Filmer\UKMTV\Filmer;
use UKMNorge\Filmer\UKMTV\Write;
use UKMNorge\OAuth2\HandleAPICall;

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
    $cloudflareFilm = Filmer::getByCFId($cloudflare_id);
} catch (Exception $e) {
    $handleCall->sendErrorToClient('Fant ikke film med id ' . $cloudflare_id, 404);
}

if (empty($cloudflareFilm)) {
    $handleCall->sendErrorToClient('Fant ikke film med id ' . $cloudflare_id, 404);
}

// Konverter filmen fra CloudflareFilm til Film
$convertedFilm = Film::convertFromCloudflare($cloudflareFilm);

// Marker Cloudflare filmen som slettet
Write::slett($cloudflareFilm);

// Returnere status
$handleCall->sendToClient([
    'status' => $convertedFilm->getId() == $cloudflareFilm->getId(),
]);