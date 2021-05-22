<?php

namespace app\importer;
//use app\importer\LinkpulseAdapter;

class AnalyticsImport
{

	private $serviceAccountKeyfile = ANALYTICS_SERVICE_ACCOUNT_FILE;
	private $analyticsConnection;
	private $analyticsData;

	public $profileViewID = ANALYTICS_PROFILE_ID;
	public $metrics;
	public $from;
	public $to;
	public $dimensions;
	public $sort;
	public $filters;
	public $maxResults;

	function __construct() {
		$this->analyticsConnection = $this->initialize_analytics();
	}


	public function fetch() {
		$this->make_API_request();
		return $this->dimension_details();
	}

	public function metric_totals() {

		$gaTotals = $this->analyticsData->getTotalsForAllResults();

		$output = [];
		foreach ($gaTotals as $key => $value) {
			$output[$this->strip_ga_string($key)] = $value;
		}

		return $output;

	}

	public function dimension_details() {
		return $this->data_merged_with_headers();
	}



	private function data_merged_with_headers() {

		$headers = $this->analyticsData->getColumnHeaders();
		$results = $this->analyticsData->getRows();

		if (!$results) {
			return [];
		}

		foreach ($results as $index => $result) {
			foreach ($result as $key => $value) {
				$results[$index][$this->strip_ga_string($headers[$key]['name'])] = $value;
				unset($results[$index][$key]);
			}
		}

		return $results;
	}

	private function strip_ga_string($string) {
		return ucfirst(strtolower(substr($string,3)));
	}


	// Google API Stuff
	private function initialize_analytics() {
		$client = new \Google_Client();
		$client->setApplicationName("Artikel-Reports-Analytics");
		$client->setAuthConfig($this->serviceAccountKeyfile);
		$client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
		$analytics = new \Google_Service_Analytics($client);
		return $analytics;
	}


	private function make_API_request() {

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
