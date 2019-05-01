<?php
require_once('UKM/innslag_typer.class.php');

$export = [];

foreach( innslag_typer::getAllTyper() as $type ) {
	if( $type->getId() == 1 ) {
		foreach( innslag_typer::getAllScene() as $scene_type ) {
			$export[] = json_export::innslag_type( $scene_type );
		}
	} else {
		$export[] = json_export::innslag_type( $type );
	}
}
echo json_encode( $export );
die();