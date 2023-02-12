<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;
use app\models\helpers\CSVImports;
use app\importer\PlenigoAPI;
use app\importer\CleverpushAPI;

class Test extends Controller {

	public function __construct() {
		$this->view('DefaultLayout');
		$this->models('Articles,ArticleMeta,Orders,Subscriptions,Conversions,Analytics,Charts,Readers,Plenigo,Cleverpush,Orders,DailyKPIs,Kilkaya,Pushes');
	}


	public function test() {

		$this->Subscriptions->update_last_days();

		//$this->DailyKPIs->count_conversiontable_to_daily_kpis('today', 'today');

		//$this->Readers->import_user_segments('2023-01-02', '2023-01-04');

	}




	public function import_userneeds() {

		$this->ArticleMeta->import_drive_data();

		///$articles = $this->Articles->list_all_userneeds();
		//dd($articles);

		$unsetIDs = $this->Articles->get_unset_userneed_ids();
		$userneeds = $this->ArticleMeta->userneeds_for($unsetIDs);

		// No new Topics found
		if (empty($userneeds)) {
			echo 'keine neuen Drive Userneeds zugeordnet | ' . date('H:i:s') . "\r\n";
			return null;
		}

		$userneeds = array_filter($userneeds);


		foreach ($userneeds as $id => $userneed) {
			$this->Articles->update(['userneed' => $userneed], $id);
		}

		echo 'Drive Userneeds zugeordnet | ' . date('H:i:s') . "\r\n";

	}



	public function kilkayaimporttest() {



		$from = date('Y-m-d', strtotime('today -3 days'));
		$to = date('Y-m-d', strtotime('today -1 day'));

		$articles = $this->Articles->by_date_range($from, $to);

		$articles = array_slice($articles,0,1);


		$stats = [];
		foreach ($articles as $article) {
			$id = $article['id'];
			$pubDate = formatDate($article['pubdate'],'Y-m-d');

			/*
			$gaData = $this->Analytics->by_article_id($id, $pubDate);
			$gaData['totals']['subscriberviews'] = $this->Kilkaya->subscribers($id, $pubDate);
			$gaData['totals']['buyintent'] = $this->Analytics->buy_intention_by_article_id($id, $pubDate);
			unset($gaData['totals']['Itemquantity']); // Don't Overwrite Plenigo Conversions
			*/
			$stats[$id]['ga'] = $gaData = $this->Analytics->by_article_id($id, $pubDate);;
			$stats[$id]['kilkaya'] = $this->Kilkaya->article_by_day($id, $pubDate);
			$stats[$id]['kilkayamt'] = $this->Kilkaya->article_mt($id, $pubDate);

		}

		dd($stats);

		//$this->DailyKPIs->import();
		//$this->Readers->import_user_segments('2022-11-21', '2022-11-23');

	}



	public function reader($id) {
		dd($this->Readers->live_from_api($id));
	}



}
