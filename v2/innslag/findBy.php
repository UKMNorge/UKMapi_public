<?php

require_once('UKM/monstringer.class.php');

$monstring = new monstring_v2( API_MONSTRING );

switch( API_FINDBY_SELECTOR ) {
	case 'id':
		$innslag = $monstring->getInnslag()->get( API_FINDBY_ID );
        $export = json_export::innslag( $innslag );
        $export->bilder = [];
        $export->artikler = [];
        $export->filmer = [];
        
        // BILDER
        foreach( $innslag->getBilder()->getAll() as $bilde ) {
            $export_bilde = json_export::bilde( $bilde );
            $export_bilde->storrelser = [];
            foreach( ['thumbnail','medium','large'] as $storrelse ) {
                $export_bilde->storrelser[ $storrelse ] = json_export::bilde( $bilde, $storrelse );
            }
            $export->bilder[] = $export_bilde;
        }

        // ARTIKLER
        foreach( $innslag->getArtikler()->getAll() as $artikkel ) {
            $export->artikler[] = json_export::artikkel( $artikkel );
        }

        // UKM-TV
        if( is_array( $innslag->getFilmer()  ) ) {
            foreach( $innslag->getFilmer() as $film ) {
                $export->filmer[] = json_export::tv( $film );
            }
        }
        break;
	default:
		throw new Exception('Unknown findBy selector '. API_FINDBY_SELECTOR );
}

echo json_encode( $export );
die();
