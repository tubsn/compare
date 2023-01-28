<?php

namespace app\importer;

use \flundr\cache\RequestCache;

class CleverpushAPI
{

	const API_BASE_URL = 'https://api.cleverpush.com';
	private $apiToken = CLEVERPUSH_APIKEY;
	public $channelID = CLEVERPUSH_CHANNELID;


	public function notification($id) {

		$data = $this->curl('/channel/' . $this->channelID. '/notification/' . $id);
		$notification = $data['notification'];
		$notification['stats'] = $this->notification_stats($id);

		return $notification;

	}

	public function notification_stats($id) {
		$data = $this->curl('/channel/' . $this->channelID. '/notification/' . $id . '/hourly-statistics');
		return $data['statistics'] ?? $data;
	}

	public function notifications($from = null, $to = null, $limit = 30, $status = null) {

		$parameters = null;
		$parameters .= '?startDate=' . strtotime($from . '00:00');
		$parameters .= '&endDate=' . strtotime($to. '23:59');
		$parameters .= '&limit=' . $limit;

		$data = $this->curl('/channel/' . $this->channelID. '/notifications' . $parameters);
		return $data['notifications'] ?? $data;
	}

	public function notifications_today() {

		$parameters = null;
		$parameters .= '?startDate=' . strtotime('today');
		$parameters .= '&endDate=' . strtotime('tomorrow');
		$parameters .= '&limit=100';

		//dd($parameters);

		$data = $this->curl('/channel/' . $this->channelID. '/notifications' . $parameters);
		return $data['notifications'] ?? $data;
	}

	public function channel_stats($from = 'first day of this month', $to = 'last day of this month') {

		$parameters = null;
		$parameters .= '?startDate=' . strtotime($from . '00:00');
		$parameters .= '&endDate=' . strtotime($to. '23:59');

		return $this->curl('/channel/' . $this->channelID. '/statistics' . $parameters);
	}

	public function channel_subscription_stats() {
		return $this->curl('/channel/' . $this->channelID. '/subscription-count');
	}

	public function channel_segments() {
		$data = $this->curl('/channel/' . $this->channelID. '/segments');
		return $data['segments'] ?? $data;
	}

	public function channels() {
		//dd($this->curl('/channels'));
	}


	private function curl($path, $data = null) {

		$url = CleverpushAPI::API_BASE_URL . $path;

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    'Authorization: ' . $this->apiToken,
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
