<?php
if(!isset($_GET['api'])) {
	die(false);
}

if(!file_exists($_GET['api'].'.api.php'))
	die(false);
	
require_once($_GET['api'].'.api.php');