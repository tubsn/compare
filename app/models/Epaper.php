<?php

namespace app\models;
use app\importer\AnalyticsImport;
use \flundr\utility\Session;
use \flundr\cache\RequestCache;

class Epaper
{

	public $from = '0000-00-00';
	public $to = '3000-01-01';

	private $ga;
	private $ressorts = [
		'/cos/' => 'Cottbus',
		'/sen/' => 'Senftenberg',
		'/hoy/' => 'Hoyerswerda',
		'/wwr/' => 'Weißwasser',
		'/spr/' => 'Spremberg',
		'/for/' => 'Forst',
		'/gub/' => 'Guben',
		'/lie/' => 'Elsterwerda',
		'/fin/' => 'Finsterwalde',
		'/her/' => 'Herzberg',
		'/lue/' => 'Lübben',
		'/cal/' => 'Lübbenau',
		'/luc/' => 'Luckau',
		'/wspn/' => 'Lawo-Spree-Neiße',
		'/wcos/' => 'Lawo-Cottbus',
		'/wsen/' => 'Lawo-Senftenberg',
		'/wspw/' => 'Lawo-Spreewald',
		'/frankfurt/' => 'Frankfurt',
		'/angermuende/' => 'Angermünde',
		'/freienwalde/' => 'Bad Freienwalde',
		'/bernau/' => 'Bernau',
		'/eberswalde/' => 'Eberswalde',
		'/eisenhuettenstadt/' => 'Eisenhüttenstadt',
		'/erkner/' => 'Erkner',
		'/fuerstenwalde/' => 'Fürstenwalde',
		'/gransee/' => 'Gransee',
		'/hennigsdorf/' => 'Henningsdorf',
		'/neuruppin/' => 'Neuruppin',
		'/oranienburg/' => 'Oranienburg',
		'/schwedt/' => 'Schwedt',
		'/seelow/' => 'Seelow',
		'/strausberg/' => 'Strausberg',
	];

	function __construct() {
		$this->ga = new AnalyticsImport;
		$this->ga->profileViewID = '162893723';

		if (PORTAL == 'MOZ') {
			$this->ga->profileViewID = '230870272';
		}

		$this->from = date('Y-m-d', strtotime('yesterday -6days'));
		$this->to = date('Y-m-d', strtotime('yesterday'));

		if (Session::get('from')) {$this->from = Session::get('from');}
		if (Session::get('to')) {$this->to = Session::get('to');}

		if (Session::get('to') == '2050-01-01') {
			$this->from = '2010-01-01';
			$this->to = date('Y-m-d', strtotime('today'));
		}

	}


	public function test() {






	}



	public function articles() {

		$data = $this->gather_article_list();

		$output = [];
		foreach ($data as $index => $article) {
			
			$output[$index]['id'] = $this->extract_id($article['Pagepath']);
			$output[$index]['ressort'] = $this->ressorts[$article['Pagepathlevel2']] ?? $article['Pagepathlevel2'];
			$output[$index]['garessort'] = substr($article['Pagepathlevel2'],1,-1);			
			$output[$index]['title'] = $this->extract_title($article['Pagepathlevel4']);
			$output[$index]['pubdate'] = substr($article['Pagepathlevel3'],1,-1);
			$output[$index]['pageviews'] = $article['Pageviews'];
			$output[$index]['mediatime'] = round($article['Avgtimeonpage'],2);

		}

		return $output;


	}


	private function extract_title($path) {

		$path = explode('/', $path);
		$rawTitle = $path[2];

		$rawTitle = explode('-', $rawTitle);
		array_pop($rawTitle);

		$title = implode(' ', $rawTitle);
		$title = ucwords($title);

		return $title;
	}


	public function gather_article_list() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$this->ga->metrics = 'ga:pageViews, ga:avgTimeOnPage';
		$this->ga->from = $from;
		$this->ga->to = $to;
		$this->ga->dimensions = 'ga:pagePathLevel4, ga:pagePathLevel3, ga:pagePathLevel2, ga:pagePath';
		$this->ga->sort = '-ga:pageViews';
		$this->ga->filters = 'ga:pagePath=@.html';
		$this->ga->maxResults = '1000';

		return $this->ga->fetch();	

	}

	public function by_ressort($ressort) {

		$ressort = '/' . $ressort . '/';
		$data = $this->gather_ressort_articles($ressort);

		$output = [];
		foreach ($data as $index => $article) {
			
			$output[$index]['id'] = $this->extract_id($article['Pagepath']);
			$output[$index]['ressort'] = $this->ressorts[$article['Pagepathlevel2']] ?? $article['Pagepathlevel2'];
			$output[$index]['garessort'] = substr($article['Pagepathlevel2'],1,-1);
			$output[$index]['title'] = $this->extract_title($article['Pagepathlevel4']);
			$output[$index]['pubdate'] = substr($article['Pagepathlevel3'],1,-1);
			$output[$index]['pageviews'] = $article['Pageviews'];
			$output[$index]['mediatime'] = round($article['Avgtimeonpage'],2);

		}

		return $output;

	}


	public function gather_ressort_articles($ressort) {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$this->ga->metrics = 'ga:pageViews, ga:avgTimeOnPage';
		$this->ga->from = $from;
		$this->ga->to = $to;
		$this->ga->dimensions = 'ga:pagePathLevel4, ga:pagePathLevel3, ga:pagePathLevel2, ga:pagePath';
		$this->ga->sort = '-ga:pageViews';
		$this->ga->filters = 'ga:pagePath=@.html;ga:pagePathLevel2==' . $ressort;
		$this->ga->maxResults = '500';

		return $this->ga->fetch();	

	}


	public function ressorts() {

		$data = $this->gather_ressort_stats();
		$ressortWhitelist = $this->ressorts;

		$output = [];
		foreach ($data as $set) {

			$gaRessort = $set['Pagepathlevel2'];
			$value = $set['Pageviews'];

			if (isset($ressortWhitelist[$gaRessort])) {
				$output[$ressortWhitelist[$gaRessort]] = $value;
			}

		}

		return $output;

	}


	public function gather_ressort_stats($from = '30daysAgo', $to = 'today') {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$this->ga->metrics = 'ga:pageViews';
		$this->ga->from = $from;
		$this->ga->to = $to;
		$this->ga->dimensions = 'ga:pagePathLevel2';
		$this->ga->sort = '-ga:pageViews';
		//$this->ga->filters = 'ga:pagePath=@' . $articleID . ';ga:pageviews>0';
		$this->ga->maxResults = '365';

		return $this->ga->fetch();

	}


	public function by_article_id($articleID, $from = '30daysAgo', $to = 'today') {

		$articleID = htmlspecialchars($articleID, ENT_QUOTES, 'UTF-8');
		if (strlen($articleID) != 8) {return false;}

		$this->ga->metrics = 'ga:pageViews,ga:sessions,ga:timeOnPage,ga:avgTimeOnPage';
		$this->ga->from = $from;
		$this->ga->to = $to;
		$this->ga->dimensions = 'ga:date';
		$this->ga->sort = '-ga:date';
		$this->ga->filters = 'ga:pagePath=@' . $articleID . ';ga:pageviews>0';
		$this->ga->maxResults = '365';

		$details = $this->ga->fetch();

		return [
			'totals' => $this->ga->metric_totals(),
			'details' => $details
		];

	}


	private function extract_id($url) {
		// Regex search for the ID = -8Digits.html
		$searchPattern = "/-(\d{8}).html/";
		preg_match($searchPattern, $url, $matches);
		return $matches[1] ?? NULL; // First Match should be the ID
	}

}
