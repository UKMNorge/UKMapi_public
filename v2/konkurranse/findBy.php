<?php

require_once('UKM/Konkurranse/sporsmal.collection.php');

$alternativer = [];

switch( API_FINDBY_SELECTOR ) {
	case 'id':
	default:
		$konkurranse = SporsmalColl::getById( API_FINDBY_ID );	
		
		$export = new stdClass();
		$export->id 	= $konkurranse->getId();
		$export->navn	= $konkurranse->getName();
		$export->alternativer = [];

		
		foreach( $konkurranse->getAlternatives()->getAllByName() as $alternativ ) {
			$data = new stdClass();
			$data->id = $alternativ->getId();
			$data->navn = $alternativ->getName();
			$export->alternativer[] = $data;
		}
	break;
}

echo json_encode( $export );
die();