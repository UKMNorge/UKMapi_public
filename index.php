<?php
if(!isset($_GET['API']) || !isset($_GET['CALL'])) {
	die(false);
}

if(!file_exists($_GET['API'].'/'.$_GET['CALL'].'.api.php'))
	die(false);
	
require_once($_GET['API'].'/'.$_GET['CALL'].'.api.php');