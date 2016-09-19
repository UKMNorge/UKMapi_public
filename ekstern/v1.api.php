<?php
# Ekstern 
# Autentiserings-API for å benytte UKM-tjenester
# Version 0.1
# Author: UKM Norge / A Hustad

error_reporting(E_ALL);
#var_dump($_GET['ID']);
require_once('UKM/sql.class.php');

### ROUTER:
$call = $_GET['ID'];

// Todo: Implement switch here, so doSign can't be called externally?
if(function_exists($call)) {
	$result = $call();	
}
else {
	$result = new stdClass();
	$result->success = false;
	$result->errors[] = 'UKMapi: API-kallet du forespurte finnes ikke!';
}

echo json_encode($result);
return;

### Tilgang
## Sjekker om et gitt system har rettigheter for tjeneste.
## Databasestruktur:
# API_Keys:
# 	- id, numerisk
# 	- api_key, VARCHAR(50) UNIQUE - Identifikator for tjenesten som spør om tilgang.
#	- secret, VARCHAR(60) - Brukes for signering av requests.
# API_Permissions:
#	- id, numerisk
#	- system, VARCHAR(50) - Identifikator for systemet.
#	- permission, VARCHAR(50) - tekststreng med rettighetsnavnet det spørres om.
#	- api_key, FOREIGN KEY fra API_Keys->api_key
## Input (i $_POST):
# API_KEY - streng, nøkkel til tjeneste som spør om tilgang
# SYSTEM - streng, nøkkel til systemet det bes om tilgang til
# PERMISSION - streng, nøkkel til rettigheten det bes om. Varierer fra system til system.
## Output:
# Et objekt som beskriver om forespørselen er godkjent eller ikke.
#
function tilgang() {
	$result = new stdClass();
	// URLRewrite håndterer en del av sikkerheten, SQL-klassen resterende.
	$api_key = $_POST['API_KEY'];
	$system = $_POST['SYSTEM'];
	$permission = $_POST['PERMISSION'];

	if($api_key == null || $system == null || $permission == null) {
		$result->success = false;
		$result->errors[] = 'UKMapi: Mangler èn eller flere parametere.';
		return $result;
	}

	#$api_key = 'test';
	#$system = 'rsvp';
	#$permission = 'read';

	
	$qry = new SQL("SELECT COUNT(*) FROM API_Permissions 
					WHERE `api_key` = '#api_key'
					AND `system` = '#system'
					AND `permission` = '#permission'", 
					array(	'api_key' => $api_key,
							'system' => $system,
							'permission' => $permission));
	#echo $qry->debug();
	#$qry->error();
	$res = $qry->run('field', 'COUNT(*)');
	
	if($res == 1) {
		$result->success = true;
	}
	elseif($res === false) {
		$result->success = false;
		$result->errors[] = 'UKMapi: Det er intern feil, ta kontakt med support.';
		// TODO: Error log!
	}
	else {
		$result->success = false;
		$result->errors[] = 'UKMapi: Du har ikke tilgang til denne ressursen!';
	}
	return $result;
}

### Sign
# Signerer en POST-request for et gitt system og sjekker om signeringen er korrekt
# Signeringen er en md5-sum av api_key, variabler og secret.
## Inputs:
# $_POST[]-variabler
# $_POST['signed_request']
## Outputs:
# Et objekt med success = true/false
function sign() {
	#var_dump($_GET);
	#var_dump($_POST);
	$result = new stdClass();
	if($_POST['API_KEY'] == null || $_POST['signed_request'] == null) {
		$result->success = false;
		$result->errors[] = 'UKMapi: Mangler èn eller flere parametere.';
		return $result;
	}

	$api_key = $_POST['API_KEY'];
	$data = $_POST;
	$signed_request = $_POST['signed_request'];
	

	unset($data['signed_request']);
	$signed = doSign($api_key, $data);

	if($signed == $signed_request) {
		$result->success = true;
	}
	else {
		/*echo '<pre>data: <br>';
		var_dump($data);
		echo '<br>signed_request: <br>';
		var_dump($signed_request);
		echo '<br>signed: <br>';
		var_dump($signed);
		echo '</pre>';*/
		$result->success = false;
		$result->errors[] = 'UKMapi: Den signerte spørringen stemmer ikke.';
		// TODO: ERROR LOG THIS
	}

	return $result;
}

### Gjør det samme som tilgang og sign, men slått sammen.
## Dette er den anbefalte metoden å bruke for et eksternt system.
function signedTilgang() {
	$validate = sign();
	$result = new stdClass();
	if($validate->success === true) {
		$tilgang = tilgang();
		if($tilgang->success === true) {
			$result->success = true;
		} 
		else {
			$result->success = false;
			$result->errors = $tilgang->errors;
		}
	} 
	else {
		$result->success = false;
		$result->errors = $validate->errors;
	}
	return $result;
}

### doSign
## Gjennomfører den faktiske signeringen av en POST-request.
function doSign($api_key, $data) {

	if($api_key == null || $data == null) {
		return false;
	}

	$qry = new SQL("SELECT secret FROM API_Keys
					WHERE `api_key` = '#api_key'", array('api_key' => $api_key));
	$secret = $qry->run('field', 'secret');
	if($secret == false) {
		return false;
	}
	return hash('sha256', $api_key . http_build_query($data) . $secret);
}
