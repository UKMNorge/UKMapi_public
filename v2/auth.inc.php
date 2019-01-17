<?php

if( !isset( $_SERVER['PHP_AUTH_USER'] ) || empty( $_SERVER['PHP_AUTH_USER'] ) ) {
    abort('Missing auth.', 0);   
}

if( !isset( $_SERVER['PHP_AUTH_PW'] ) || empty( $_SERVER['PHP_AUTH_PW'] ) ) {
    abort('Missing auth.', 0);
}

require_once('UKM/sql.class.php');

// CHECK API AUTH OK
$authCheck = new SQL(
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

if( !$res || SQL::numRows( $res ) == 0 ) {
    abort('Auth failed.', 1);
}

// RESET DB CONNECTION
DBread::setDatabase('ukm');