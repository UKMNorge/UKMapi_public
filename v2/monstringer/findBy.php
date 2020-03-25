<?php
	
use UKMNorge\Arrangement\Arrangement;

require_once('UKM/Autoloader.php');

switch( API_FINDBY_SELECTOR ) {
	case 'kommune':
		$monstring = monstringer_v2::kommune( API_FINDBY_ID, API_SEASON );
		break;		
	case 'fylke':
		$monstring = monstringer_v2::fylke( API_FINDBY_ID, API_SEASON );
		break;
	case 'land':
		$monstring = monstringer_v2::land( API_FINDBY_ID );
		break;
	case 'id':
		$monstring = new Arrangement( API_FINDBY_ID );
		break;
	default:
		throw new Exception('Unknown findBy selector '. API_FINDBY_SELECTOR );
}
echo json_encode( json_export::monstring( $monstring ) );
die();
