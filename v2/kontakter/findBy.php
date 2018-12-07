<?php
	
require_once('UKM/monstringer.class.php');

switch( API_FINDBY_SELECTOR ) {
	case 'id':
		$monstring = new monstring_v2( API_MONSTRING );
		$kontakt = $monstring->getKontakter()->get( API_FINDBY_ID );
		
		$kontaktdata = json_export::kontakt( $kontakt );
		
		echo json_encode( $kontaktdata );
		die();
	default:
		throw new Exception('Unknown findBy selector '. API_FINDBY_SELECTOR );
}