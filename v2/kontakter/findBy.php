<?php
	
use UKMNorge\Arrangement\Arrangement;

require_once('UKM/Autoloader.php');

switch( API_FINDBY_SELECTOR ) {
	case 'id':
		$monstring = new Arrangement( API_MONSTRING );
		$kontakt = $monstring->getKontaktpersoner()->get( API_FINDBY_ID );
		
		$kontaktdata = json_export::kontakt( $kontakt );
		
		echo json_encode( $kontaktdata );
		die();
	default:
		throw new Exception('Unknown findBy selector '. API_FINDBY_SELECTOR );
}