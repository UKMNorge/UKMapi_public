<?php

namespace UKMNorge\APIBundle\Util;
require_once('UKM/sql.class.php');
use SQL;

class Signer {

	private $sys_key;
	private $sys_secret;

	public function __construct($sys_key) {
		$this->sys_key = $sys_key;
		$this->sys_secret = $this->findSecret($sys_key);
	}
	
	public function sign($data) {
		$data = array_change_key_case($data, CASE_LOWER);
		ksort($data);
		#var_dump($params);
		if(isset($data['time'])) {
			$time = $data['time'];
			unset($data['time']);
		}
		else {
			$time = time();
		}
		$params = http_build_query($data);
		$params = $this->sys_key . $params . $time . $this->sys_secret;
		#var_dump($params);
		return hash('sha256', $params);
	}

	public function responseSign($sign, $time, $response) {
		$response = http_build_query($response);
		return hash('sha256', $sign . $time . $this->sys_secret . $response);
	}

	private function findSecret($api_key) {
		if($api_key == null) {
			#echo 'api_key er tom';
			return false;
		}

		$qry = new SQL("SELECT secret FROM API_Keys
						WHERE `api_key` = '#api_key'", array('api_key' => $api_key));
		$secret = $qry->run('field', 'secret');
		if($secret == false) {
			#echo 'Secret finnes ikke';
			return false;
		}
		return $secret;
	}
}