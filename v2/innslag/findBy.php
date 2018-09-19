<?php

require_once('UKM/monstringer.class.php');

$monstring = new monstring_v2( API_MONSTRING );

switch( API_FINDBY_SELECTOR ) {
	case 'id':
		$innslag = $monstring->getInnslag()->get( API_FINDBY_ID );
		$export = json_export::innslag( $innslag );
        break;
	default:
		throw new Exception('Unknown findBy selector '. API_FINDBY_SELECTOR );
}

echo json_encode( $export );
die();
