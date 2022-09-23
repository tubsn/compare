<?php

namespace app\models;
use	\app\importer\KilkayaAPI;
use \flundr\cache\RequestCache;

class Kilkaya
{

	// Remember that the KilkayaAPI Class is automatically casting KPI Names!

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

	/*
	public function active_users() {

		$api = new KilkayaAPI();

		$to = date(DATE_ATOM);
		$to = substr($to,0,-6);

		$from = date(DATE_ATOM, strtotime('-1minute'));
		$from = substr($from,0,-6);

		$api->exactFrom = $from;
		$api->exactTo = $to;
		//$api->columns = ['pageview'];
		$api->columns = ['autouniqueusers', 'domain'];

		$api->run_query();

		$data = array_column($api->response, 'autouniqueusers', 'domain');

		switch (PORTAL) {
			case 'LR': $domain = 'lr-online.de';break;
			case 'SWP': $domain = 'swp.de';break;
			case 'MOZ': $domain = 'moz.de';break;
			default: $domain = 'lr-online.de';break;
		}

		return $data[$domain];

	}
	*/


	public function today($kpiName = 'pageview', $resolution = 3) {

		$api = new KilkayaAPI();

		$today = date('Y-m-d');

		$api->from = $today;
		$api->to = $today;
		$api->columns = [$kpiName, '_minute'];
		$api->sortBy = '_minute';
		$api->order = 'asc';
		$api->limit = 10000;

		$api->run_query();
		return $this->today_as_chart_data($api->response, $kpiName . 's' , $resolution);

	}

	private function today_as_chart_data($data, $kpiName = 'pageviews', $resolution = 3) {

		if ($kpiName == 'subscribers') {$kpiName = 'subscriberviews';}

		$values = null;
		$time = null;
		$counter = 0;

		$kpi = array_sum(array_column($data, $kpiName));

		foreach ($data as $moment) {

			$counter++;
			if ($resolution != 0) {
				if ($counter % $resolution != 0) {continue;}
			}

			$values .= $moment[$kpiName] . ',';
			$timestring = date('H:i', strtotime($moment['minute']));
			$time .= "'" . $timestring."'" . ',';

		}

		return [
			'values' => $values,
			'timestamps' => $time,
			$kpiName => $kpi,
		];

	}



	public function subscribers($id, $pubDate) {

		$api = new KilkayaAPI();
		$api->from = $pubDate;
		$api->to = date('Y-m-d', strtotime('today'));
		$api->columns = ['subscriber'];
		$api->filters = [ ['operator' => 'like', 'field' => 'url', 'value' => '*' . $id . '*'] ];

		$api->run_query();
		return $api->response['subscriberviews'] ?? null;
	}

	public function subscribers_grouped_by_date($from, $to) {

		$api = new KilkayaAPI();
		$api->forceResponseWithIndex = true;
		$api->from = $from;
		$api->to = $to;
		$api->columns = ['_day','subscriber'];

		$api->run_query();

		return array_column($api->response, 'subscriberviews', 'day');

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

	public function conversions($from, $to) {

		$api = new KilkayaAPI();
		$api->from = date('Y-m-d', strtotime($from));
		$api->to = date('Y-m-d', strtotime($to));
		$api->columns = ['conversion', '_day'];
		$api->sortBy = '_day';
		$api->order = 'asc';

		$api->run_query();
		return $api->response;
	}


	public function article($id, $pubdate) {

		$api = new KilkayaAPI();
		$api->from = date('Y-m-d', strtotime($pubdate));
		$api->to = date('Y-m-d');
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

		//$images = $api->call_image_endpoint($urls);
		//$images = array_column($images, 'image', 'url');

		foreach ($articles as $key => $article) {
			//$articles[$key]['image'] = $images[$article['url']] ?? '/styles/img/flundr/no-thumb.svg';
			$articles[$key]['image'] = 'https://dataapi.kilkaya.com/api/image/,?url=' . $article['url'];
			$articles[$key]['id'] = $api->extract_id($article['url']);
			$articles[$key]['avgmediatime'] = round($article['avgmediatime'],2);
		}

		return $articles;

	}

}
