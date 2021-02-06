<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\auth\Auth;

class Analytics extends Controller {

	public function __construct() {

		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Analytics,Articles,Stats');
	}

	public function warmup_daterange() {

		if (isset($_GET['from']) && isset($_GET['from'])) {
			$from = $_GET['from'];
			$to = $_GET['to'];
		}
		else {
			$from = date('Y-m-d', strtotime('today -3 days'));
			$to = date('Y-m-d', strtotime('today -1 day'));
		}

		$articles = $this->Articles->by_date_range($from, $to);

		// dd($articles);

		foreach ($articles as $article) {
			$id = $article['id'];
			$pubDate = formatDate($article['pubdate'],'Y-m-d');
			$gaData = $this->Analytics->byArticleID($id, $pubDate);
			$this->save_article_stats($gaData, $id);
		}

		echo 'wenn dieser Text erscheint hats geklappt...<br/>';
		echo 'Processing-Time: <b>'.round((microtime(true)-APP_START)*1000,2) . '</b>ms';


	}

	public function warmup_weeks_ago($weeks = 1) {

		$articles = $this->Articles->by_weeks_ago($weeks);

		//dd($articles);

		foreach ($articles as $article) {
			$id = $article['id'];
			$pubDate = formatDate($article['pubdate'],'Y-m-d');
			$gaData = $this->Analytics->byArticleID($id, $pubDate);
			$this->save_article_stats($gaData, $id);
		}

		echo 'wenn dieser Text erscheint hats geklappt...<br/>';
		echo 'Processing-Time: <b>'.round((microtime(true)-APP_START)*1000,2) . '</b>ms';

	}

	private function save_article_stats($analyticsData, $id) {
		$dailyStats = $analyticsData['details'];
		$lifeTimeStats = $analyticsData['stats'];
		$this->Articles->add_stats($lifeTimeStats,$id);
		$this->Stats->add($dailyStats,$id);
	}

}
