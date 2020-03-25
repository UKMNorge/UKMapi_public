<?php

use UKMNorge\Innslag\Typer\Typer;

require_once('UKM/Autoloader.php');

$export = [];

foreach( Typer::getAllTyper() as $type ) {
	if( $type->getId() == 1 ) {
		foreach( Typer::getAllScene() as $scene_type ) {
			$export[] = json_export::innslag_type( $scene_type );
		}
	} else {
		$export[] = json_export::innslag_type( $type );
	}
}
echo json_encode( $export );
die();