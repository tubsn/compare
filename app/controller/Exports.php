<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\auth\Auth;
use flundr\utility\Session;

class Exports extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
		$this->view('CSV');
		$this->models('Articles,Analytics,Conversions,DailyKPIs,Plenigo,Orders');
	}

	public function articles() {
		$viewData['articles'] = $this->Articles->list_all();
		$this->view->title = 'LRO-Artikel-'.date("dmY").'.csv';
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

		$this->view->title = 'LRO-Conversions-'.date("dmY").'.csv';
		$this->view->export($viewData['conversions']);

	}


	public function daily_stats() {
		$viewData['articles'] = $this->DailyKPIs->with_article_data();
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

		$data = array_map(function ($set) { 
			if (isset($set['Transactionid'])) {
				$set['Transactionid'] = '<a href="/orders/'.$set['Transactionid'].'">'.$set['Transactionid'].'</a>';
				unset($set['Transactions']);
			} return $set; }, $data);

		$grouped = $this->Analytics->utm_campaigns($days, true);
		$this->view->data = $data;
		$this->view->grouped = $grouped;
		$this->view->info = 'UTM Kampagnen der letzten ' . $days . ' Tage (ohne Sampling)';
		$this->view->render('export/dump');
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


}
