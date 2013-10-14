<?php
if(!isset($_GET['b_id']))
	die(false);
	
require_once('UKM/innslag.class.php');
$innslag = new innslag( $_GET['b_id'] );
$innsslag->statistikk_oppdater();