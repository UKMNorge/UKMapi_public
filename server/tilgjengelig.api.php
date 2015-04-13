<?php
	require_once('UKM/curl.class.php');
	$UKMCURL->request( 'http://'. $ID );
	
	var_dump( $UKMCURL );
?>