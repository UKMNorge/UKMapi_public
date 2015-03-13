<?php
	
$cron_id = $_GET['ID'];

require_once('UKM/sql.class.php');
require_once('UKM/innslag.class.php');
require_once('UKM/tv.class.php');
require_once('UKM/monstring.class.php');
 
$tv_file = new tv( false, $cron_id );


$infos = new stdClass();

$infos->tittel = $tv_file->title;
$infos->url = $tv_file->full_url;

$infos->type = $tv_file->b_id == 0 ? 'Reportasje' : 'Innslag';

$infos->pl_id = $tv_file->tag('pl');

$monstring = new monstring( $infos->pl_id );
$type_monstring = $monstring->g('type');

$infos->sesong = $monstring->g('season');
$infos->monstring = new stdClass();
$infos->monstring->navn = $monstring->g('pl_name');

if( empty( $infos->pl_id ) ) {
    $infos->monstring->navn = 'Ukjent';
    $path = $infos->sesong .'/Ukjent/';
} elseif( $infos->type == 'land' ) {
	$infos->monstring->sokestreng = $infos->monstring->navn .' '. $infos->sesong;	
	$path = $infos->sesong .'/UKM-festivalen/';
} elseif( $infos->type == 'fylke' ) {
	$infos->monstring->sokestreng = 'Fylkesmønstringen '. $infos->monstring->navn .' i '. $infos->sesong;
	$infos->monstring->fylke = $monstring->g('fylke_name');
	$path = $infos->sesong .'/'. $infos->monstring->navn .'(PLID'. $infos->pl_id .')/';
} else {
	$infos->monstring->sokestreng = 'Lokalmønstringen '. $infos->monstring->navn .' i '. $infos->sesong;
	$infos->monstring->fylke = $monstring->g('fylke_name');
	$kommuner = $monstring->g('kommuner');
	$infos->monstring->kommuner = '';
	foreach( $kommuner as $kommune ) {
		$infos->monstring->kommuner .= $kommune['name'] .', ';
	}
	$infos->monstring->kommuner = rtrim( $infos->monstring->kommuner, ', ' );
	$path = $infos->sesong .'/'. $infos->monstring->fylke .'/'. $infos->monstring->kommuner .' (PLID'. $infos->pl_id .')/';
}

$infos = new stdClass();

$infos->tittel = $tv_file->title;
$infos->url = $tv_file->full_url;

$infos->type = $tv_file->b_id == 0 ? 'Reportasje' : 'Innslag';

if( $infos->type == 'Reportasje' ) {
	$path .= 'Reportasje/';
} else {
	$path .= 'Innslag/';
	$infos->innslag = new stdClass();
	$infos->innslag->ID = $tv_file->b_id;
	$infos->innslag->b_id = $infos->innslag->ID;
	
	$innslag = new innslag( $infos->innslag->ID, true );
	$innslag->loadGEO();
	
	$infos->innslag->navn = $innslag->g('b_name');
	$infos->innslag->kommune = $innslag->g('kommune');
	$infos->innslag->fylke = $innslag->g('fylke');
	
	$infos->innslag->type = mb_convert_case($innslag->g('b_kategori'), MB_CASE_TITLE);
	$infos->innslag->kategori = mb_convert_case($innslag->g('kategori'), MB_CASE_TITLE);
	$infos->innslag->sjanger = mb_convert_case($innslag->g('sjanger'), MB_CASE_TITLE);
	
	$infos->innslag->kontaktperson = infos_person( $innslag->g('b_contact') );
	
	$personer = $innslag->personer();
	foreach( $personer as $person ) {
		$infos->innslag->personer[] = infos_person( $person['p_id'], $infos->innslag->ID );
	}
	
	$titler = $innslag->titler();
	foreach( $titler as $tittel ) {
		$infos->innslag->titler[] = infos_tittel( $tittel );
	}
}

$infos->path = new stdClass();
$infos->path->dir = $path;
$infos->path->filename = preg_replace('/[^\da-z -æøå]/i', '',$tv_file->title);

function infos_person( $p_id, $b_id=false ) {
	$person = new person( $p_id, $b_id );
	$data = new stdClass();

	$data->p_id = $person->g('p_id');
	$data->navn = $person->g('p_firstname') .' '. $person->g('p_lastname');
	$data->fornavn = $person->g('p_firstname');
	$data->etternavn = $person->g('p_lastname');
	$data->telefon = $person->getNicePhone() .' ('. $person->g('p_phone') .')';
	$data->alder = $person->alder() .' år';	
	$data->epost = $person->g('p_email');
	
	return $data;
}

function infos_tittel( $tittel ) {
	$data = new stdClass();
	
	$data->tittel = $tittel->g('tittel');
	$data->tekst_av = $tittel->g('tekst_av');
	$data->melodi_av = $tittel->g('melodi_av');
	$data->koreografi_av = $tittel->g('koreografi');
	
	return $data;
}

#echo json_encode( $infos );

echo '<pre>';
var_dump( $infos );
echo '</pre>';