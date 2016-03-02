<?php
// API:    playback:file/_file_id_/_pl_id/

// SUBID = PL_ID
// ID = FILE ID



if( !isset( $_GET['ID'] ) || !isset( $_GET['SUBID']) || empty( $_GET['ID'] ) || empty( $_GET['SUBID'] ) ) {
	die('Mangler identifikator(er)'); 
}

require_once('UKM/sql.class.php');

$sql = new SQL("SELECT * 
				FROM `ukm_playback`
				WHERE `pb_id` = '#id'
				AND `pl_id` = '#plid'",
			   array('id' => $_GET['ID'], 'plid' => $_GET['SUBID'] )
			  );
$res = $sql->run();
$file = mysql_fetch_assoc( $res );

die( json_encode( $file ) );