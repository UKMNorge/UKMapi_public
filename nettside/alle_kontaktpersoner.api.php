<?php

use UKMNorge\Geografi\Fylker;
use UKMNorge\Nettverk\Omrade;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Tools\ObjectTransformer;
use UKMNorge\Nettverk\OmradeKontaktpersoner;
use UKMNorge\Database\SQL\Query;
use UKMNorge\Nettverk\Administratorer;


require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall([], [], ['GET', 'POST'], false);

$retOmradeKontakpersoner = [];

try {

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
                $retAdminObj = ObjectTransformer::adminKontaktperson($admin, getAdminBilde($kontaktpersonAdmin->getId()));
                if (!isKontaktpersonAdded($retOmradeKontakpersoner[$omradeType. '_' .$omradeId]['kontaktpersoner'], $retAdminObj)) {
                    $retOmradeKontakpersoner[$omradeType. '_' .$omradeId]['kontaktpersoner'][] = $retAdminObj;
                }
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
                    // Add admin contact person to the kommune only if name doesn't already exist
                    $retAdminObj = ObjectTransformer::adminKontaktperson($admin, getAdminBilde($kontaktpersonAdmin->getId()));
                    if (!isKontaktpersonAdded($retOmradeKontakpersoner[$kommuneType. '_' .$kommuneId]['kontaktpersoner'], $retAdminObj)) {
                        $retOmradeKontakpersoner[$kommuneType. '_' .$kommuneId]['kontaktpersoner'][] = $retAdminObj;
                    }
                }
            }
        }
    }
    
    $handleCall->sendToClient(
        $retOmradeKontakpersoner
    );
} catch(Exception $e) {
    $handleCall->sendErrorToClient('Det har oppstått en serverfeil', 500);
    return;
}

/**
 * Check if a contact person with the given name already exists in the array
 * 
 * @param array $kontaktpersoner Array of contact persons
 * @param string $retKontaktperson Returned contact person object
 * @return bool True if kontaktperson exists, false otherwise
 */
function isKontaktpersonAdded($kontaktpersoner, $retKontaktperson) {
    foreach ($kontaktpersoner as $kontaktperson) {
        if ($kontaktperson['id'] == $retKontaktperson['id']) {
            return true;
        }
    }
    return false;
}

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
            um.meta_value AS user_phone,
            u.user_email AS user_email
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

/**
 * Get the profile image URL of an admin from the database
 * 
 * @param int $adminId ID of the admin
 * @return string|null Returns the image URL or null if not found
 */
function getAdminBilde($adminId) {
    $sql = new Query(
        "SELECT `bilde_url`
        FROM `wp_user_bilde`
        WHERE `wp_user` = '#userid'",
        [
            'userid' => $adminId
        ]
    );

    $row = $sql->run('array');
    if($row) {
        return $row['bilde_url'];
    }
}