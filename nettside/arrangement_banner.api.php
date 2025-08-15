<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Database\SQL\Query;

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

// Henter blog ID for arrangementet
$blogId = getBlogIdByPath('/'. $arrangement->getPath() .'/');

// Hvis blog ID ikke finnes, send feilmelding
if($blogId === null) {
    $handleCall->sendErrorToClient('Fant ikke blogId for arrangementet.', 400);
    return;
}

// Henter bannerbilde for arrangementet, hvis null returneres tom array
$retBanner = getBannerImage(getBlogIdByPath('/'. $arrangement->getPath() .'/'));

$handleCall->sendToClient(
    $retBanner ?? []
);


/* METODER */

/**
 * Get the blog ID by the path.
 *
 * @param string $path The path to the blog.
 * @return int|null The blog ID or null if not found.
 */
function getBlogIdByPath($path) : int|null {
    $query = new Query(
        "SELECT blog_id 
        FROM wpms2012_blogs
        WHERE path = '#path'",
        [
            'path' => $path
        ], 
        'wordpress'
    );

    $query->setDatabase('wordpress');

    $blogId = $query->run('array') ? $query->run('array')['blog_id'] : null;
    
    if($blogId === null) {
        return null;
    }

    return intval($blogId);
}

/**
 * Get the banner image for a blog.
 *
 * @param int $blogId The blog ID.
 * @return array|null An array of options or null if not found.
 */
function getBannerImage(int $blogId) {
    // Check if the blog ID is number
    if (!is_numeric($blogId)) {
        return null;
    }

    $query = new Query(
        "SELECT * 
        FROM wpms2012_#blogId_options
        WHERE option_name = 'UKM_banner_image'
        OR option_name = 'UKM_banner_image_large'
        OR option_name = 'UKM_banner_image_position_y'",
        [
            'blogId' => $blogId
        ], 
        'wordpress'
    );
    $query->setDatabase('wordpress');

    $res = $query->run();

    if (!$res) {
        return null;
    }

    $retArr = [];
    while ($row = Query::fetch($res)) {
        $retArr[$row['option_name']] =  $row['option_value'];
    }


    return $retArr;
}