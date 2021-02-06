<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\auth\Auth;

class Exports extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
		$this->view('CSV');
		$this->models('Articles,Stats');
	}

	public function full() {
		$viewData['articles'] = $this->Articles->list_all();
		$this->view->title = 'LRO-Artikel'.date("dmY").'.csv';
		$this->view->render('export/excel-full', $viewData);
	}

	public function daily() {
		$viewData['articles'] = $this->Stats->with_article_data();
		$this->view->title = 'LRO-Daily'.date("dmY").'.csv';
		$this->view->render('export/excel-dailystats', $viewData);
	}

	public function full_json() {
		$articles = $this->Articles->list_all();
		$this->view->json($articles);
	}

	public function ressort_stats() {

		$from = date('Y-m-d', strtotime('-7days'));
		$to = date('Y-m-d', strtotime('today'));

		$this->Articles->from = $from;
		$this->Articles->to = $to;

		$stats = $this->Articles->stats_grouped_by($column = 'ressort', $order = 'conversions DESC, ressort ASC');

		$keys = array_keys($stats);
		$stats = array_values($stats);

		foreach ($keys as $key => $ressort) {
			$stats[$key]['ressort'] = $ressort;
		}

		//dd($stats);

		$this->view->json($stats);

	}

}
