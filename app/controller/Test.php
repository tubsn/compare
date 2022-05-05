<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;

class Test extends Controller {

	public function __construct() {

		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Articles,Orders,Conversions,Analytics,Charts');
	}

	public function index($id) {

		$article = $this->Articles->get($id);
		$pubDate = formatDate($article['pubdate'],'Y-m-d');

		$buyintent = $this->Analytics->buy_intention_by_article_id($id, $pubDate);


		echo 'Paywallklicks: ' . $buyintent .'<br>';
		echo 'Conversions:' . $article['conversions'] .'<br>';
		dump($article);

	}

	public function publications($audience = ARTICLE_AUDIENCES[0]) {

		$this->view->audienceList = ARTICLE_AUDIENCES;

		$articles = $this->Articles->audience_by_time($audience);
		$orders = $this->Orders->audience_by_time($audience);
		$sessions = $this->Analytics->use_timeframe_by_audience(ucfirst($audience));

		$data = [];
		foreach (range(0,23) as $key => $void) {
			$data[$key]['articles'] = $articles[$key]['articles'] ?? 0;
			$data[$key]['orders'] = $orders[$key]['orders'] ?? 0;
			$data[$key]['sessions'] = $sessions[$key]['Sessions'] ?? 0;
		}

		$this->view->audience = ucFirst($audience);
		$this->view->chart = $this->Charts->convert($data);
		$this->view->charts = $this->Charts;

		$this->view->title = 'Zeitliche Interessensverteilung: ' . ucFirst($audience);
		$this->view->render('pages/audiences-by-time');

	}


	private function fill_gaps($array) {

		$maxDays = 23;
		$range = range(0,$maxDays);

		$out = [];
		foreach ($range as $day) {
			if (isset($array[$day])) {
				$out[$day] = $array[$day];
			}
			else {
				$out[$day]['articles'] = 0;
			}
		}

		return $out;

	}


}
