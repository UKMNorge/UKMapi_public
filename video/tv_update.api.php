<?php
// http://api.ukm.no/video:updateTV/$id

if(isset($_POST['type']) && $_POST['type'] == 'standalone') {
	$qry = new SQL("SELECT * 
					FROM `ukm_standalone_video` 
					WHERE `cron_id` = '#cron_id'",
					array('file' => $_GET['ID']));
	$res = $qry->run('array');
	if($res) {
		require_once('UKM/inc/tv/cron.functions.tv.php');
		$data = video_calc_data('standalone_video', $res );
		tv_update($data);
	}
}