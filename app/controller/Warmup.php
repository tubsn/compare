<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\auth\Auth;

class Warmup extends Controller {

	public function __construct() {

		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Analytics,Linkpulse,Articles,Conversions,Stats');
	}

	public function daterange() {

		if (isset($_GET['from']) && isset($_GET['from'])) {
			$from = $_GET['from'];
			$to = $_GET['to'];
		}
		else {
			$from = date('Y-m-d', strtotime('today -3 days'));
			$to = date('Y-m-d', strtotime('today -1 day'));
		}

		$articles = $this->Articles->by_date_range($from, $to);

		//dd($articles);

		foreach ($articles as $article) {
			$id = $article['id'];
			$pubDate = formatDate($article['pubdate'],'Y-m-d');
			$gaData = $this->Analytics->by_article_id($id, $pubDate);
			$gaData['totals']['subscribers'] = $this->Linkpulse->subscribers($id, $pubDate);
			$gaData['totals']['buyintent'] = $this->Analytics->buy_intention_by_article_id($id, $pubDate);


			// Dont Upgrade Conversions in a Specific Time Period after the Plenigo V3 Update...
			// The Analytics Conversion Data is completely wrong due to a tracking error
			$plenigoUpdateDate = date("2021-02-23");
			$fixDate = date("2021-05-05");
			if ($pubDate > $plenigoUpdateDate && $pubDate < $fixDate) {
				unset($gaData['totals']['Itemquantity']);
			}

			$this->save_article_stats($gaData, $id);
		}

		echo 'wenn dieser Text erscheint hats geklappt...<br/>';
		echo 'Processing-Time: <b>'.round((microtime(true)-APP_START)*1000,2) . '</b>ms';


	}

	public function weeks_ago($weeks = 1) {

		// This Only Updates Pageviews and Conversions

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


	public function conversions() {

		$from = date('Y-m-d', strtotime('today -3 days'));
		$to = date('Y-m-d', strtotime('today -1 day'));
		$articles = $this->Articles->conversions_only_by_date_range($from, $to);

		//dd($articles);

		foreach ($articles as $article) {
			$id = $article['id'];
			$pubDate = formatDate($article['pubdate'],'Y-m-d');

			$this->Conversions->articleID = $id;
			$this->Conversions->pubDate = $pubDate;
			$this->Conversions->refresh();
		}

		echo 'wenn dieser Text erscheint hats geklappt...<br/>';
		echo 'Processing-Time: <b>'.round((microtime(true)-APP_START)*1000,2) . '</b>ms';

	}

	public function subscribers() {

		if (isset($_GET['from']) && isset($_GET['from'])) {
			$from = $_GET['from'];
			$to = $_GET['to'];
		}
		else {
			$from = date('Y-m-d', strtotime('today -3 days'));
			$to = date('Y-m-d', strtotime('today -1 day'));
		}

		$articles = $this->Articles->by_date_range($from, $to);

		//dd($articles);

		foreach ($articles as $article) {
			$id = $article['id'];
			$pubDate = formatDate($article['pubdate'],'Y-m-d');
			$data['subscribers'] = $this->Linkpulse->subscribers($id, $pubDate);
			$this->Articles->update($data, $id);
		}

		echo 'wenn dieser Text erscheint hats geklappt...<br/>';
		echo 'Processing-Time: <b>'.round((microtime(true)-APP_START)*1000,2) . '</b>ms';

	}

	private function save_article_stats($analyticsData, $id) {
		$dailyStats = $analyticsData['details'];
		$lifeTimeTotals = $analyticsData['totals'];
		$this->Articles->add_stats($lifeTimeTotals,$id);
		$this->Stats->add($dailyStats,$id);
	}

}
