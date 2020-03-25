<?php

use UKMNorge\Arrangement\Arrangement;

require_once('UKM/Autoloader.php');

if( !API_MONSTRING ) {
	die('false');
}

$monstring = new Arrangement( API_MONSTRING );

$kontakter = [];
foreach( $monstring->getKontaktpersoner() as $kontakt ) {
    $kontakter[] = json_export::kontakt( $kontakt );
}

echo json_encode( $kontakter );
die();
