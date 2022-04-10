<?php

namespace app\controller;
use flundr\mvc\Controller;
use app\importer\ArticleImport;

class CronImports extends Controller {

	public function __construct() {
		$this->models('Analytics,Linkpulse,Kilkaya,Articles,ArticleMeta,Conversions,Readers,ArticleKPIs,Orders,DailyKPIs,Campaigns,Epaper');
	}


	public function analytics_last_days() {

		$from = date('Y-m-d', strtotime('today -3 days'));
		$to = date('Y-m-d', strtotime('today -1 day'));

		$articles = $this->Articles->by_date_range($from, $to);

		$updatedArticles = 0;
		foreach ($articles as $article) {
			$id = $article['id'];
			$pubDate = formatDate($article['pubdate'],'Y-m-d');
			$gaData = $this->Analytics->by_article_id($id, $pubDate);

			$gaData['totals']['subscribers'] = $this->Kilkaya->subscribers($id, $pubDate);
			$gaData['totals']['buyintent'] = $this->Analytics->buy_intention_by_article_id($id, $pubDate);
			unset($gaData['totals']['Itemquantity']); // Don't Overwrite Plenigo Conversions

			$this->save_article_stats($gaData, $id);

			$updatedArticles++;
		}

		echo $updatedArticles . ' Articles updatet | ' . date('H:i:s') . "\r\n";

	}

	public function analytics_longtail() {

		$from = date('Y-m-d', strtotime('today -7 days'));
		$to = date('Y-m-d', strtotime('today -4 day'));

		$articles = $this->Articles->by_date_range($from, $to);

		$updatedArticles = 0;
		foreach ($articles as $article) {
			$id = $article['id'];
			$pubDate = formatDate($article['pubdate'],'Y-m-d');
			$gaData = $this->Analytics->by_article_id($id, $pubDate);
			unset($gaData['totals']['Itemquantity']); // Don't Overwrite Plenigo Conversions

			$this->save_article_stats($gaData, $id);

			$updatedArticles++;
		}

		echo $updatedArticles . ' Articles - Weekly Stats updatet | ' . date('H:i:s') . "\r\n";

	}


	private function save_article_stats($analyticsData, $id) {
		$dailyStats = $analyticsData['details'];
		$lifeTimeTotals = $analyticsData['totals'];
		$this->Articles->add_stats($lifeTimeTotals,$id);
		$this->ArticleKPIs->add($dailyStats,$id);
	}

	public function feeds() {

		$feeds = IMPORT_FEEDS; // Feeds in Config
		$import = new ArticleImport();

		foreach ($feeds as $feed) {

			$articles = $import->rss($feed);

			$articles = array_filter($articles, function($article) {
				$dpaFilterPattern = "/\b(?:dpa)\b/i"; // Filter DPA
				if (preg_match($dpaFilterPattern,$article['author'])) {return null;}
				if ($article['ressort'] == 'Bilder') {return null;}  // Filter Bildergalerien
				return $article;
			});

			$articles = array_values($articles); // Reindex the array Keys

			$this->Articles->add_to_database($articles);

		}

		$this->ArticleMeta->import_drive_data(); // Emotions and Stuff from DPA Drive

		echo 'Article Feed Import abgeschlossen | ' . date('H:i:s') . "\r\n";

	}


	public function epaper_import() {

		$this->Epaper->from = date('Y-m-d', strtotime('yesterday -3days'));
		$this->Epaper->to = date('Y-m-d', strtotime('yesterday'));

		$this->Epaper->import_ressort_stats_to_db();
		$this->Epaper->import_event_clicks_to_db();

		echo 'ePaper Daten importiert | ' . date('H:i:s') . "\r\n";

	}


	public function import_global_kpis() {
		$this->DailyKPIs->import(3);

		$from = date('Y-m-d', strtotime('yesterday -3days'));
		$to = date('Y-m-d', strtotime('yesterday'));
		$this->DailyKPIs->import_subscribers($from, $to);

		echo 'Global KPIs importiert | ' . date('H:i:s') . "\r\n";
	}

	public function import_utm_campaigns($days = 5) {

		$data = $this->Analytics->utm_campaigns($days);
		foreach ($data as $order) {
			$campaign['order_id'] = $order['Transactionid'];
			$campaign['ga_date'] =  date('Y-m-d', strtotime($order['Date']));
			$campaign['utm_source'] = $order['Source'];
			$campaign['utm_medium'] = $order['Medium'];
			$campaign['utm_campaign'] = $order['Campaign'];
			$this->Campaigns->create_or_update($campaign);
		}

		echo 'UTM Kampagnen importiert | ' . date('H:i:s') . "\r\n";

	}

	public function conversions($daysago = 3) {

		$dates = [];
		array_push($dates, date('Y-m-d', strtotime('today')));
		array_push($dates, date('Y-m-d', strtotime('today -1 day')));
		array_push($dates, date('Y-m-d', strtotime('today -2 day')));
		array_push($dates, date('Y-m-d', strtotime('today -3 day')));

		if ($daysago == 1) {
			$dates = [];
			$dates[0] = date('Y-m-d', strtotime('today'));
		}

		foreach ($dates as $day) {
			$this->Orders->import($day);
			echo 'Conversions fuer ' . $day . ' importiert | ' . date('H:i:s') . "\r\n";
		}

		$this->enrich_conversions_with_ga();
		$this->enrich_conversions_with_drive_segments();

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

	public function enrich_conversions_with_drive_segments() {

		$this->Readers->import_readers();
		$this->Readers->add_segment_to_latest_orders();
		$this->Readers->add_segement_to_latest_cancellations();

		echo 'Drive Segmente importiert | ' . date('H:i:s') . "\r\n";

	}

}
