<?php

############
# POSTSTED #
###########

$id = $_GET['ID'];

echo json_encode(poststed($id));

function poststed($code) {
	require_once('UKM/sql.class.php');

	$qry = new SQL("SELECT `postalplace` FROM `smartukm_postalplace` WHERE `postalcode` = #code", array("code" => $code));

	$place = $qry->run('field', 'postalplace');

	if(empty($place)) return false;
	return $place;
}