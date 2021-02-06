<?php

namespace app\importer;
use app\importer\LR_RSS_Adapter;

class ArticleImport
{

	function __construct() {

	}

	public function rss($url) {

		$xml = simplexml_load_string($this->curl($url));
		$adapter = new LR_RSS_Adapter;
		return $adapter->convert($xml);

	}


	private function curl($url) {

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
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
