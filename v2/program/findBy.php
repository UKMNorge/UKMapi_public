<?php
	
use UKMNorge\Arrangement\Arrangement;

require_once('UKM/Autoloader.php');

switch( API_FINDBY_SELECTOR ) {
	case 'id':
		$monstring = new Arrangement( API_MONSTRING );
		$hendelse = $monstring->getProgram()->get( API_FINDBY_ID );
		
		$export_hendelse = json_export::hendelse( $hendelse );
		$export_hendelse->innslag = [];
		
		if( $hendelse->harSynligDetaljprogram() ) {
			foreach( $hendelse->getInnslag()->getAll() as $innslag ) {
				$export_hendelse->innslag[] = json_export::innslag( $innslag );
			}
		}

		echo json_encode( $export_hendelse );
		die();
	default:
		throw new Exception('Unknown findBy selector '. API_FINDBY_SELECTOR );
}