<?php
	
$cron_id = $_GET['ID'];

require_once('UKM/sql.class.php');
require_once('UKM/innslag.class.php');
require_once('UKM/tv.class.php');

$tv_file = new tv( false, $cron_id );


$infos = new stdClass();

$infos->tittel = $tv_file->title;
$infos->url = $tv_file->full_url;

$infos->type = $tv_file->b_id == 0 ? 'Reportasje' : 'Innslag';

if( $infos->type == 'Reportasje' ) {
	$pl_id = new SQL("SELECT `pl_id`
					  FROM `ukm_standalone_video`
					  WHERE `cron_id` = '#cron_id'",
					array('cron_id' => $cron_id )
					);
	$pl_id = $pl_id->run('field','pl_id');
} else {
	$infos->innslag = new stdClass();
	$infos->innslag->ID = $tv_file->b_id;
	
	$innslag = new innslag( $infos->innslag->ID, true );
	$innslag->loadGEO();
	
	$infos->innslag->navn = $innslag->g('b_name');
	$infos->innslag->kommune = $innslag->g('kommune');
	$infos->innslag->fylke = $innslag->g('fylke');
	
	$infos->innslag->type = mb_strtoupper($innslag->g('b_kategori'));
	$infos->innslag->kategori = mb_strtoupper($innslag->g('kategori'));
	$infos->innslag->sjanger = mb_strtoupper($innslag->g('sjanger'));
	
	$infos->innslag->kontaktperson = infos_person( $innslag->g('b_contact') );
	
	$personer = $innslag->personer();
	foreach( $personer as $person ) {
		$infos->innslag->personer[] = infos_person( $
	}
}

function infos_person( $p_id, $b_id=false ) {
	$person = new person( $p_id, $b_id );
	$data = new stdClass();

	$data->fornavn = $person->g('p_firstname');
	$data->etternavn = $person->g('p_lastname');
	$data->telefon = $person->getNicePhone() .' ('. $person->g('p_phone') .')';
	$data->alder = $person->alder() .' år';	
}