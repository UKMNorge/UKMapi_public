<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

require_once('UKMconfig.inc.php');
require_once('functions.inc.php');
if( UKM_HOSTNAME == 'ukm.dev' ) {
	ini_set('display_errors', true);
}
$current_season = date('n') < 8 ? (int) date('Y') : (int) date('Y') + 1;

define('API', empty( $_GET['API'] ) ? 'list' : $_GET['API'] );
define('API_SEASON', isset( $_GET['SESONG'] ) ? $_GET['SESONG'] : $current_season );
define('API_VERSION', $_GET['V']);

/**
 * Path til auth-check
 * Endpoints som krever autentisering må inkludere følgende kode-linje:
 * require_once( AUTH ); 
*/
define('AUTH', dirname( __FILE__ ) .'/auth.inc.php');

/**
 * Tomt kall vil returnere en liste
**/
if( empty( $_GET['CALL'] ) ) {
	define('API_CALL', 'list');
}
/**
 * FindBy-kall krever en identifikator med ID og hva det skal finnes etter
 * F.eks: fylke-16 vil finne fylkesfestivalen for fylke 16
**/
elseif( strpos( $_GET['CALL'], '-' ) !== false ) {
	define('API_CALL', 'findBy');
	$data = explode('-', $_GET['CALL']);
	/* Hvis vi mangler enten ID eller selektor, die umiddelbart. */
	if ( !isset($data[0]) || empty($data[0]) || !isset($data[1]) || empty($data[1]) ) {
		die("false");
	}
	define('API_FINDBY_SELECTOR', $data[0]);
	define('API_FINDBY_ID', $data[1]);
}
elseif( is_numeric( $_GET['CALL'] ) ) {
	define('API_CALL', 'findBy');
	define('API_FINDBY_SELECTOR', 'id');
	define('API_FINDBY_ID', $_GET['CALL']);
}
/**
 * Finn og returner ønsket utvalg
**/
else {
	define('API_CALL', basename( $_GET['CALL'] ));
}

if( isset( $_GET['MONSTRING'] ) && !empty( $_GET['MONSTRING'] ) ) {
	define('API_MONSTRING', (int) $_GET['MONSTRING']);
} else {
	define('API_MONSTRING', false);
}


$folder = dirname( __FILE__ ) .'/'. API;

/**
 * V2.0 bruker apifil.php
 * V2.x bruker apifil-x.php
**/
if( API_VERSION == 0 ) {
	$file = $folder .'/'. API_CALL .'.php';
} else {
	$file = $folder .'/'. API_CALL .'-'. API_VERSION .'.php';
}

if( file_exists( $folder ) && is_dir( $folder ) && file_exists( $file ) ) {
	require_once( 'models.php');

	try {
		require_once( $file );
	} catch( Exception $e ) {
		abort( $e->getMessage(), $e->getCode() );
	}
}

die('false');