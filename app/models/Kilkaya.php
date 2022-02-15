<?php

namespace app\models;
use	\app\importer\KilkayaAPI;
use \flundr\cache\RequestCache;

class Kilkaya
{

	function __construct() {

	}


	public function test() {

		$api = new KilkayaAPI();

		$api->from = '2022-01-01';
		$api->to = '2022-01-10';

		$api->columns = ['title', 'pageview', 'section'];
		$api->filters = [ ['operator' => 'like', 'field' => 'section', 'value' => 'cottbus'] ];

		$api->run_query();

		return $api->response;
	}


	public function active_users() {

		$api = new KilkayaAPI();

		$to = date(DATE_ATOM);
		$to = substr($to,0,-6);

		$from = date(DATE_ATOM, strtotime('-1minute'));
		$from = substr($from,0,-6);

		$api->exactFrom = $from;
		$api->exactTo = $to;
		$api->columns = ['pageview'];

		$api->run_query();
		return $api->response['pageviews'];

	}


	public function today() {

		$api = new KilkayaAPI();

		$query = '{
		  "datefrom": "2022-01-19T00:00:00",
		  "dateto": "2022-01-19T23:59:00",
		  "columns": [
		    "pageview",
		    "_minute"
		  ],
		  "filters": [],
		  "sortby": [
		    "_minute"
		  ],
		  "sortorder": [
		    "asc"
		  ],
		  "resultsortby": [
		    "_minute"
		  ],
		  "resultsortorder": [
		    "asc"
		  ],
		  "scoreweights": [],
		  "schemaname": "pageview",
		  "limit": 10000,
		  "skip": 5
	  	}';

		$api->run_query($query);
		return $api->response;

	}


	public function subscribers($id, $pubDate) {

		$api = new KilkayaAPI();
		$api->from = $pubDate;
		$api->to = date('Y-m-d', strtotime('today'));
		$api->columns = ['subscriber'];
		$api->filters = [ ['operator' => 'like', 'field' => 'url', 'value' => '*' . $id . '*'] ];

		$api->run_query();
		return $api->response['subscribers'] ?? null;
	}

	public function subscribers_grouped_by_date($from, $to) {

		$api = new KilkayaAPI();
		$api->forceResponseWithIndex = true;
		$api->from = $from;
		$api->to = $to;
		$api->columns = ['_day','subscriber'];

		$api->run_query();

		return array_column($api->response, 'subscribers', 'day');

	}

	public function article_today($id) {

		$api = new KilkayaAPI();
		$api->from = date('Y-m-d', strtotime('today'));
		$api->to = date('Y-m-d', strtotime('today'));
		$api->columns = ['pageview', 'subscriber', 'conversion'];
		$api->filters = [ ['operator' => 'like', 'field' => 'url', 'value' => '*' . $id . '*'] ];

		$api->run_query();
		return $api->response;
	}

	public function stats_today($id) {
		return $this->article_today($id);
	}


	public function articles_today() {

		$api = new KilkayaAPI();
		$api->columns = ['pageview', 'subscriber', 'conversion', 'title', 'section', 'publishtime', 'viewTime', 'viewTimeMedian', 'url'];
		$api->filters = [ ['operator' => 'like', 'field' => 'pagetype', 'value' => 'article'] ];
		$api->limit = 10;
		$api->run_query();

		$articles = $api->response;

		$urls = array_column($articles, 'url');
		$images = $api->call_image_endpoint($urls);
		$images = array_column($images, 'image', 'url');


		foreach ($articles as $key => $article) {
			$articles[$key]['image'] = $images[$article['url']] ?? '';
			//$articles[$key]['image'] = 'https://dataapi.kilkaya.com/api/image/,?url=' . $article['url'];			
			$articles[$key]['id'] = $api->extract_id($article['url']);
			$articles[$key]['avgmediatime'] = round($article['avgmediatime'],2);
		}

		return $articles;

	}

}
