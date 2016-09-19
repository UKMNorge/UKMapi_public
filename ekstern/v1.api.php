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
function tilgang($api_key, $system, $permission) {
	$result = new stdClass();
	// URLRewrite håndterer en del av sikkerheten, SQL-klassen resterende.

	if($api_key == null || $system == null || $permission == null) {
		$result->success = false;
		$result->errors[] = 'UKMapi: Mangler èn eller flere parametere.';
		return $result;
	}
	
	$qry = new SQL("SELECT COUNT(*) FROM API_Permissions 
					WHERE `api_key` = '#api_key'
					AND `system` = '#system'
					AND `permission` = '#permission'", 
					array(	'api_key' => $api_key,
							'system' => $system,
							'permission' => $permission));
	
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

function signedTilgang2() {

	$result = new stdClass();
	// Sjekk at tidspunktene er innenfor maksgrensa vi tillater.
	if(!timestampsOkay()) {
		$result->success = false;
		$result->errors[] = 'UKMapi: Timestampene er ikke innenfor godkjent intervall!';
		return $result;
	}
	require_once(__DIR__.'/class/Signer.php');

	$api_key = $_POST['api_key'];
	$sys_key = $_POST['sys_key'];
	$originalSigner = new UKMNorge\APIBundle\Util\Signer($api_key, $secret);
	$serviceSigner = new UKMNorge\APIBundle\Util\Signer($sys_key, $secret);

	// VALIDATE EXTERNAL SERVICE
	$externalData = array();
	$externalData['time'] = $_POST['externalTime'];
	$internalSign1 = $originalSigner->sign($externalData);
	
	// VALIDATE DATA PROVIDER
	$serviceData = $_POST;
	$hidden = array('api_key', 'sign1', 'sign2', 'externalTime', 'sys_key');
	foreach ($serviceData as $key => $val) {
		if(in_array($key, $hidden)) {
			unset($serviceData[$key]);
		}
	}

	$serviceData['signature'] = $internalSign1;
	$internalSign2 = $serviceSigner->sign($serviceData);
	
	if($internalSign2 == $_POST['sign2'] && $internalSign1 == $_POST['sign1']) {
		$tilgang = tilgang($api_key, $_POST['sys_key'], $_POST['permission']);
		if($tilgang->success === true) {
			$result->success = true;
		} 
		else {
			$result->success = false;
			$result->errors = $tilgang->errors;
			return $result;
		}
		// Sign the result
		$result->sign = $serviceSigner->responseSign(get_object_vars($result));
		return $result;
	}
	else $result->success = false;
	
	return $result;
}

// TODO: Implementer
function timestampsOkay() {
	return false;
}