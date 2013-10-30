<?php
echo '<h2>Register video</h2>';

require_once('UKM/innslag.class.php');
require_once('UKM/monstring.class.php');

$monstring = new monstring( $_POST['pl_id'] );

if( (int) $_POST['b_id'] > 0 )
	$innslag = new innslag( $_POST['b_id'] );
else
	$innslag = false;
	
//// UKM WP RELATED VIDEO
if($innslag) {
	$cron_id		= $_POST['id'];
	$blog_id 		= $_POST['blog_id'];
	$blog_url 		= 'http:' . $monstring->get('link');
	
	$b_id			= $innslag->g('b_id');
	$b_kommune		= $innslag->g('b_kommune');
	$season			= $_POST['season'];
	
	$pl_type		= $monstring->get('type');
	
	
	$post_meta		= array('file' => 'ukmno/videos/' . $_POST['file_path']. $_POST['file_name_store'],
							'nicename' => $blog_id,
							'img' => 'ukmno/videos/' . $_POST['file_path']. str_replace('.mp4','.jpg', $_POST['file_name_store']),
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
	
	if(!$already_exists) {
		$sql = new SQLins('ukmno_wp_related');
	} else {
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
	
	echo '<strong>Create video/band relation</strong><br />'
		. $sql->debug() . '<br />';
	$sql->run();


	$sql2 = new SQLins('ukm_related_video',
					  array('cron_id' => $cron_id));
	$sql2->add('file', 'ukmno/videos/' . $_POST['file_path']. $_POST['file_name_store']);
	
	echo '<strong>Tell videomodule file is converted</strong><br />'
		. $sql2->debug() . '<br />';
	$sql2->run();
} else {
	die('Not supported video');
}
?>