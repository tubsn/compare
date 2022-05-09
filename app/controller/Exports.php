<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\auth\Auth;
use flundr\utility\Session;

class Exports extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
		$this->view('CSV');
		$this->models('Articles,Analytics,Conversions,Campaigns,ArticleKPIs,DailyKPIs,Plenigo,Orders,Linkpulse');
	}

	public function articles() {
		$viewData['articles'] = $this->Articles->list_all();
		$this->view->title = PORTAL . '-Artikel-'.date("dmY").'.csv';
		$this->view->render('export/excel-articles', $viewData);
	}

	public function conversions() {

		$sessionFrom = Session::get('from') ?? '0000-00-00';
		$sessionTo = Session::get('to') ?? '2099-01-01';

		Session::set('from','0000-00-00');
		Session::set('to', '2099-01-01');

		$viewData['conversions'] = $this->Conversions->list();

		//dd($viewData['conversions']);

		Session::set('from', $sessionFrom);
		Session::set('to', $sessionTo);

		$this->view->title = PORTAL . '-Conversions-'.date("dmY").'.csv';
		$this->view->export($viewData['conversions']);

	}

	public function KPIs() {

		$sessionFrom = Session::get('from') ?? '0000-00-00';
		$sessionTo = Session::get('to') ?? '2099-01-01';

		Session::set('from','0000-00-00');
		Session::set('to', '2099-01-01');

		$kpis = $this->DailyKPIs->list();

		Session::set('from', $sessionFrom);
		Session::set('to', $sessionTo);

		$this->view->title = PORTAL . '-KPIs-'.date("dmY").'.csv';
		$this->view->export($kpis);

	}

	public function campaigns() {

		$sessionFrom = Session::get('from') ?? '0000-00-00';
		$sessionTo = Session::get('to') ?? '2099-01-01';

		Session::set('from','0000-00-00');
		Session::set('to', '2099-01-01');

		$campaigns = $this->Campaigns->list();

		Session::set('from', $sessionFrom);
		Session::set('to', $sessionTo);

		$this->view->title = PORTAL . '-UTM-Kampagnen-'.date("dmY").'.csv';
		$this->view->export($campaigns);

	}

	public function value_articles() {

		$sessionFrom = Session::get('from') ?? '0000-00-00';
		$sessionTo = Session::get('to') ?? '2099-01-01';

		Session::set('from','0000-00-00');
		Session::set('to', '2099-01-01');

		$artikel = $this->Articles->value_articles();

		Session::set('from', $sessionFrom);
		Session::set('to', $sessionTo);

		$this->view->title = 'Wertschoepfende-Artikel.csv';
		$this->view->export($artikel);

	}

	public function yesterday_stats() {

		$from = date('Y-m-d', strtotime('yesterday'));
		$to = date('Y-m-d', strtotime('yesterday'));

		$this->DailyKPIs->from = $from;
		$this->DailyKPIs->to = $to;

		$this->Orders->from = $from;
		$this->Orders->to = $to;

		$stats = $this->DailyKPIs->stats();
		$stats['orders'] = $this->Orders->count();

		header('Access-Control-Allow-Origin: *');
		$this->view->json($stats);

	}

	public function daily_stats() {
		$viewData['articles'] = $this->ArticleKPIs->with_article_data();
		$this->view->title = 'LRO-Daily-'.date("dmY").'.csv';
		$this->view->render('export/excel-dailystats', $viewData);
	}

	public function full_json() {
		$articles = $this->Articles->list_all();
		$this->view->json($articles);
	}

	public function ga_campaigns($days = 30) {
		$this->view('DefaultLayout');
		$data = $this->Analytics->utm_campaigns($days);

		// Save into Campaigns DB
		/*
		foreach ($data as $order) {
			$campaign['order_id'] = $order['Transactionid'];
			$campaign['ga_date'] =  date('Y-m-d', strtotime($order['Date']));
			$campaign['utm_source'] =  $order['Source'];
			$campaign['utm_medium'] =  $order['Medium'];
			$campaign['utm_campaign'] =  $order['Campaign'];
			$this->Campaigns->create_or_update($campaign);
		}*/

		$data = array_map(function ($set) {
			if (isset($set['Transactionid'])) {
				$set['Transactionid'] = '<a href="/orders/'.$set['Transactionid'].'">'.$set['Transactionid'].'</a>';
				unset($set['Transactions']);
			} return $set; }, $data);

		$grouped = $this->Analytics->utm_campaigns($days, true);

		$this->view->data = $data;
		$this->view->days = $days;
		$this->view->grouped = $grouped;
		$this->view->info = 'UTM Kampagnen der letzten ' . $days . ' Tage (ohne Sampling)';
		$this->view->render('export/utm-campaigns');
	}



	public function ressort_stats() {

		$from = date('Y-m-d', strtotime('-7days'));
		$to = date('Y-m-d', strtotime('today'));

		$this->Articles->from = $from;
		$this->Articles->to = $to;

		$stats = $this->Articles->stats_grouped_by($column = 'ressort', $order = 'conversions DESC, ressort ASC');

		if (empty($stats)) {
			$this->view->json($stats);
			die;
		}

		$keys = array_keys($stats);
		$stats = array_values($stats);

		foreach ($keys as $key => $ressort) {
			$stats[$key]['ressort'] = $ressort;
		}

		$this->view->json($stats);

	}

	public function linkpulse_halftime() {

		$baseStats = $this->Linkpulse->ressort_stats_cache();
		$baseStats = $this->average_data($baseStats, 34);
		ksort($baseStats);

		$this->view->title = 'Linkpulse-Halbjahreszahlen.csv';
		$this->view->export($baseStats);

	}

	public function linkpulse_current() {

		$from = Session::get('from') ?? date('Y-m-d', strtotime('yesterday -6days'));
		$to = Session::get('to') ?? date('Y-m-d', strtotime('yesterday'));
		$weeks = $this->calculate_weeks($from, $to);

		$currentStats = $this->Linkpulse->ressort_stats($from, $to);
		$currentStats = $this->average_data($currentStats, $weeks);
		ksort($currentStats);

		$output_with_ressort = [];
		$index = 0;
		foreach ($currentStats as $ressort => $data) {
			$output_with_ressort[$index]['ressort'] = $ressort;
			foreach ($data as $key => $void) {
				$output_with_ressort[$index][$key] = $data[$key];
			}
			$index++;
		}
		$currentStats = $output_with_ressort;

		$this->view->title = 'Linkpulse-Aktuellezahlen.csv';
		$this->view->export($currentStats);

	}

	/* Used By Linkpulse Export */

	private function calculate_weeks($from, $to) {

		$origin = date_create($from);
		$target = date_create($to);
		$interval = date_diff($origin, $target);

		return round($interval->format('%a') / 7);

	}

	/* Used By Linkpulse Export */

	private function average_data($data, $avgBase = 34) {

		return array_map(function($set) use ($avgBase) {
			$set['pageviews'] = round($set['pageviews'] / $avgBase,2);
			$set['subscriberviews'] = round($set['subscriberviews'] / $avgBase,2);
			$set['conversions'] = round($set['conversions'] / $avgBase,2);
			$set['mediatime'] = round($set['mediatime'] / $avgBase,2);
			$set['avgmediatime'] = round($set['avgmediatime'],2);
			return $set;
		}, $data);

	}



}
