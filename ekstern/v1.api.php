<?php
# Ekstern 
# Autentiserings-API for å benytte UKM-tjenester
# Version 0.1
# Author: UKM Norge / A Hustad

use UKMNorge\Database\SQL\Query;

require_once('UKM/Autoloader.php');

error_reporting(0);
#var_dump($_GET['ID']);

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
	
	$qry = new Query("SELECT COUNT(*) FROM API_Permissions 
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
	try {
		$result = new stdClass();
		#$result->errors[] = "Debug: ".var_export($_POST, true);
		// Sjekk at tidspunktene er innenfor maksgrensa vi tillater.
		if(!timestampsOkay()) {
			$result->success = false;
			$result->errors[] = 'UKMapi: Timestampene er ikke innenfor godkjent intervall!';
			return $result;
		}
		require_once(__DIR__.'/class/Signer.php');

		## DATA FROM SERVICE:
		$service_time = $_POST['time'];
		$service_sys_key = $_POST['sys_key'];
		$service_permission = $_POST['permission'];
		$service_time = $_POST['time'];
		$service_sign = $_POST['sign2'];

		if(null == $service_time || null == $service_sys_key || null == $service_permission || null == $service_time || null == $service_sign ) {
			$result->success == false;
			$result->errors[] = "UKMapi: Mangler data fra UKM-tjenesten!";
		}

		## DATA FROM EXTERNAL SERVICE
		$external_time = $_POST['externalTime'];
		$external_sign = $_POST['sign1'];
		$external_api_key = $_POST['api_key'];

		if(null == $external_time || null == $external_sign || null == $external_api_key) {
			$result->success == false;
			$result->errors[] = "UKMapi: Mangler data fra ekstern tjeneste!";
		}

		$originalSigner = new UKMNorge\APIBundle\Util\Signer($external_api_key);
		$serviceSigner = new UKMNorge\APIBundle\Util\Signer($service_sys_key);

		// VALIDATE EXTERNAL SERVICE
		$internalSign1 = $originalSigner->sign($external_time);

		// VALIDATE DATA PROVIDER
		$serviceData = $_POST;
		$hidden = array('api_key', 'sign1', 'sign2', 'externalTime', 'sys_key');
		foreach ($serviceData as $key => $val) {
			if(in_array($key, $hidden)) {
				unset($serviceData[$key]);
			}
		}

		$data = $internalSign1.$service_permission;
		$internalSign2 = $serviceSigner->sign($service_time, $data);
		#echo $internalSign2;

		if($internalSign2 == $_POST['sign2'] && $internalSign1 == $_POST['sign1']) {
			$tilgang = tilgang($external_api_key, $service_sys_key, $service_permission);
			if($tilgang->success === true) {
				$result->success = true;
			} 
			else {
				$result->success = false;
				$result->errors = $tilgang->errors;
				return $result;
			}
			// Sign the result
			$result->sign = $serviceSigner->responseSign($internalSign1, $service_time, get_object_vars($result));
			return $result;
		}
		else {
			$result->success = false;
			$result->errors[] = 'UKMapi: Signeringen er ikke godkjent.';
			$result->errors[] = 'Sign1: '.$internalSign1.' != ' .$external_sign;
			$result->errors[] = 'Sign2: '.$internalSign2.' != ' .$service_sign;
		}
		
		return $result;
	}
	catch(Exception $e) {
		$result = new stdClass();
		$result->success = false;
		$result->errors[] = 'UKMapi: Det oppsto en ukjent feil med feilmeldingen '.$e->getMessage();
	}
}

// TODO: Implementer
function timestampsOkay() {
	return true;
}