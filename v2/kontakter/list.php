<?php

require_once('UKM/monstring.class.php');

if( !API_MONSTRING ) {
	die('false');
}

$monstring = new monstring_v2( API_MONSTRING );

$kontakter = [];
foreach( $monstring->getKontaktpersoner() as $kontakt ) {
    $kontakter[] = json_export::kontakt( $kontakt );
}

echo json_encode( $kontakter );
die();
