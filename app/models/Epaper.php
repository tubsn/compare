<?php

namespace app\models;
use \flundr\database\SQLdb;
use \flundr\mvc\Model;
use \flundr\utility\Session;
use \flundr\cache\RequestCache;
use app\importer\AnalyticsImport;

class Epaper extends Model
{

	public $from = '0000-00-00';
	public $to = '3000-01-01';

	protected $db;

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
		'/wlie/' => 'Lawo-Elbe-Elster',
		'/wele/' => 'Lawo-Elbe-Elster',
		'/lawowele/' => 'Lawo-Elbe-Elster',
	];

	function __construct() {

		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'epaper_kpis';
		$this->db->primaryIndex = 'date';
		$this->db->orderby = 'date';
		$this->db->order = 'DESC';

		$this->ga = new AnalyticsImport;
		$this->ga->profileViewID = '162893723';

		if (PORTAL == 'MOZ') {
			$this->ga->profileViewID = '230870272';
			$this->ressorts = [
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
		}

		if (PORTAL == 'SWP') {
			$this->ga->profileViewID = '121139429';
			$this->ressorts = [
				'/ulm/' => 'Ulm',
				'/neu-ulm/' => 'Neu Ulm',
				'/ehingen-und-umgebung/' => 'Ehingen',
				'/laichinger-alb-mit-alb-donau-kreis/' => 'Alb-Donau-Kreis',
				'/illertal-bote-mit-kreis-neu-ulm/' => 'Iller- und Rothtal',
				'/illertal-bote-mit-alb-donau-kreis/' => 'Iller- und Weihungstal',
				'/blaumaennle/' => 'Blaubeuren',
				'/langenau-aktuell/' => 'Langenau',
				'/goeppingen/' => 'Göppingen',
				'/eislingen/' => 'Eislingen',
				'/geislingen/' => 'Geislingen',
				'/muensingen/' => 'Münsingen',
				'/hechingen/' => 'Hechingen',
				'/metzingen/' => 'Metzingen',
				'/reutlingen/' => 'Reutlingen',
				'/schwaebisch-hall/' => 'Schwaebisch Hall',
				'/crailsheim/' => 'Crailsheim',
				'/gaildorf/' => 'Gaildorf',
			];
		}


		$this->from = date('Y-m-d', strtotime(DEFAULT_FROM));
		$this->to = date('Y-m-d', strtotime(DEFAULT_TO));

		if (Session::get('from')) {$this->from = Session::get('from');}
		if (Session::get('to')) {$this->to = Session::get('to');}

		if (Session::get('to') == '2050-01-01') {
			$this->from = '2010-01-01';
			$this->to = date('Y-m-d', strtotime('today'));
		}

	}



	public function articles($merge = true) {

		$data = $this->gather_article_list();

		$output = [];
		foreach ($data as $index => $article) {

			$output[$index]['id'] = $this->extract_id($article['Pagepath']);
			$output[$index]['url'] = $article['Pagepath'];
			$output[$index]['ressort'] = $this->ressorts[$article['Pagepathlevel2']] ?? $article['Pagepathlevel2'];
			$output[$index]['garessort'] = substr($article['Pagepathlevel2'],1,-1);
			$output[$index]['title'] = $this->extract_title($article['Pagepathlevel4']);
			$output[$index]['pubdate'] = substr($article['Pagepathlevel3'],1,-1);
			$output[$index]['pageviews'] = $article['Pageviews'];
			$output[$index]['sessions'] = $article['Sessions'];
			$output[$index]['mediatime'] = round($article['Avgtimeonpage'],2);
			$output[$index]['rawmediatime'] = round($article['Timeonpage']);

		}

		if ($merge) {
			$output = $this->merge_similar_articles($output);
		}

		return $output;

	}

	private function merge_similar_articles(array $articles) {

		$maxRessorts = 13;
		if (PORTAL == 'MOZ') {$maxRessorts = 15;}

		$merged = [];

		foreach ($articles as $article) {

			$titles = array_column($merged, 'title');

			if (!array_search($article['title'], $titles)) {
				array_push($merged, $article);
				continue;
			}

			$key = array_search($article['title'], $titles);

			$merged[$key]['pageviews'] = $merged[$key]['pageviews'] + $article['pageviews'];
			$merged[$key]['rawmediatime'] = $merged[$key]['rawmediatime'] + $article['rawmediatime'];
			$merged[$key]['mediatime'] = round($merged[$key]['rawmediatime'] / $merged[$key]['pageviews'],2);

			if (is_array($merged[$key]['ressort'])) {
				array_push($merged[$key]['ressort'], $article['ressort']);
			}

			else {
				$merged[$key]['ressort'] = [$article['ressort']];
			}

		}

		$emptyArticleKey = array_search('', array_column($merged, 'title'));
		unset($merged[$emptyArticleKey]);

		$merged = array_filter($merged, function($article) use ($maxRessorts) {
			if ($article['pageviews'] < 10) {return false;}
			if ($article['title'] == 'Polizei') {return false;}
			if (is_array($article['ressort']) && count($article['ressort']) > $maxRessorts) {return false;}
			return $article;
		});

		uasort ($merged, function ($a, $b) {
			return $a['pageviews'] < $b['pageviews'];
		});

		return $merged;

	}



	public function gather_article_list() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$this->ga->metrics = 'ga:pageViews, ga:sessions, ga:avgTimeOnPage, ga:timeOnPage';
		$this->ga->from = $from;
		$this->ga->to = $to;
		$this->ga->dimensions = 'ga:pagePathLevel4, ga:pagePathLevel3, ga:pagePathLevel2, ga:pagePath';
		$this->ga->sort = '-ga:pageViews';
		$this->ga->filters = 'ga:pagePath=@.html';
		$this->ga->maxResults = '10000';

		$cache = new RequestCache('epaper-article-' . $from . $to . PORTAL, 10*60);
		$cachedData = $cache->get();
		if ($cachedData) {return $cachedData;}

		$gaData = $this->ga->fetch();

		$cache->save($gaData);
		return $gaData;

	}

	public function article_stats() {

		$data = $this->gather_article_stats();

		$output = [];
		foreach ($data as $index => $article) {

			$output[$index]['date'] = formatDate($article['Date'], 'Y-m-d');
			$output[$index]['ressort'] = $this->ressorts[$article['Pagepathlevel2']] ?? $article['Pagepathlevel2'];
			$output[$index]['pageviews'] = $article['Pageviews'];
			$output[$index]['sessions'] = $article['Sessions'];
			$output[$index]['mediatime'] = round($article['Timeonpage']);
			$output[$index]['avgmediatime'] = round($article['Avgtimeonpage'],2);

		}

		return $output;

	}


	public function gather_article_stats() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$this->ga->metrics = 'ga:pageViews, ga:sessions, ga:avgTimeOnPage, ga:timeOnPage';
		$this->ga->from = $from;
		$this->ga->to = $to;
		$this->ga->dimensions = 'ga:pagePathLevel2,ga:date';
		$this->ga->sort = '-ga:pageViews';
		$this->ga->filters = 'ga:pagePath=@.html';
		$this->ga->maxResults = '5000';

		$cache = new RequestCache('epaper-article-sessions' . $from . $to . PORTAL, 10*60);
		$cachedData = $cache->get();
		if ($cachedData) {return $cachedData;}

		$gaData = $this->ga->fetch();
		$output = $this->ga->metric_totals();

		$cache->save($gaData);
		return $gaData;

	}




	public function by_ressort($ressort) {

		$ressort = '/' . $ressort . '/';
		$data = $this->gather_ressort_articles($ressort);

		$output = [];
		foreach ($data as $index => $article) {

			$output[$index]['id'] = $this->extract_id($article['Pagepath']);
			$output[$index]['url'] = $article['Pagepath'];
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

		$cache = new RequestCache('epaper-article-'.$ressort . $from . $to . PORTAL, 10*60);
		$cachedData = $cache->get();
		if ($cachedData) {return $cachedData;}

		$gaData = $this->ga->fetch();

		$cache->save($gaData);
		return $gaData;

	}


	public function ressort_stats($filterBy = null) {
		return $this->get_grouped_stats('ressort', false, true, $filterBy);
	}

	public function date_stats($filterBy = null) {
		return $this->get_grouped_stats('date', true, false, $filterBy);
	}

	public function week_stats($filterBy = null) {
		return $this->get_grouped_stats('date', true, false, $filterBy, "DATE_FORMAT(date,'%Y-%u')");
	}

	public function month_stats($filterBy = null) {
		return $this->get_grouped_stats('date', true, false, $filterBy, "DATE_FORMAT(date,'%Y-%m')");
	}

	public function get_grouped_stats($groupedBy = 'date', $sortByGroup = false, $decending = true, $filterBy = null, $overwriteDate = null) {

		$table = $this->db->table;
		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$filter = "AND `ressort` is not null AND `ressort` != ''";
		if ($filterBy) {
			$filterBy = strip_tags($filterBy);
			$filterBy = $this->ressort_speaking_name('/'.$filterBy.'/');
			$filter = "AND `ressort` = '" . $filterBy . "'";
		}

		if ($groupedBy != 'date') {$groupedBy = 'ressort';}
		else {$groupedBy = 'date';}

		$sortBy = 'pageviews';
		if ($sortByGroup) {$sortBy = $groupedBy;}

		$order = 'ASC';
		if ($decending) {$order = 'DESC';}

		if ($overwriteDate) {$groupedBy = $overwriteDate;}

		$SQLstatement = $this->db->connection->prepare(
			"SELECT $groupedBy,
				sum(pageviews) as pageviews,
				sum(pageviews_article) as pageviews_article,
				sum(sessions) as sessions,
				sum(sessions_article) as sessions_article,
				sum(mediatime) as mediatime,
				sum(mediatime_article) as mediatime_article,
				round(sum(mediatime) / sum(pageviews),2) as avgmediatime,
				round(sum(mediatime_article) / sum(pageviews_article),2) as avgmediatime_article
			 FROM `$table`
			 WHERE DATE(`date`) BETWEEN :startDate AND :endDate
			 $filter
			 GROUP BY $groupedBy
			 ORDER BY $sortBy $order"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_UNIQUE);

		return $output;

	}


	public function import_event_clicks_to_db() {

		$db = new DailyKPIs();
		$eventData = $this->gather_clicks_on_epaper_btn();

		$failedDates = [];
		foreach ($eventData as $date => $clicks) {
			$success = $db->update(['epaper_naviclicks' => $clicks], $date);
			if (!$success) {
				array_push($failedDates, $date);
			}
		}

		return $failedDates;

	}

	public function import_ressort_stats_to_db() {

		$ressortData = $this->gather_ressort_stats();
		$ressortNames = $this->ressorts;

		$ressortStats = array_map(function ($set) use ($ressortNames) {

			$out['date'] = formatDate($set['Date'], 'Y-m-d');
			$out['ressort'] = $ressortNames[$set['Pagepathlevel2']] ?? null;
			$out['pageviews'] = $set['Pageviews'];
			$out['sessions'] = $set['Sessions'];
			$out['mediatime'] = round($set['Timeonpage']);
			$out['avgmediatime'] = round($set['Avgtimeonpage'],2);

			return $out;

		}, $ressortData);

		$articleStats = $this->article_stats();

		$ressortStats = array_group_by('date', $ressortStats);
		$articleStats = array_group_by('date', $articleStats);

		$temp = [];
		foreach ($ressortStats as $date => $entry) {
			$temp[$date] = array_group_by('ressort', $entry);
		}
		$ressortStats = $temp;

		$temp = [];
		foreach ($articleStats as $date => $entry) {
			$temp[$date] = array_group_by('ressort', $entry);
		}
		$articleStats = $temp;

		// Combine Ressort and Article Stats

		$ePaperStats = [];
		foreach ($ressortStats as $date => $ressorts) {

			foreach ($ressorts as $ressort => $stats) {

				$articleData = $articleStats[$date][$ressort] ?? false;

				$out['date'] = $date;
				$out['ressort'] = $ressort;
				$out['pageviews'] = array_sum(array_column($stats,'pageviews'));
				$out['sessions'] = array_sum(array_column($stats,'sessions'));
				$out['mediatime'] = array_sum(array_column($stats,'mediatime'));

				if ($articleData) {
					$out['pageviews_article'] = array_sum(array_column($articleData,'pageviews'));
					$out['sessions_article'] = array_sum(array_column($articleData,'sessions'));
					$out['mediatime_article'] = array_sum(array_column($articleData,'mediatime'));
				}

				array_push($ePaperStats, $out);

			}


		}

		foreach (array_column($ePaperStats,'date') as $date) {
			$this->db->delete($date);
		};

		foreach ($ePaperStats as $set) {
			$this->db->create($set);
		}

	}


	public function gather_ressort_stats() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$this->ga->metrics = 'ga:pageViews,ga:sessions,ga:timeOnPage,ga:avgTimeOnPage';
		$this->ga->from = $from;
		$this->ga->to = $to;
		$this->ga->dimensions = 'ga:pagePathLevel2,ga:date';
		$this->ga->sort = '-ga:pageViews';
		$this->ga->maxResults = '5000';

		$cache = new RequestCache('epaper-ressortstats-' . $from . $to . PORTAL, 10*60);
		$cachedData = $cache->get();
		if ($cachedData) {return $cachedData;}

		$gaData = $this->ga->fetch();

		$cache->save($gaData);
		return $gaData;

	}

	public function gather_clicks_on_epaper_btn() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$ga = new AnalyticsImport; // Data from Main Portal
		$ga->metrics = 'ga:totalEvents';
		$ga->from = $from;
		$ga->to = $to;
		$ga->dimensions = 'ga:date';
		$ga->sort = 'ga:date';
		$ga->filters = 'ga:eventLabel==ePaper;ga:eventAction==click';
		if (PORTAL == 'SWP') {
			$ga->filters = 'ga:eventLabel==eZeitung;ga:eventAction==click';
		}
		$ga->maxResults = '1000';

		$cache = new RequestCache('epaper-btn-clicks-' . $from . $to . PORTAL, 60*60);
		$cachedData = $cache->get();
		if ($cachedData) {return $cachedData;}

		$gaData = $ga->fetch();
		$gaData = array_map(function ($set) {
			$out['date'] = formatDate($set['Date'], 'Y-m-d');
			$out['clicks'] = $set['Totalevents'];
			return $out;
		}, $gaData);
		$gaData = array_column($gaData, 'clicks', 'date');

		$cache->save($gaData);
		return $gaData;

	}

	public function clicks_on_epaper_btn() {

		$dailyKPIs = new DailyKPIs();
		$clickData = $dailyKPIs->kpi_grouped_by('epaper_naviclicks','date');
		$clickData = array_column($clickData, 'epaper_naviclicks', 'date');

		return $clickData;

	}

	/*
	public function epaper_daily_sessions() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$this->ga->metrics = 'ga:sessions';
		$this->ga->from = $from;
		$this->ga->to = $to;
		$this->ga->dimensions = 'ga:date';
		$this->ga->sort = 'ga:date';
		$this->ga->maxResults = '1000';

		$cache = new RequestCache('epaper-daily-sessions-' . $from . $to . PORTAL, 60*60);
		$cachedData = $cache->get();
		if ($cachedData) {return $cachedData;}

		$gaData = $this->ga->fetch();
		$gaData = array_map(function ($set) {
			$out['date'] = formatDate($set['Date'], 'Y-m-d');
			$out['sessions'] = $set['Sessions'];
			return $out;
		}, $gaData);

		$gaData = array_column($gaData, 'sessions', 'date');

		$cache->save($gaData);
		return $gaData;

	}
	*/


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

	public function ressort_url($ressort) {
		$ressorts = $this->ressorts;
		$ressorts = array_flip($ressorts);
		return $ressorts[$ressort] ?? $ressort;
	}

	public function ressort_speaking_name($ressort) {
		return $this->ressorts[$ressort] ?? $ressort;
	}

	public function distinct_ressorts() {
		$ressorts = $this->ressorts;
		return array_flip($ressorts);
	}

	private function extract_id($url) {
		// Regex search for the ID = -8Digits.html
		$searchPattern = "/-(\d{8}).html/";
		preg_match($searchPattern, $url, $matches);
		return $matches[1] ?? NULL; // First Match should be the ID
	}

	private function extract_title($path) {

		$path = explode('/', $path);
		$rawTitle = $path[2] ?? '';

		$rawTitle = explode('-', $rawTitle);
		array_pop($rawTitle);

		$title = implode(' ', $rawTitle);
		$title = ucwords($title);

		if (empty($title)) {return 'Kein Titel';}
		return $title;
	}

}
