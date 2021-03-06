<?php

use UKMNorge\Database\SQL\Query;

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once('UKM/Autoloader.php');

$qryTotal = "(SELECT COUNT(`konkurranse_svar`.`id`) AS `total` FROM `konkurranse_svar` WHERE `konkurranse_svar`.`sporsmal_id` = '#key')";

$sql = new Query("
	SELECT `alternativ`.`id` AS `id`, COUNT(`konkurranse_svar`.`id`) AS `antall`,`alternativ`.`name`,
		$qryTotal AS `total`,
		FLOOR((100/$qryTotal * COUNT(`konkurranse_svar`.`id`))) AS `prosent`
	FROM `konkurranse_svar`
	LEFT JOIN `konkurranse_alternativ` AS `alternativ`
		ON(`alternativ`.`id` = `konkurranse_svar`.`alternativ_id`)
	WHERE `konkurranse_svar`.`sporsmal_id` = '#key'
	GROUP BY `alternativ_id`
	ORDER BY `alternativ_id` ASC
	LIMIT 6
	",
	['key' => $_GET['ID']]
);

$res = $sql->run();
$antall = Query::fetch( $res );
$resultat = [];
while( $row = Query::fetch( $res ) ) {
	$resultat[] = $row;
}

echo json_encode( ['data' => $resultat] );

die();