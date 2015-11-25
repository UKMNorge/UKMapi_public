<?php

############
# POSTSTED #
###########

$id = $_GET['ID'];

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
ini_set('display_errors', true);
error_reporting(E_ALL);
// echo mb_detect_encoding(poststed($id));
//$p["sted"] = mb_convert_encoding(poststed($id), 'UTF-8', mb_detect_encoding(poststed($id)));
$p["sted"] = poststed($id);
var_dump($p);
//echo mb_detect_encoding(poststed($id));
// echo $p;
echo json_encode($p);

function poststed($code) {
	require_once('UKM/sql.class.php');

	$qry = new SQL("SELECT `postalplace` FROM `smartukm_postalplace` WHERE `postalcode` = #code", array("code" => $code));

	$place = $qry->run('field', 'postalplace');

	//var_dump($place);
	if(empty($place)) return false;
	return ($place);
}