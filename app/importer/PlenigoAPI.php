<?php

namespace app\importer;

class PlenigoAPI
{

	const API_BASE_URL = 'https://api.plenigo.com/api/v3.0';
	private $plenigoToken = PLENIGO_TOKEN_LR;

	public function __construct() {
	}

	public function client($client) {

		switch ($client) {
			case 'LR': $this->plenigoToken = PLENIGO_TOKEN_LR; break;
			case 'MOZ': $this->plenigoToken = PLENIGO_TOKEN_MOZ; break;
			case 'SWP': $this->plenigoToken = PLENIGO_TOKEN_SWP; break;
			default: $this->plenigoToken = PLENIGO_TOKEN_LR; break;
		}

	}

	public function orders($start, $end, $items = 100) {

		$data = $this->curl('/orders/?startTime=' . $start . 'T00:00:00Z&endTime=' . $end . 'T23:59:59Z&size=' . $items);
		return $data['items'];

	}

	public function order($id) {
		$data = $this->curl('/orders/' . $id);
		$additionalData = $this->curl('/orders/' . $id . '/additionalData');

		/*
		//$data = $this->curl('https://api.plenigo.com/api/v3.0/orders/?size=10');
		$data = $this->curl('https://api.plenigo.com/api/v3.0/orders/?startTime=2021-02-23T00:00:00Z&endTime=2021-03-24T00:00:00Z&size=10');
		dd(json_decode($data,1));
		*/

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

		if (curl_exec($ch) === false) {
			dd(curl_error($ch));
		}

		$recievedData = curl_exec($ch);
		curl_close ($ch);

		return json_decode($recievedData, true);

	}

}