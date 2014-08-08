<?php
header('Content-Type: application/json; charset=utf-8');

error_log('REGISTRER:VIDEO');
error_log('CRON_ID: '. var_export($_POST['id'],true));

foreach( $_POST as $key => $val ) {
    error_log('POST:'. $key .' => '. var_export($val,true));
}

if( !is_numeric( $_POST['id'] ) || $_POST['id'] == 0 ) {
   	error_log('ERROR: Ugyldig cron ID');
	error_log('FAILURE REGISTRER:VIDEO CRON_ID: '. var_export($_POST['id'],true));
    header( 'HTTP/1.1 450 BAD REQUEST' );
	die( json_encode( array('success' => false, 'type' => 'unknown') ) );
}

require_once('UKM/innslag.class.php');
require_once('UKM/monstring.class.php');

$monstring = new monstring( $_POST['pl_id'] );

if( (int) $_POST['b_id'] > 0 )
	$innslag = new innslag( $_POST['b_id'] );
else
	$innslag = false;
	
//// UKM WP RELATED VIDEO
if($innslag) {
    error_log('TYPE: INNSLAG');
	$cron_id		= $_POST['id'];
	$blog_id 		= $_POST['blog_id'];
	$blog_url 		= 'http:' . $monstring->get('link');
	
	$b_id			= $innslag->g('b_id');
	$b_kommune		= $innslag->g('b_kommune');
	$season			= $_POST['season'];
	
	$pl_type		= $monstring->get('type');
	
	$file_with_path = 'ukmno/videos/' . $_POST['file_path']. $_POST['file_name_store'];
	
	$post_meta		= array('file' => $file_with_path,
							'nicename' => $blog_id,
							'img' => str_replace('.mp4','.jpg', $file_with_path),
							'title' => ucfirst($pl_type));
	
	$already_exists = new SQL("SELECT `rel_id`
							   FROM `ukmno_wp_related`
							   WHERE `post_type` = 'video'
							   AND `post_id` = '#cron_id'
							   AND `blog_id` = '#blog_id'",
							   array('cron_id' => $cron_id,
							   		 'blog_id' => $blog_id)
							   );
	$already_exists = $already_exists->run();
	$already_exists = mysql_fetch_assoc( $already_exists );
	
	// REGISTRER MOT INNSLAG
	if(!$already_exists) {
	    error_log('Video finnes ikke i related-table fra tidligere');
		$sql = new SQLins('ukmno_wp_related');
	} else {
	    error_log('Video finnes allerede (rel_id: '. $already_exists['rel_id'].')');
	    		$sql = new SQLins('ukmno_wp_related',
						  array('rel_id' => $already_exists['rel_id']));
	}
	$sql->add('blog_id', $blog_id);
	$sql->add('blog_url', $blog_url);

	$sql->add('post_id', $cron_id);
	$sql->add('post_type', 'video');

	$sql->add('post_meta', serialize( $post_meta ) );

	$sql->add('b_id', $b_id);
	$sql->add('b_kommune', $b_kommune);
	$sql->add('b_season', $season);
	
	$sql->add('pl_type', $pl_type);
	
	$sql->run();
	error_log('Oppdater wp_related, knytt film mot innslag');
	error_log('WP_RELATED QRY: '. $sql->debug());

	// REGISTRER MOT OPPLASTER-TABELL
	$sql2 = new SQLins('ukm_related_video',
					  array('cron_id' => $cron_id));
	$sql2->add('file', $file_with_path);
	$sql2->run();
	error_log('Oppdater UKM Video-modulen med status');
	error_log('RELATED_VIDEO QRY: '. $sql2->debug());
	
	// REGISTRER MOT UKM-TV
	require_once('UKM/inc/tv/cron.functions.tv.php');
	$qry = new SQL("SELECT * 
				FROM `ukmno_wp_related`
				WHERE `post_id` = '#cronid'
				AND `post_type` = 'video'
				LIMIT 1",
				array('cronid' => $cron_id));
	
	$res = $qry->run('array');
    error_log('Registrer i UKM-TV');
	if( is_array($res) ) {
	    error_log('Data sendt inn til tv_update()');
	    foreach( $res as $key => $val ) {
    	    error_log('TV_UPDATE:'. $key .' => '. var_export($val, true));
	    }
		$data = video_calc_data('wp_related', $res);
		tv_update($data);
		
		error_log('SUCCESS REGISTRER:VIDEO CRON_ID: '. var_export($_POST['id'],true));		
		die( json_encode( array('success' => true, 'type' => 'innslag') ) );
	} else {
    	error_log('ERROR: fikk ikke hentet ut data fra wp_related!');
		error_log('FAILURE REGISTRER:VIDEO CRON_ID: '. var_export($_POST['id'],true));
        header( 'HTTP/1.1 451 BAD REQUEST' );
		die( json_encode( array('success' => false, 'type' => 'innslag') ) );
	}

} else {
    error_log('TYPE: REPORTASJE');

	$file_with_path = 'ukmno/videos/' . $_POST['file_path']. $_POST['file_name_store'];

	$update = new SQLins('ukm_standalone_video', array( 'cron_id' => $_POST['id'] ));
	$update->add('video_file', $file_with_path);
	$update->add('video_image', str_replace('.mp4','.jpg', $file_with_path));
	$update->run();	
    error_log('Oppdater standalone video');
    error_log('STANDALONE QRY: '. $update->debug() );

	require_once('UKM/inc/tv/cron.functions.tv.php');
	$qry = new SQL("SELECT * 
					FROM `ukm_standalone_video` 
					WHERE `cron_id` = '#file'",
					array('file' => $_POST['id']));
	$res = $qry->run('array');
    error_log('Registrer i UKM-TV');
	if($res) {
	    error_log('Data sendt inn til tv_update()');
	    foreach( $res as $key => $val ) {
    	    error_log('TV_UPDATE:'. $key .' => '. var_export($val, true));
	    }
		$data = video_calc_data('standalone_video', $res );
		tv_update($data);
		error_log('SUCCESS REGISTRER:VIDEO CRON_ID: '. var_export($_POST['id'],true));
		die( json_encode( array('success' => true, 'type' => 'reportasje') ) );
	} else {
    	error_log('ERROR: fikk ikke hentet ut data fra standalone_video!');
		error_log('FAILURE REGISTRER:VIDEO CRON_ID: '. var_export($_POST['id'],true));
        header( 'HTTP/1.1 452 BAD REQUEST' );
		die( json_encode( array('success' => false, 'type' => 'reportasje') ) );
	}
}
?>