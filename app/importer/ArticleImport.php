<?php

namespace app\importer;
use app\importer\RSS_Adapter;

class ArticleImport
{

	function __construct() {
		$this->portalURL = PORTAL_URL;
	}

	public function rss($url) {

		$rssData = $this->curl($url);
		if (empty($rssData)) {throw new \Exception('Import Error at: ' . $url);}

		$adapter = new RSS_Adapter;
		return $adapter->convert($rssData);

	}

	public function detail_rss($articleID) {

		$url = $this->portalURL . '/' . $articleID . '?_XML=rss';
		$curlData = $this->curl_with_redirect($url);

		$url = $curlData['url'];
		$rssData = $curlData['data'];

		$adapter = new RSS_Adapter;
		return $adapter->convert_news_markup($rssData, $url);

	}

	private function curl($url) {

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

		$recievedData = curl_exec($ch);
		if ($recievedData === false) {
			dd(curl_error($ch));
		}

		curl_close ($ch);

		return $recievedData;

	}

	private function curl_with_redirect($url) {

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

		$recievedData = curl_exec($ch);
		if ($recievedData === false) {
			dd(curl_error($ch));
		}

		$lastUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		$responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

		curl_close ($ch);

		if ($responseCode == 404) {
			throw new \Exception("Artikel nicht gefunden oder kann nicht importiert werden", 404);
		}

		return ['data' => $recievedData, 'url' => $lastUrl];

	}


}
