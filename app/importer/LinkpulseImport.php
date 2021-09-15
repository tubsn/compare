<?php

namespace app\importer;
use app\importer\LinkpulseAdapter;
use \flundr\cache\RequestCache;

class LinkpulseImport
{

	const API_BASE_URL = 'https://api5.linkpulse.com/v2.1/query';
	private $apiKey = LINKPULSE_APIKEY;
	private $apiSecret = LINKPULSE_SECRET;
	private $client = PORTAL;

	public function __construct() {
		$this->adapter = new LinkpulseAdapter();
	}

	public function client($client) {

		/*
		switch ($client) {
			case 'LR': $this->apiKey = LINKPULSE_APIKEY_LR; $this->apiSecret = LINKPULSE_SECRET_LR; $this->client = 'LR'; break;
			case 'MOZ': $this->apiKey = LINKPULSE_APIKEY_MOZ; $this->apiSecret = LINKPULSE_SECRET_MOZ; $this->client = 'MOZ'; break;
			case 'SWP': $this->apiKey = LINKPULSE_APIKEY_SWP; $this->apiSecret = LINKPULSE_SECRET_SWP; $this->client = 'SWP'; break;
			default: $this->apiKey = LINKPULSE_APIKEY_LR; $this->apiSecret = LINKPULSE_SECRET_LR; break;
		}
		*/

	}

	public function live() {

		$cacheExpireMinutes = 3;
		$cache = new RequestCache('lp-live-' . $this->client, $cacheExpireMinutes * 60);
		$cachedData = $cache->get();
		if ($cachedData) {return $cachedData;}

		$apiQuery = '?filter%5Brange%5D=today&field%5Bpageviews%5D=sum&aggregate=minute&page%5Boffset%5D=0&page%5Blimit%5D=2000&sort=minute';

		//$apiQueryUrl = 'https://api5.linkpulse.com/v2.1/query?filter%5Brange%5D=today&field%5Bpageviews%5D=sum&aggregate=hour&page%5Boffset%5D=0&page%5Blimit%5D=100&sort=-minute';

		$rawJson = $this->curl($apiQuery);

		$data = json_decode($rawJson, true);
		$data = $data['data'];

		$cache->save($data);

		return $data;

	}



	public function article_today($id) {

		$apiQuery = '?filter%5Brange%5D=today&filter%5Burl%5D=*' . $id . '*&field%5Bpageviews%5D=sum&field%5Bconverted_usercount%5D=sum&field%5Bsubscribers%5D=sum&aggregate=total&page%5Boffset%5D=0&page%5Blimit%5D=100&sort=-pageviews';

		$rawAPIData = $this->curl($apiQuery);
		$stats = $this->adapter->convert($rawAPIData);

		return $stats[0]; // Should be only one Hit

	}

	public function subscribers($id, $pubDate) {

		$pubDate = formatDate($pubDate,'Ymd');
		$today = date('Ymd') ;

		$timeframe = '?filter[from]=' . $pubDate . '&filter[to]=' . $today . 'T23%3A59%3A59'; // 23:59
		$filter = '&filter[url]=*' . $id . '*';
		$fields = '&field[subscribers]=sum';
		$suffix = '&aggregate=total';

		$fullQuery = $timeframe . $filter . $fields . $suffix;

		$rawAPIData = $this->curl($fullQuery);

		$stats = $this->adapter->convert($rawAPIData);

		$subscribers = $stats[0]['subscribers'];

		return $subscribers;

	}


	private function curl($path) {

		$url = LinkpulseImport::API_BASE_URL . $path;

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_USERPWD, $this->apiKey . ":" . $this->apiSecret);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

		$recievedData = curl_exec($ch);
		if ($recievedData === false) {
			dd(curl_error($ch));
		}

		//$statuscode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close ($ch);

		return $recievedData;

	}

}
