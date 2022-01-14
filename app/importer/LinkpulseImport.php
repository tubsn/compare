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

	public function live_subs() {

		$cacheExpireMinutes = 3;
		$cache = new RequestCache('lp-live-subs' . $this->client, $cacheExpireMinutes * 60);
		$cachedData = $cache->get();
		if ($cachedData) {return $cachedData;}

		$apiQuery = '?filter%5Brange%5D=today&field%5Bsubscribers%5D=sum&aggregate=minute&page%5Boffset%5D=0&page%5Blimit%5D=2000&sort=minute';

		$rawJson = $this->curl($apiQuery);

		$data = json_decode($rawJson, true);
		$data = $data['data'];

		$cache->save($data);

		return $data;

	}


	public function active_users() {

		$apiQuery = '?filter%5Brange%5D=1min&field%5Bactiveusers%5D=avg&aggregate=total&page%5Boffset%5D=0&page%5Blimit%5D=100&sort=-pageviews';
		$rawJson = $this->curl($apiQuery);
		$data = json_decode($rawJson, true);
		$users = $data['data'][0]['attributes']['activeusers'] ?? 0;
		return $users;

	}


	public function article_today($id) {

		$apiQuery = '?filter%5Brange%5D=today&filter%5Burl%5D=*' . $id . '*&field%5Bpageviews%5D=sum&field%5Bconverted_usercount%5D=sum&field%5Bsubscribers%5D=sum&aggregate=total&page%5Boffset%5D=0&page%5Blimit%5D=100&sort=-pageviews';

		$rawAPIData = $this->curl($apiQuery);
		$stats = $this->adapter->convert($rawAPIData);
		$stats = $stats[0];

		// Remove null values
		foreach ($stats as $key => $value) {
			if (is_null($value)) {
				unset($stats[$key]);
			}
		}

		return $stats; // Should be only one Hit

	}

	public function articles_today() {

		$apiQuery = '?filter%5Brange%5D=today&filter%5Bpagetype%5D=article&field%5Bsection%5D=one&field%5Bpageviews%5D=sum&field%5Bviewtime%5D=median%26listcount&field%5Binscreencnt%5D=sum&field%5Bconverted_usercount%5D=sum&field%5Bsubscribers%5D=sum&field%5Burl%5D=one&aggregate=section%2Curl&page%5Boffset%5D=0&page%5Blimit%5D=10&sort=-pageviews&include=url';

		$rawAPIData = $this->curl($apiQuery);
		return $this->adapter->convert($rawAPIData);

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

	public function subscribers_grouped_by_date($from, $to) {

		$from = formatDate($from,'Ymd');
		$to = formatDate($to,'Ymd');

		$timeframe = '?filter[from]=' . $from . '&filter[to]=' . $to . 'T23%3A59%3A59'; // 23:59
		$fields = '&field[subscribers]=sum';
		$suffix = '&aggregate=day&sort=-day&page[limit]=1000';

		$fullQuery = $timeframe . $fields . $suffix;

		$rawAPIData = $this->curl($fullQuery);
		$stats = $this->adapter->convert($rawAPIData);

		if (empty($stats)) {return null;}

		return array_sum_grouped_by('subscribers', 'date', $stats);

	}


	public function ressort_stats($from = null, $to = null) {

		if (is_null($from)) {$from = date('Ymd');}
		else {$from = formatDate($from,'Ymd');}

		if (is_null($to)) {$to = date('Ymd');}
		else {$to = formatDate($to,'Ymd');}

		if (PORTAL == 'LR') {$domain = 'lr-online.de';}
		if (PORTAL == 'MOZ') {$domain = 'moz.de';}
		if (PORTAL == 'SWP') {$domain = 'swp.de';}

		$timeframe = '?filter[from]=' . $from . '&filter[to]=' . $to . 'T23%3A59%3A59'; // 23:59
		$query = '&filter%5Bsection%5D=!%2F%7C!%3Cempty%3E&filter%5Bdomain%5D=' . $domain . '&field%5Bsection%5D=one&field%5Bpageviews%5D=sum&field%5Bviewtime%5D=median%26listcount&field%5Binscreencnt%5D=sum&field%5Bviewtimesum%5D=sum&field%5Bconverted_usercount%5D=sum&field%5Bsubscribers%5D=sum&aggregate=section&page%5Boffset%5D=0&page%5Blimit%5D=100&sort=-pageviews';

		$apiQuery = $timeframe . $query;
		$rawAPIData = $this->curl($apiQuery);

		//dd($rawAPIData);

		return $this->adapter->convert($rawAPIData);

	}

	private function curl($path) {

		$url = LinkpulseImport::API_BASE_URL . $path;

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_USERPWD, $this->apiKey . ":" . $this->apiSecret);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 300);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

		$recievedData = curl_exec($ch);
		if ($recievedData === false) {
			dd(curl_error($ch));
		}

		curl_close ($ch);

		return $recievedData;

	}

}
