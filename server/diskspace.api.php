<?php
require_once('UKMconfig.inc.php');

$info = new stdClass;
if( 'ukm.no' == UKM_HOSTNAME ) {
	$info->diskspace = diskfreespace("/home/");
	$info->total_diskspace = disk_total_space("/home/" );
} else {
	$info->diskspace = diskfreespace("/");
	$info->total_diskspace = disk_total_space("/" );
}
die(json_encode($info));