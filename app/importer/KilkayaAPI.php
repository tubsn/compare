<?php

namespace app\importer;
use \flundr\cache\RequestCache;

class KilkayaAPI
{

	const API_BASE_URL = 'https://dataapi.kilkaya.com/api/';
	private $bearerToken = KILKAYA_APIKEY;
	private $jsonQuery;
	private $queryRuns = 0;
	private $maxQueryRuns = 15;

	public $response;
	public $responseRaw;
	public $responseMessage;
	public $responseMeta;

	public $from;
	public $to;
	public $schema = 'pageview';
	public $columns = [];
	public $filters = [];
	public $limit = 100;

	private $KPImap = [
		'pageview' => 'pageviews',
		'session' => 'sessions',
		'conversion' => 'conversions',
		'subscriber' => 'subscribers',
		'image' => 'image',
		'description' => 'description',
		'published' => 'pubdate',
		'section' => 'ressort',
		'title' => 'title',
		'url' => 'url',
		'inscreencnt' => 'mediatime',
		'viewtimeavgmed' => 'avgmediatime',
		'_minute' => 'minute',
	];

	public function __construct() {}

	public function run_query() {
		$this->build_query();
		$this->call_query_endpoint($this->options);
		return $this->response;
	}

	public function run_direct($query) {
		$this->options = $query;
		$this->call_query_endpoint($this->options);
		return $this->response;
	}


	public function cache() {

		$cache = new RequestCache('test', 1 * 60);
		$cachedData = $cache->get();
		if ($cachedData) {return $cachedData;}
		$cache->save($data);

	}

	public function from($time) {

		return date('c', strtotime($time));

		//dd(strtotime($time));

		return date('Y-m-d', strtotime($time));

	}


	private function build_query() {

		$options = [

			'datefrom' => $this->from . 'T00:00:00+00:00',
			'dateto' => $this->to . 'T23:59:59+00:00',

			'schemaname' => $this->schema,
			'columns' => $this->columns,
			'filters' => $this->filters,

			'resultsortby' => [$this->columns[0]],
			'resultsortorder' => ['desc'],
			'limit' => 100,

		];

		$options = json_encode($options);

		$this->options = $options;
		return $options;

	}


	private function transform_response_element($entry) {

		$type = $entry['type'];
		$id = $entry['id'];
		$attributes = $entry['attributes'];

		$out = [];
		foreach ($attributes as $key => $value) {
			if ($key == '_id') {continue;}
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


	private function handle_response($curlData) {

		$this->responseRaw = $curlData;
		$this->responseMessage = $curlData['message'] ?? null;
		$this->responseMeta = $curlData['meta'] ?? null;

		$this->handle_errors();
		$this->handle_queued_query();

		if (isset($curlData['data'])) {
			$this->response = array_map([$this, 'transform_response_element'], $curlData['data']);
		}

	}

	public function handle_errors() {

		if ($this->responseMessage) {
			echo 'Kilkaya-API: ' . $this->responseMessage . '<br>';
			//dump($this->responseRaw);
		}

	}

	private function handle_queued_query() {

		if (!isset($this->responseMeta['delayed'])) {return;}
		if ($this->responseMeta['delayed'] == 1) {

			if ($this->queryRuns > $this->maxQueryRuns) {echo '<br>Maximum query repetitions reached'; return;}
			$timeout = $this->responseMeta['timeout'] * 1000 + 5000;
			echo '<br>Query No. ' . $this->queryRuns . ' - Sleeping for ' . $timeout / 1000 . ' milliseconds';
			usleep($timeout);
			$this->call_query_endpoint($this->options);

		}
	}

	private function extract_id($url) {
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
