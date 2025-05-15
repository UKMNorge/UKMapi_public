<?php

use UKMNorge\Filmer\UKMTV\CloudflareFilm;
use UKMNorge\Filmer\UKMTV\Film;
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
    $cloudflareFilm = new CloudflareFilm([], $cloudflare_id);
} catch (Exception $e) {
    $handleCall->sendErrorToClient('Fant ikke film med id ' . $cloudflare_id, 404);
}

if (empty($cloudflareFilm)) {
    $handleCall->sendErrorToClient('Fant ikke film med id ' . $cloudflare_id, 404);
}

$convertedFilm = Film::convertFromCloudflare($cloudflareFilm);

$handleCall->sendToClient([
    'status' => $convertedFilm->getId() == $cloudflareFilm->getId(),
]);