<?php
	

require_once('UKM/monstringer.class.php');

switch( API_FINDBY_SELECTOR ) {
	case 'kommune':
		$monstring = monstringer_v2::kommune( API_FINDBY_ID, API_SEASON );
		
		echo json_encode( json_export::monstring( $monstring ) );
		die();

	case 'fylke':
		$monstring = monstringer_v2::fylke( API_FINDBY_ID, API_SEASON );
		
		echo json_encode( json_export::monstring( $monstring ) );
		die();
	case 'id':
		$monstring = new monstring_v2( API_FINDBY_ID );
		echo json_encode( json_export::monstring( $monstring ) );
		die();
	default:
		throw new Exception('Unknown findBy selector '. API_FINDBY_SELECTOR );
}