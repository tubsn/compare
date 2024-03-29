<?php

namespace app\controller;
use flundr\mvc\Controller;
use app\importer\ArticleImport;

class CronImports extends Controller {

	public function __construct() {
		$this->models('Analytics,Linkpulse,Kilkaya,Articles,ArticleMeta,Conversions,Subscriptions,Readers,ArticleKPIs,Orders,DailyKPIs,Campaigns,Epaper');
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

			$gaData['totals']['subscriberviews'] = $this->Kilkaya->subscribers($id, $pubDate);
			$gaData['totals']['buyintent'] = $this->Analytics->buy_intention_by_article_id($id, $pubDate);
			unset($gaData['totals']['Itemquantity']); // Don't Overwrite Plenigo Conversions

			$this->save_article_stats($gaData, $id);

			$updatedArticles++;
		}

		echo $updatedArticles . ' Articles updatet | ' . date('H:i:s') . "\r\n";

	}

	public function analytics_longtail() {

		$from = date('Y-m-d', strtotime('today -7 days'));
		$to = date('Y-m-d', strtotime('today -6 day'));

		$articles = $this->Articles->by_date_range($from, $to);

		$updatedArticles = 0;
		foreach ($articles as $article) {
			$id = $article['id'];
			$pubDate = formatDate($article['pubdate'],'Y-m-d');
			$gaData = $this->Analytics->by_article_id($id, $pubDate);
			unset($gaData['totals']['Itemquantity']); // Don't Overwrite Plenigo Conversions

			// Test for Subscribers 
			$gaData['totals']['subscriberviews'] = $this->Kilkaya->subscribers($id, $pubDate);

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

			/* Filter DPA Articles
			$articles = array_filter($articles, function($article) {
				$dpaFilterPattern = "/\b(?:dpa)\b/i"; // Filter DPA
				if (preg_match($dpaFilterPattern,$article['author'])) {return null;}
				if ($article['ressort'] == 'Bilder') {return null;}  // Filter Bildergalerien
				return $article;
			});
			*/

			$articles = array_filter($articles, function($article) {
				if ($article['ressort'] == 'a') {return null;}  // Filter PR Articles
				return $article;
			});


			$articles = array_values($articles); // Reindex the array Keys

			$this->Articles->add_to_database($articles);

		}

		echo 'RSS Feeds Importiert | ' . date('H:i:s') . "\r\n";

		$this->ArticleMeta->import_drive_data(); // Emotions and Stuff from DPA Drive
		$this->assign_drive_topics(); // Assigns new Drive Topics to Article Table
		$this->assign_drive_userneeds(); // Assigns new Drive Userneeds to Article Table

		echo 'Article Import abgeschlossen | ' . date('H:i:s') . "\r\n";

	}


	public function epaper_import() {

		$this->Epaper->from = date('Y-m-d', strtotime('yesterday -3days'));
		$this->Epaper->to = date('Y-m-d', strtotime('yesterday'));

		$this->Epaper->import_ressort_stats_to_db();
		//$this->Epaper->import_event_clicks_to_db(); Import Disabled Event is not working 23.Jun.22

		echo 'ePaper Daten importiert | ' . date('H:i:s') . "\r\n";

	}


	public function import_global_kpis() {
		$this->DailyKPIs->import(3);

		$from = date('Y-m-d', strtotime('yesterday -3days'));
		$to = date('Y-m-d', strtotime('yesterday'));

		$this->DailyKPIs->import_subscribers($from, $to);
		echo 'Global KPIs importiert | ' . date('H:i:s') . "\r\n";

		$segments = $this->Readers->import_user_segments($from, $to);
		echo 'Global KPI User Segments importiert | ' . date('H:i:s') . "\r\n";

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
		$this->enrich_conversions_with_drive_data();
		$this->enrich_conversions_with_referals();
		echo 'Conversions angereichert | ' . date('H:i:s') . "\r\n";

		$updatedSubscriptions = $this->update_subscriptions();
		echo $updatedSubscriptions . ' Subscriptions erneuert | ' . date('H:i:s') . "\r\n";

		$this->DailyKPIs->count_conversiontable_to_daily_kpis('yesterday -3 days', 'yesterday');
		echo 'Pushed Conversions to DailyKPIs | ' . date('H:i:s') . "\r\n";		

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

	public function enrich_conversions_with_referals() {
		$this->Orders->assign_sources(3);
	}

	public function enrich_conversions_with_drive_data() {

		$this->Readers->import_readers();
		$this->Readers->update_latest_orders();
		$this->Readers->update_latest_cancellations();

		echo 'Drive Userdaten importiert | ' . date('H:i:s') . "\r\n";

	}

	public function update_subscriptions() {
		return $this->Subscriptions->update_last_days();
	}

	public function assign_drive_topics() {

		$unsetIDs = $this->Articles->get_unset_ids();
		$topics = $this->ArticleMeta->topics_for($unsetIDs);

		// No new Topics found
		if (empty($topics)) {
			echo 'keine neuen Drive Topics zugeordnet | ' . date('H:i:s') . "\r\n";		
			return null;
		}

		$topics = array_filter($topics); // Remove possibly Empty Sets
		
		foreach ($topics as $id => $type) {
			$this->Articles->update(['type' => $type], $id);
		}

		echo 'Drive Topics zugeordnet | ' . date('H:i:s') . "\r\n";

	}


	public function assign_drive_userneeds() {

		$unsetIDs = $this->Articles->get_unset_userneed_ids();
		$userneeds = $this->ArticleMeta->userneeds_for($unsetIDs);

		// No new Topics found
		if (empty($userneeds)) {
			echo 'keine neuen Drive Userneeds zugeordnet | ' . date('H:i:s') . "\r\n";		
			return null;
		}

		$userneeds = array_filter($userneeds); // Remove possibly Empty Sets

		foreach ($userneeds as $id => $userneed) {
			$this->Articles->update(['userneed' => $userneed], $id);
		}

		echo 'Drive Userneeds zugeordnet | ' . date('H:i:s') . "\r\n";

	}


}
