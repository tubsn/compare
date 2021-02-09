<?php

namespace app\models;

class Analytics
{

	private $serviceAccountKeyfile = CONFIGPATH .'/analytics/lr-admanager-1534602526065-ba07dbe6f23a.json';
	private $analyticsConnection;
	private $analyticsData;

	public $profileViewID = '2001197'; // LR-Online
	public $metrics;
	public $from;
	public $to;
	public $dimensions;
	public $sort;
	public $filters;
	public $maxResults;

	function __construct() {
		$this->analyticsConnection = $this->initializeAnalytics();
	}


	public function byArticleID($articleID, $from = '30daysAgo', $to = 'today') {

		$articleID = htmlspecialchars($articleID, ENT_QUOTES, 'UTF-8');

		if (strlen($articleID) != 8) {
			return false;
		}

		$this->metrics = 'ga:pageViews,ga:sessions,ga:itemQuantity';
		$this->from = $from;
		$this->to = $to;
		$this->dimensions = 'ga:date';
		$this->sort = '-ga:date';
		$this->filters = 'ga:pagePath=@' . $articleID . ';ga:pageviews>0';
		$this->maxResults = '365';


		$this->makeRequest();

		foreach ($this->totals() as $key => $value) {
			$stats[$this->stripGaString($key)] = $value;
		}

		return [
			'stats' => $stats,
			'details' => $this->dataMergedWithHeaders(),
		];
	}

	public function conversions_by_article_id($articleID, $from = '30daysAgo', $to = 'today') {

		$articleID = htmlspecialchars($articleID, ENT_QUOTES, 'UTF-8');
		if (strlen($articleID) != 8) {return false;}

		$this->metrics = 'ga:itemQuantity';
		$this->from = $from;
		$this->to = $to;
		$this->dimensions = 'ga:transactionId,ga:source,ga:city,ga:sessionCount';
		$this->sort = 'ga:transactionId';
		$this->filters = 'ga:pagePath=@' . $articleID . ';ga:itemQuantity>0';
		$this->maxResults = '365';

		$this->makeRequest();

		return $this->dataMergedWithHeaders();

	}



	public function analyze() {

		$this->metrics = 'ga:pageViews,ga:sessions';
		$this->from = '365daysAgo';
		$this->to = '335daysAgo';
		$this->dimensions = 'ga:hostName,ga:pagePath';
		$this->sort = '-ga:pageViews';
		$this->filters = 'ga:pagePath=@aid-;ga:pagePathLevel2==/luckau/';
		$this->maxResults = '100';

		$this->makeRequest();

		foreach ($this->totals() as $key => $value) {
			$stats[$this->stripGaString($key)] = $value;
		}

		return [
			'stats' => $stats,
			'details' => $this->dataMergedWithHeaders(),
		];
	}

	private function headers() {
		return $this->analyticsData->getColumnHeaders();
	}

	private function results() {
		return $this->analyticsData->getRows();
	}

	private function totals() {
		return $this->analyticsData->getTotalsForAllResults();
	}

	private function stripGaString($string) {
		return ucfirst(strtolower(substr($string,3)));
	}

	private function dataMergedWithHeaders() {

		$headers = $this->analyticsData->getColumnHeaders();
		$results = $this->analyticsData->getRows();

		if (!$results) {
			return [];
		}

		foreach ($results as $index => $result) {
			foreach ($result as $key => $value) {
				$results[$index][$this->stripGaString($headers[$key]['name'])] = $value;
				unset($results[$index][$key]);
			}
		}

		return $results;
	}

	// Google Stuff
	private function initializeAnalytics() {
		$client = new \Google_Client();
		$client->setApplicationName("LR-Online AdManager Analytics");
		$client->setAuthConfig($this->serviceAccountKeyfile);
		$client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
		$analytics = new \Google_Service_Analytics($client);
		return $analytics;
	}


	private function makeRequest() {

		$data = $this->analyticsConnection->data_ga->get(
			'ga:' . $this->profileViewID,
			$this->from,
			$this->to,
			$this->metrics,
			[
				'dimensions' => $this->dimensions,
				'sort' => $this->sort,
				'filters' => $this->filters,
				'max-results' => $this->maxResults
			]
		);

		$this->analyticsData = $data;

	}

}
