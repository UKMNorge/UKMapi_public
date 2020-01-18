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

// FILMEN ER KLAR, REGISTRER I UKM-TV
$tv_id = Converted::sendToUKMTV( $film );

// FEEDBACK TO VIDEOCONVERTER
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
} else {
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
}