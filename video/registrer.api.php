<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Filmer\UKMTV\Write;
use UKMNorge\Filmer\Upload\Converted;

header('Content-Type: application/json; charset=utf-8');

error_log('REGISTRER:VIDEO');
error_log('CRON_ID: ' . var_export($_POST['id'], true));

foreach ($_POST as $key => $val) {
    error_log('POST:' . $key . ' => ' . var_export($val, true));
}

if (!is_numeric($_POST['id']) || $_POST['id'] == 0) {
    error_log('FAILURE REGISTRER:VIDEO CRON_ID: ' . var_export($_POST['id'], true));
    error_log('ERROR: Ugyldig cron ID');
    header('HTTP/1.1 450 BAD REQUEST');
    die(
        json_encode(
            [
                'success' => false,
                'type' => 'unknown'
            ]
        )
    );
}

require_once('UKM/Autoloader.php');
require_once('UKM/vendor/autoload.php'); // fordi InnslagType bruker yaml 😱

$arrangement = new Arrangement($_POST['pl_id']);

// OPPDATER UPLOADED_VIDEO
try {
    if ((int) $_POST['b_id'] > 0) {
        $type = 'innslag';
        $innslag = $arrangement->getInnslag()->get(intval($_POST['b_id']), true);
        $film = Converted::registerInnslag(
            intval($_POST['id']),                   // $cron_id
            $arrangement,
            $_POST['file_path'],
            $_POST['file_name_store'],
            $innslag
        );
    } else {
        $type = 'reportasje';
        $film = Converted::registerReportasje(
            intval($_POST['id']),   // $cron_id
            $arrangement,
            $_POST['file_path'],
            $_POST['file_name_store']
        );
    }
} catch( Exception $e ) {
    error_log('CAUGHT REGISTER EXCEPTION (code'. $e->getCode() .':');
    error_log($e->getMessage());
    header('HTTP/1.1 500 INTERNAL SERVER ERROR');
    die(
        json_encode(
            [
                'success' => false,
                'type' => 'unknown'
            ]
        )
    );
}

if( $film->getTvId() > 0 ) {
    $tv_id = $film->getTvId();
    error_log('ER ALLEREDE REGISTRER I UKM-TV');
} else {
    error_log('REGISTRER I UKM-TV');
    // FILMEN ER KLAR, REGISTRER I UKM-TV
    try {
        $tv_id = Converted::sendToUKMTV( $film );
    } catch( Exception $e ) {
        error_log('TV CAUGHT EXCEPTION (code'. $e->getCode() .':');
        error_log($e->getMessage());
        $tv_id = false;
    }
}

// FEEDBACK TO VIDEOCONVERTER
// SUCCESS
if( !$tv_id ) {
    error_log('FAILURE REGISTRER:VIDEO CRON_ID: ' . var_export($_POST['id'], true));
    error_log('TYPE: '. strtoupper($type));
    header('HTTP/1.1 '. ($type == 'innslag' ? '451' : '452' ).' BAD REQUEST');
    die(
        json_encode(
            [
                'success' => false,
                'type' => $type
            ]
        )
    );
}

// ERROR
error_log('SUCCESS REGISTRER:VIDEO CRON_ID: ' . var_export($_POST['id'], true));
error_log('TYPE: '. strtoupper($type));
die(
    json_encode(
        [
            'success' => true,
            'type' => $type
        ]
    )
);