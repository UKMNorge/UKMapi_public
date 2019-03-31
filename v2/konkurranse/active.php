<?php
header('Cache-Control: no-store');

require('UKM/Konkurranse/config.class.php');

$active = Config::get('app_active');

if( $active == 0 ) {
	$active = false;
} else {
	$active = (int) $active;
}

echo json_encode( $active );
die();