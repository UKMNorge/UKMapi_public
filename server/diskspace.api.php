<?php
	
$info = new stdClass;
$info->diskspace = diskfreespace("/");
$info->total_diskspace = disk_total_space("/");

die(json_encode($info));