<?php

namespace app\importer;
use app\importer\RetrescoAdapter;

class RetrescoImport
{

	private $apiURL = 'https://lausitzer-rundschau-tms.rtrsupport.de/api/';

	function __construct() {

	}

	public function collect($id) {

		$adapter = new RetrescoAdapter();

		$topicPagesUrl = $this->apiURL . 'content/' . $id . '/topic-pages';
		$topicPageData = $this->curl($topicPagesUrl);
		$topicPages = $adapter->extract_topic_pages($topicPageData);

		$articleDataUrl = $this->apiURL . 'content/' . $id;
		$articleData = $this->curl($articleDataUrl);
		$article = $adapter->extract_article_data($articleData);

		$retrescoInfo = array_merge($article,$topicPages);

		return $retrescoInfo;

	}

	private function curl($url) {

		$username = RETRESCO_APIKEY;
		$password = RETRESCO_SECRET;

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
