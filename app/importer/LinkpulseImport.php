<?php

namespace app\importer;
use app\importer\LinkpulseAdapter;

class LinkpulseImport
{

	function __construct() {

		$this->adapter = new LinkpulseAdapter();

	}

	public function article_today($id) {

		$apiQueryUrl = 'https://api5.linkpulse.com/v2.1/query?filter%5Brange%5D=today&filter%5Burl%5D=*' . $id . '*&field%5Bpageviews%5D=sum&field%5Bconverted_usercount%5D=sum&field%5Bsubscribers%5D=sum&aggregate=total&page%5Boffset%5D=0&page%5Blimit%5D=100&sort=-pageviews';

		$rawAPIData = $this->curl($apiQueryUrl);
		$stats = $this->adapter->convert($rawAPIData);

		return $stats[0]; // Should be only on Hit

	}

	public function subscribers($id, $pubDate) {

		$pubDate = formatDate($pubDate,'Ymd');
		$today = date('Ymd') ;

		$apiURL = 'https://api5.linkpulse.com/v2.1/query';

		$timeframe = '?filter[from]=' . $pubDate . '&filter[to]=' . $today . 'T23%3A59%3A59'; // 23:59
		$filter = '&filter[url]=*' . $id . '*';
		$fields = '&field[subscribers]=sum';
		$suffix = '&aggregate=total';

		$fullQueryUrl = $apiURL . $timeframe . $filter . $fields . $suffix;

		$rawAPIData = $this->curl($fullQueryUrl);

		$stats = $this->adapter->convert($rawAPIData);

		$subscribers = $stats[0]['subscribers'];

		return $subscribers;

	}


	private function curl($url) {

		$username = LINKPULSE_APIKEY;
		$password = LINKPULSE_SECRET;

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_USERPWD, $username . ":" . $password);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

		if (curl_exec($ch) === false) {
			dd(curl_error($ch));
		}

		$recievedData = curl_exec($ch);
		curl_close ($ch);

		return $recievedData;

	}

}
