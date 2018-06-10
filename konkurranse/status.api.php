<?php
require_once('UKM/sql.class.php');

$qryTotal = "(SELECT COUNT(`id`) AS `total` FROM `konkurranse_svar` WHERE `sporsmal_id` = '#key')";

$sql = new SQL("
	SELECT `svar` AS `id`, COUNT(`id`) AS `antall`,
		$qryTotal AS `total`,
		FLOOR((100/$qryTotal * COUNT(`id`))) AS `prosent`
	FROM `konkurranse_svar`
	WHERE `sporsmal_id` = '#key'
	GROUP BY `svar`
	ORDER BY `svar` ASC
	",
	['key' => $_GET['ID']]
);

$res = $sql->run();
$antall = mysql_num_rows( $res );
$resultat = [];
while( $row = mysql_fetch_assoc( $res ) ) {
	$resultat[] = $row;
}

echo json_encode( ['data' => $resultat] );

die();