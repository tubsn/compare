<?php

namespace app\importer;

class PlenigoImport
{

	function __construct() {

	}

	public function order($id) {
		$data = $this->curl('https://api.plenigo.com/api/v3.0/orders/' . $id);
		$additionalData = $this->curl('https://api.plenigo.com/api/v3.0/orders/' . $id . '/additionalData');

		$data = array_merge($data, $additionalData);

		return $data;

	}


	public function subscription($id) {
		$data = $this->curl('https://api.plenigo.com/api/v3.0/subscriptions/' . $id);
		return $data;
	}

	public function chain($id) {
		$data = $this->curl('https://api.plenigo.com/api/v3.0/subscriptions/chain/' . $id);
		return $data;
	}


	public function subscriptions($id) {
		$data = $this->curl('https://api.plenigo.com/api/v3.0/customers/' . $id . '/subscriptions');
		return $data;
	}

	public function customer($id) {
		$data = $this->curl('https://api.plenigo.com/api/v3.0/customers/' . $id);
		return $data;
	}


	private function curl($url) {

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    'X-plenigo-token: ' . PLENIGO_TOKEN_LR,
		    'Content-Type: application/json'
		));

		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

		if (curl_exec($ch) === false) {
			dd(curl_error($ch));
		}

		$recievedData = curl_exec($ch);
		curl_close ($ch);

		return json_decode($recievedData, true);

	}




}
