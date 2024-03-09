<?php

############
# POSTSTED #
###########

use UKMNorge\Database\SQL\Query;
require_once('UKM/Autoloader.php');

$id = $_GET['ID'];

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
// ini_set('display_errors', true);
// error_reporting(E_ALL);
$p["sted"] = poststed($id);
$p["sted"] = mb_convert_encoding($p["sted"], 'UTF-8', mb_detect_encoding($p["sted"]));

echo json_encode($p);

function poststed($code) {
	$qry = new Query("SELECT `postalplace` FROM `smartukm_postalplace` WHERE `postalcode` = #code", array("code" => $code));

	$place = $qry->run('field', 'postalplace');

	//var_dump($place);
	if(empty($place)) return false;
	return ($place);
}