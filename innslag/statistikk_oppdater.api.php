<?php
if(!isset($_GET['ID']))
	die(false);
	
require_once('UKM/innslag.class.php');
$innslag = new innslag( $_GET['ID'] );
$innslag->statistikk_oppdater();
die(json_encode(array('success': true)));