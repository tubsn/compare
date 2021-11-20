<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\auth\Auth;

class Warmup extends Controller {

	public function __construct() {

		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Analytics,Linkpulse,Articles,ArticleMeta,Conversions,ArticleKPIs,Orders');
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

		// This Only Updates Pageviews
		$articles = $this->Articles->by_weeks_ago($weeks);

		foreach ($articles as $article) {

			$id = $article['id'];
			$pubDate = formatDate($article['pubdate'],'Y-m-d');
			$gaData = $this->Analytics->by_article_id($id, $pubDate);

			$this->save_article_stats($gaData, $id);

		}

		echo 'wenn dieser Text erscheint hats geklappt...<br/>';
		echo 'Processing-Time: <b>'.round((microtime(true)-APP_START)*1000,2) . '</b>ms';

	}


	public function enrich_conversions_with_ga($daysago = 5) {

		$plainOrders = $this->Orders->without_ga_sources($daysago);
		$transactionInfoList = $this->Analytics->transaction_metainfo_as_list($daysago);
		$transactionInfoList = array_combine(array_column($transactionInfoList, 'Transactionid'), $transactionInfoList);

		$enrichedData = array_intersect_key($transactionInfoList, $plainOrders);

		foreach ($enrichedData as $orderID => $data) {
			$order['ga_source'] = $data['Source'];
			$order['ga_sessions'] = $data['Sessioncount'];
			$order['ga_city'] = $data['City'];
			$this->Orders->update($order, $orderID);
		}

	}


	public function enrich_article_with_buy_intents() {
		/* Does not Work ... GA Data is Sampled too much */
		$buyIntentions = $this->Analytics->list_buy_intention();
		dd($buyIntentions);

	}


	public function conversions($daysago = 3) {

		/*
		still need to implement functionality :S
		*/
		$dates = [];
		array_push($dates, date('Y-m-d', strtotime('today')));
		array_push($dates, date('Y-m-d', strtotime('today -1 day')));
		array_push($dates, date('Y-m-d', strtotime('today -2 day')));
		array_push($dates, date('Y-m-d', strtotime('today -3 day')));
		array_push($dates, date('Y-m-d', strtotime('today -4 day')));

		if ($daysago == 1) {
			$dates = [];
			$dates[0] = date('Y-m-d', strtotime('today'));
		}

		foreach ($dates as $day) {
			$this->Orders->import($day);
		}

		$this->enrich_conversions_with_ga();

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


	public function topic_clusters() {

		// Refresh is made Daily by cronjob
		$this->ArticleMeta->import_drive_data();

		$unsetIDs = $this->Articles->get_unset_ids();

		$topics = $this->ArticleMeta->topics_for($unsetIDs);

		if (empty($topics)) {
			echo 'nix zuzuordnen'; return null;
		}

		$articleInfo = $this->Articles->get(array_keys($topics),['id','title']);

		//Automatically set Topic Clusters
		$counter = 0;
		foreach ($topics as $id => $type) {
			$text = '<a href="/artikel/' . $id . '">[' . $id . ']</a> ' . $articleInfo[$counter]['title'] . ' => ' . $type ?? '-';
			echo $text . '<br />';
			$counter++;
			if (empty($type)) {continue;}
			$this->Articles->update(['type' => $type], $id);
		}

	}

	private function save_article_stats($analyticsData, $id) {
		$dailyStats = $analyticsData['details'];
		$lifeTimeTotals = $analyticsData['totals'];
		$this->Articles->add_stats($lifeTimeTotals,$id);
		$this->ArticleKPIs->add($dailyStats,$id);
	}

}
