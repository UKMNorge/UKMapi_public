<?php

use UKMNorge\Innslag\Innslag;
require_once('UKM/Autoloader.php');

if(!isset($_GET['ID']))
	die(false);
	
$innslag = new Innslag( intval($_GET['ID']), false );
$innslag->statistikk_oppdater();
die(json_encode(array('success' => true)));
