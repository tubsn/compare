<?php

namespace app\importer;
use \flundr\cache\RequestCache;
use flundr\utility\Log;


class KilkayaAPI
{

	const API_BASE_URL = 'https://dataapi.kilkaya.com/api/';
	private $bearerToken = KILKAYA_APIKEY;
	private $queryRuns = 0;
	private $maxQueryRuns = 15;

	public $query = null;
	public $forceResponseWithIndex = false;
	public $response;
	public $responseRaw;
	public $responseMessage;
	public $responseMeta;

	public $from = null;
	public $to = null;
	public $exactFrom = null;
	public $exactTo = null;
	public $schema = 'pageview';
	public $columns = [];
	public $filters = [];
	public $sortBy = null;
	public $order = 'desc';
	public $limit = 100;

	private $KPImap = [
		'pageview' => 'pageviews',
		'session' => 'sessions',
		'conversion' => 'conversions',
		'subscriber' => 'subscriberviews',
		'image' => 'image',
		'description' => 'description',
		'published' => 'pubdate',
		'section' => 'ressort',
		'title' => 'title',
		'url' => 'url',
		'inscreencnt' => 'mediatime',
		'viewTime' => 'mediatime',
		'viewtimeavgmed' => 'avgmediatime',
		'viewTimeMedian' => 'avgmediatime',
		'_minute' => 'minute',
		'_day' => 'day',
	];

	public function __construct() {
		$this->from = date('Y-m-d');
		$this->to = date('Y-m-d');
	}

	public function run_query($query = null) {
		$this->handle_query_options($query);
		$this->call_query_endpoint($this->query);
		return $this->response;
	}



	public function cache() {

		$cache = new RequestCache('test', 1 * 60);
		$cachedData = $cache->get();
		if ($cachedData) {return $cachedData;}
		$cache->save($data);

	}

	private function handle_query_options($string) {

		if (is_null($string)) {
			if (is_null($this->query)) {
				$this->build_query();
			}
			return true;
		}

		json_decode($string);
		if (json_last_error() === JSON_ERROR_NONE) {
			$this->query = $string;
			return true;
		}

		throw new \Exception("KilkayaAPI: Invalid Request Query ", 400);

	}

	private function build_query() {

		$sortBy = $this->sortBy ?? $this->columns[0];
		$order = $this->order;
		$order = strtolower($order);

		$options = [

			'datefrom' => $this->from . 'T00:00:00',
			'dateto' => $this->to . 'T23:59:59',

			'schemaname' => $this->schema,
			'columns' => $this->columns,
			'filters' => $this->filters,

			'sortby' => [$sortBy],
			'sortorder' => [$order],

			'limit' => $this->limit,

		];

		if (!is_null($this->exactFrom)) {$options['datefrom'] = $this->exactFrom;}
		if (!is_null($this->exactTo)) {$options['dateto'] = $this->exactTo;}

		$options = json_encode($options);

		$this->query = $options;
		return $options;

	}


	private function transform_response_element($entry) {

		$type = $entry['type'];
		$id = $entry['id'];
		$attributes = $entry['attributes'];

		$out = [];
		foreach ($attributes as $key => $value) {
			if ($key == '_id') {continue;}
			if ($key == '_day') {$value = substr($value,0,10);} // Removes unneccessary minutes and seconds
			$out[$this->map_kpi($key)] = $value;
		}

		return $out;

	}

	public function map_kpi($externalName) {
		return $this->KPImap[$externalName] ?? $externalName;
	}

	public function call_query_endpoint($options) {
		$this->queryRuns++;
		$curlData = $this->curl('query', $options);
		$this->handle_response($curlData);
	}

	public function call_image_endpoint($imageURLs) {

		$options['urls'] = $imageURLs;
		$options = json_encode($imageURLs);
		$curlData = $this->curl('images', $options);
		if (empty($curlData)) {return [];}
		return $curlData;

	}

	private function handle_response($curlData) {

		$this->responseRaw = $curlData;
		$this->responseMessage = $curlData['message'] ?? null;
		$this->responseErrors = $curlData['errors'] ?? null;
		$this->responseMeta = $curlData['meta'] ?? null;

		$this->handle_errors();
		$this->handle_messages();
		$this->handle_queued_query();

		if (!isset($curlData['data'])) {
			throw new \Exception("No Response Data", 404);

		}

		if (isset($curlData['data'])) {
			$response = array_map([$this, 'transform_response_element'], $curlData['data']);
		}

		if (!$this->forceResponseWithIndex && is_array($response) && count($response) == 1) {
			$this->response = $response[0];	return;
		}

		$this->response = $response;

	}

	public function handle_errors() {

		if ($this->responseErrors) {
			dd($this->responseRaw);
		}

	}

	public function handle_messages() {

		if ($this->responseMessage) {
			Log::error('KilkayaAPI: ' . $this->responseMessage);
		}

	}

	private function handle_queued_query() {

		if (!isset($this->responseMeta['delayed'])) {return;}
		if ($this->responseMeta['delayed'] == 1) {

			if ($this->queryRuns > $this->maxQueryRuns) {
				throw new \Exception("Maximum query repetitions reached", 429);
			}

			$timeout = 1000 * 200;
			//echo '<br>Query No. ' . $this->queryRuns . ' - Sleeping for ' . $timeout / 1000 . ' milliseconds';
			usleep($timeout);
			$this->call_query_endpoint($this->query);

		}
	}

	public function extract_id($url) {
		// Regex search for the ID = -8Digits.html
		$searchPattern = "/-(\d{8}).html/";
		preg_match($searchPattern, $url, $matches);
		return $matches[1]; // First Match should be the ID
	}

	private function curl($path, $jsonData) {

		$url = KilkayaAPI::API_BASE_URL . $path;
		$authorization = 'Authorization: Bearer ' . $this->bearerToken;

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization]);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $jsonData);
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
