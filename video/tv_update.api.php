<?php
// http://api.ukm.no/video:updateTV/$id
require_once('UKM/sql.class.php');
require_once('UKM/inc/tv/cron.functions.tv.php');

if(!isset($_POST['type'])) {
	die('Missing type!');
}

switch($_POST['type']) {
	case 'band_related':
		die('Missing support for numeric ID');
		/*
		$qry = new SQL("SELECT * 
					FROM `ukmno_wp_related`
					WHERE `post_meta` LIKE '%#file%'
					AND `post_type` = 'video'
					ORDER BY `rel_id` DESC
					LIMIT 1",
					array('file' => $_GET['ID']));
					
		$res = $qry->run('array');
		if($res) {
			$data = video_calc_data('wp_related', $res);
			tv_update($data);
		}
		*/
		break;
	case 'standalone':
		$qry = new SQL("SELECT * 
						FROM `ukm_standalone_video` 
						WHERE `cron_id` = '#cron_id'",
						array('file' => $_GET['ID']));
		$res = $qry->run('array');
		if($res) {
			$data = video_calc_data('standalone_video', $res );
			tv_update($data);
		}
		break;
}