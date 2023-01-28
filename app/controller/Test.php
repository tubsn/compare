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
		$this->models('Articles,ArticleMeta,Orders,Conversions,Analytics,Charts,Readers,Plenigo,Cleverpush,Orders,DailyKPIs,Kilkaya,Pushes');
	}


	public function test() {


		/*

		$start = 'yesterday';
		$end = 'today';

		$start = date("Y-m-d", strtotime($start));
		$end = date("Y-m-d", strtotime($end));

		$data = $this->Plenigo->subscriptions($start,$end);


		dd($data);
		*/


		//$this->Readers->import_user_segments('2023-01-02', '2023-01-04');

		//$data = $this->Pushes->list_with_userneed();
		//$this->view->csv($data);
		//dd($data);

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




	public function conversionupdater() {

		// Sets Conversions in Daily KPIs

		$startDate = '2022-10-07';
		$endDate = '2022-11-28';

		$start = $this->create_date_object($startDate);
		$end = $this->create_date_object($endDate);

		$interval = new \DateInterval('P1D');
		$period = new \DatePeriod($start, $interval, $end);

		$monthList = [];
		foreach ($period as $date) {
			$date = $date->format('Y-m-d');

			$this->Orders->from = $date;
			$this->Orders->to = $date;
			$conversions = $this->Orders->count();
			$this->DailyKPIs->update(['conversions' => $conversions],$date);

		}


	}

	private function create_date_object($dateString = null) {
		if (is_null($dateString)) {return new \DateTime(date('Y-m-d'));}
		return new \DateTime(date('Y-m-d', strtotime($dateString)));
	}


	public function reader($id) {
		dd($this->Readers->live_from_api($id));
	}



}
