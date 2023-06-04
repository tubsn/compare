<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;
use app\models\helpers\CSVImports;
use app\importer\PlenigoAPI;
use app\importer\CleverpushAPI;

class Test extends Controller {

	//dd($this->Readers->drive_active_user_segments('2023-03-06','2023-03-10'));
	//$readers = $this->Readers->drive_active_user_segments_newdb('2023-03-06','2023-03-10');
	//dd($this->Readers->import_user_segments('2023-03-01', '2023-03-16'));

	public function __construct() {
		$this->view('DefaultLayout');
		$this->models('Articles,ArticleMeta,Orders,Subscriptions,Conversions,Analytics,Charts,Readers,Plenigo,Cleverpush,Orders,DailyKPIs,Kilkaya,Pushes');
	}

	public function importstuff() {
		//dd($this->Readers->import_user_segments('2023-05-25', '2023-05-27'));
		//$this->DailyKPIs->import(3);
	}


	public function test() {



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

	}


}
