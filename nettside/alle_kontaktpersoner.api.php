<?php
// Show all errors, warnings, and notices
// error_reporting(E_ALL); 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

// Optional: Force errors to be shown even in production environments
// ini_set('log_errors', 0);

use UKMNorge\Geografi\Fylker;
use UKMNorge\Nettverk\Omrade;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Tools\ObjectTransformer;
use UKMNorge\Nettverk\OmradeKontaktpersoner;
use UKMNorge\Database\SQL\Query;
use UKMNorge\Nettverk\Administratorer;


// require_once '/full/path/to/wp-blog-header.php';

// dev-parellels/dev-html


// require_once( dirname(dirname(dirname(dirname( __FILE__ )))) . '/wp-blog-header.php' );

// die;
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
    $omradeFylke = new Omrade($omradeType, $omradeId);    

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


    // Get alle administratorer som er kontaktpersoner for fylket
    $adminer = new Administratorer($omradeType, $omradeId);
    foreach($adminer->getAll() as $kontaktpersonAdmin) {
        if($kontaktpersonAdmin->erKontaktperson($omradeFylke) != true) {
            continue; // Hopper over hvis admin ikke er kontaktperson for fylket
        }

        $admin = getAdminInfoFromWP($kontaktpersonAdmin->getId());
        if($admin != null) {
            $retOmradeKontakpersoner[$omradeType. '_' .$omradeId]['kontaktpersoner'][] = ObjectTransformer::adminKontaktperson($admin);
        }
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
        $omradeKommune = new Omrade($kommuneType, $kommuneId);    

        
        foreach($oKKommune->getAll() as $kontaktperson) {
            $retOmradeKontakpersoner[$kommuneType. '_' .$kommuneId]['kontaktpersoner'][] = ObjectTransformer::kontaktperson($kontaktperson);
        }

        // Get alle administratorer som er kontaktpersoner for kommunen
        $adminer = new Administratorer($kommuneType, $kommuneId);
        foreach($adminer->getAll() as $kontaktpersonAdmin) {
            if($kontaktpersonAdmin->erKontaktperson($omradeKommune) != true) {
                continue; // Hopper over hvis admin ikke er kontaktperson for kommunen
            }
            $admin = getAdminInfoFromWP($kontaktpersonAdmin->getId());
            if($admin != null) {
                // Add admin contact person to the kommune
                $retOmradeKontakpersoner[$kommuneType. '_' .$kommuneId]['kontaktpersoner'][] = ObjectTransformer::adminKontaktperson($admin);
            }
        }
    }
}

$handleCall->sendToClient(
    $retOmradeKontakpersoner
);


function getAdminInfoFromWP($adminId) {
    $query = new Query(
        "SELECT
            u.display_name,
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