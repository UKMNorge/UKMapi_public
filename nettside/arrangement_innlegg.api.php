<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Tools\ObjectTransformer;
use UKMNorge\Wordpress\Blog;


require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall(['arrangement_id'], ['category'], ['GET', 'POST'], false);

$arrangementIdArg = $handleCall->getArgument('arrangement_id');
$category = $handleCall->getOptionalArgument('category');

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

$retInnlegg = [];
try{ 
    $blogId = Blog::getIdByPathOutsideWP($arrangement->getPath());
    
    $innlegg = Blog::getAllPostsOutsideWP($blogId, 'innlegg');

    foreach($innlegg as $innlegg) {
        if($category != null) {
            $categories = $innlegg->getCategorySlugs();
            if(!in_array($category, $categories)) {
                continue;
            }
        }
        
        $retInnlegg[] = ObjectTransformer::innlegg($innlegg);
    }

} catch( Exception $e ) {
    $handleCall->sendErrorToClient('Det har oppstått en serverfeil', 500);
    return;
}

$handleCall->sendToClient(
    $retInnlegg
);
