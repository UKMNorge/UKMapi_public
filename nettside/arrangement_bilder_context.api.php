<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Load;
use UKMNorge\Nettverk\Omrade;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Innslag\Innslag;
use UKMNorge\Tools\ObjectTransformer;
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

$bilder = [];

foreach($arrangement->getProgram()->getAll() as $h) {
    $hendelseObj = ObjectTransformer::hendelseNew($h);
    foreach($hendelseObj['items'] as $item) {
        if($item['object_type'] == 'innslag') {
            $innslag = Innslag::getById($item['id']);
            foreach($innslag->getBilder()->getAll() as $bilde) {
                if($bilde->getPlId() != $arrangement->getId()) {
                    continue;
                }
                try {
                    if(isset($bilder[$bilde->getId()]) && !empty($bilder[$bilde->getId()])) {
                        $bilder[$bilde->getId()]['context']['hendelser'][$hendelseObj['id']] = $hendelseObj;
                    }
                    else {
                        $bilder[$bilde->getId()]['bilde'] = ObjectTransformer::bilde($bilde);
                        $bilder[$bilde->getId()]['context']['innslag'] = ObjectTransformer::innslag($innslag);
                        $bilder[$bilde->getId()]['context']['hendelser'][$hendelseObj['id']] = $hendelseObj;
                        $bilder[$bilde->getId()]['context']['omraade'] = $innslag->getOmradeNavn();
                        $bilder[$bilde->getId()]['context']['kommune'] = ObjectTransformer::kommune($innslag->getKommune());
                        $bilder[$bilde->getId()]['context']['fylke'] = ObjectTransformer::fylke($innslag->getFylke());
                        $author = getAdminInfoFromWP($bilde->getAuthorId());
                        if($author != null) {
                            $bilder[$bilde->getId()]['context']['author'] = $author;
                        }
                    }
                } catch (Exception $e) {
                    // Do nothing
                }

            }
            // $bilder[] = $item['bilde'];
        }
    }
}

// Legg til alle bilder som ikke er del av en hendelse
foreach($arrangement->getInnslag()->getAll() as $innslag) {
    foreach($innslag->getBilder()->getAll() as $bilde) {
        if($bilde->getPlId() != $arrangement->getId()) {
            continue;
        }
        if(!isset($bilder[$bilde->getId()]) || !empty($bilder[$bilde->getId()])) {
            $innslag = Innslag::getById($bilde->getInnslagId());

            $bilder[$bilde->getId()] = [];
            $bilder[$bilde->getId()]['bilde'] = ObjectTransformer::bilde($bilde);
            $bilder[$bilde->getId()]['context']['innslag'] = ObjectTransformer::innslag($innslag);
            $bilder[$bilde->getId()]['context']['hendelser'] = [];
            $bilder[$bilde->getId()]['context']['omraade'] = $innslag->getOmradeNavn();
            $bilder[$bilde->getId()]['context']['kommune'] = ObjectTransformer::kommune($innslag->getKommune());
            $bilder[$bilde->getId()]['context']['fylke'] = ObjectTransformer::fylke($innslag->getFylke());
            $author = getAdminInfoFromWP($bilde->getAuthorId());
            if($author != null) {
                $bilder[$bilde->getId()]['context']['author'] = $author;
            }
        }
    }
}

$handleCall->sendToClient(
    $bilder
);


/**
 * Get admin information from WordPress database
 * 
 * @param int $adminId ID of the admin
 * @return array|null Returns an array with admin info or null if not found
 */
function getAdminInfoFromWP($adminId) {
    $query = new Query(
        "SELECT
            u.display_name,
            u.user_email AS user_email,
            um.meta_value AS user_phone
        FROM wpms2012_users AS u
        LEFT JOIN wpms2012_usermeta AS um
        ON um.user_id = u.ID
        AND um.meta_key = 'user_phone'
        WHERE u.ID = '#user_id'",
        [
            'user_id' => $adminId
        ], 
        'wordpress'
    );
    
    
    $query->setDatabase('wordpress');
    return $query->run('array');
}