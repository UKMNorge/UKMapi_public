<?php

use UKMNorge\Database\SQL\Query;

if( !isset( $_SERVER['PHP_AUTH_USER'] ) || empty( $_SERVER['PHP_AUTH_USER'] ) ) {
    abort('Missing auth.', 0);   
}

if( !isset( $_SERVER['PHP_AUTH_PW'] ) || empty( $_SERVER['PHP_AUTH_PW'] ) ) {
    abort('Missing auth.', 0);
}

require_once('UKM/Autoloader.php');

// CHECK API AUTH OK
$authCheck = new Query(
    "SELECT * 
    FROM `DipToken`
    WHERE `uuid` = '#uuid'
    AND `token` = '#token'
    AND `active` = 1
    ",
    [
        'uuid' => $_SERVER['PHP_AUTH_USER'],
        'token' => $_SERVER['PHP_AUTH_PW']
    ]
);
DBread::setDatabase('ukmdelta');
$res = $authCheck->run();

if( !$res || Query::numRows( $res ) == 0 ) {
    abort('Auth failed.', 1);
}


die('LOG REQUEST HERE');
// RESET DB CONNECTION
DBread::setDatabase('ukm');