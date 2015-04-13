<?php
	require_once('UKM/curl.class.php');
	$UKMCURL->request( 'http://'. $_GET['ID'] );

	if( !$UKMCURL->result ) {
		die(0);
	}
	die(1);
?>