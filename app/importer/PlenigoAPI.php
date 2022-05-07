<?php

namespace app\importer;

use \flundr\cache\RequestCache;

class PlenigoAPI
{

	const API_BASE_URL = 'https://api.plenigo.com/api/v3.0';
	private $plenigoToken = PLENIGO_TOKEN;
	private $client = PORTAL;

	public function __construct() {}

	public function orders($start, $end, $items = 100) {

		$apiQuery = '/orders/?startTime=' . $start . 'T00:00:00Z&endTime=' . $end . 'T23:59:59Z&size=' . $items;
		$data = $this->curl($apiQuery);
		return $data['items'];

	}

	public function order($id) {
		$data = $this->curl('/orders/' . $id);
		$additionalData = $this->curl('/orders/' . $id . '/additionalData');
		$data = array_merge($data, $additionalData);
		return $data;

	}

	public function subscription($id) {
		$data = $this->curl('/subscriptions/' . $id);
		return $data;
	}

	public function chain($id) {
		$data = $this->curl('/subscriptions/chain/' . $id);
		return $data;
	}


	public function subscriptions($id) {
		$data = $this->curl('/customers/' . $id . '/subscriptions');
		return $data;
	}

	public function customer($id) {
		$data = $this->curl('/customers/' . $id);
		return $data;
	}

	public function customer_additional_data($id) {
		$data = $this->curl('/customers/' . $id . '/additionalData');
		return $data;
	}


	private function curl($path, $data = null) {

		$url = PlenigoAPI::API_BASE_URL . $path;

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    'X-plenigo-token: ' . $this->plenigoToken,
		    'Content-Type: application/json'
		));

		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

		$recievedData = curl_exec($ch);
		if ($recievedData === false) {
			dd(curl_error($ch));
		}

		curl_close ($ch);

		return json_decode($recievedData, true);

	}

}
