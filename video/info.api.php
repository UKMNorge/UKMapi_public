<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Filmer\UKMTV\Filmer;
use UKMNorge\Filmer\Upload\Film;
use UKMNorge\Innslag\Innslag;
use UKMNorge\Innslag\Personer\Person;
use UKMNorge\Innslag\Titler\Tittel;

header('Content-Type: application/json; charset=utf-8');

require_once('UKM/Autoloader.php');

$cron_id = (int) $_GET['ID'];

$infos = new stdClass();
$infos->monstring = new stdClass();

$upload = new Film($cron_id);

$film = Filmer::getById($upload->getTvId());


try {
	$arrangement = new Arrangement($film->getArrangementId());
	$infos->pl_id = $arrangement->getId();
	$infos->monstring->navn = $arrangement->getNavn();
	$infos->monstring->type = $arrangement->getEierType();
	$infos->sesong = $arrangement->getSesong();
} catch (Exception $e) {
	$infos->pl_id = 0;
	$infos->monstring->navn = 'Ukjent';
	$infos->monstring->type = 'ukjent';
	$infos->sesong = $film->getSe;
}

$infos->tittel = $film->getTitle();
$infos->tv_url = $film->getTvUrl();
$infos->tv_file = $film->getFile();
$infos->tv_path = $film->getFilePath();
$infos->cron_id = $cron_id;
$infos->tv_id = $film->getTvId();

$infos->type = $film->getInnslagId() == 0 ? 'Reportasje' : 'Innslag';

if (empty($infos->pl_id)) {
	$infos->monstring->navn = 'Ukjent';
	$path = $film->getSeason() . '/Ukjent/';
} elseif ($infos->monstring->type == 'land') {
	$infos->monstring->sokestreng = $infos->monstring->navn . ' ' . $infos->sesong;
	$path = $infos->sesong . '/UKM-festivalen/';
} elseif ($infos->monstring->type == 'fylke') {
	$infos->monstring->sokestreng = 'Fylkesmønstringen ' . $infos->monstring->navn . ' i ' . $infos->sesong;
	$infos->monstring->fylke = $arrangement->getFylke()->getNavn();
	$path = $infos->sesong . '/' . $arrangement->getFylke()->getNavn() . '/_Fylkesmønstringen (PLID' . $infos->pl_id . ')/';
} else {
	$infos->monstring->sokestreng = 'Lokalmønstringen ' . $infos->monstring->navn . ' i ' . $infos->sesong;
	$infos->monstring->fylke = $arrangement->getFylke()->getNavn();
	$infos->monstring->kommuner = join(', ', $arrangement->getKommuner()->getAll());
	$path = $infos->sesong . '/' . $infos->monstring->fylke . '/' . $infos->monstring->kommuner . ' (PLID' . $infos->pl_id . ')/';
}

if ($infos->type == 'Reportasje') {
	$path .= 'Reportasje/';
} else {
	$path .= 'Innslag/';
	$infos->innslag = new stdClass();
	$infos->innslag->ID = $film->getInnslagId();
	$infos->innslag->b_id = $infos->innslag->ID;

	$innslag =  $arrangement->getInnslag()->get($infos->innslag->ID, true);

	$infos->innslag->navn = $innslag->getNavn();
	$infos->innslag->kommune = $innslag->getKommune()->getNavn();
	$infos->innslag->fylke = $innslag->getFylke()->getNavn();

	$infos->innslag->type = mb_convert_case($innslag->getType()->getNavn(), MB_CASE_TITLE);
	$infos->innslag->kategori = mb_convert_case($innslag->getKategori(), MB_CASE_TITLE);
	$infos->innslag->sjanger = mb_convert_case($innslag->getSjanger(), MB_CASE_TITLE);

	$infos->innslag->kontaktperson = infos_person($innslag->getKontaktperson());

	$infos->innslag->personer = [];
	foreach ($innslag->getPersoner()->getAll() as $person) {
		$infos->innslag->personer[] = infos_person($person);
	}

	$infos->innslag->titler = [];

	if ($innslag->getType()->harTitler()) {
		foreach ($innslag->getTitler()->getAll() as $tittel) {
			$infos->innslag->titler[] = infos_tittel($tittel);
		}
	}
}

$infos->path = new stdClass();
$infos->path->dir = $path;
$infos->path->filename = preg_replace('/[^\da-z -æøå]/i', '', $film->title) . ' (CRONID' . $cron_id . ' TVID' . $film->getId() . ')';

function infos_person(Person $person)
{
	$data = new stdClass();

	$data->p_id = $person->getId();
	$data->navn = $person->getNavn();
	$data->fornavn = $person->getFornavn();
	$data->etternavn = $person->getEtternavn();
	$data->telefon = $person->getMobil();
	$data->alder = $person->getAlder();
	$data->epost = $person->getEpost();
	$data->instrument = $person->getRolle();

	return $data;
}

function infos_tittel(Tittel $tittel)
{
	$data = new stdClass();

	$data->tittel = $tittel->getTittel();

	if (get_class($tittel) == 'UKMNorge\Innslag\Titler\Musikk') {
		$data->tekst_av = $tittel->getTekstAv();
		$data->melodi_av = $tittel->getMelodiAv();
	} else {
		$data->tekst_av = '';
		$data->melodi_av = '';
	}

	if (get_class($tittel) == 'UKMNorge\Innslag\Titler\Dans') {
		$data->koreografi_av = $tittel->getKoreografiAv();
	} else {
		$data->koreografi_av = '';
	}

	return $data;
}

echo json_encode($infos);
die();
