<?php

namespace app\models;
use \flundr\database\SQLdb;
use \flundr\mvc\Model;
use \flundr\utility\Session;

class ArticleKPIs extends Model
{

	public $from = '0000-00-00';
	public $to = '3000-01-01';

	private $articleID;
	protected $db;

	function __construct() {
		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'article_kpis';
		$this->db->orderby = 'date';
		$this->db->order = 'DESC';

		$this->from = date('Y-m-d', strtotime(DEFAULT_FROM));
		$this->to = date('Y-m-d', strtotime(DEFAULT_TO));

	}

	public function by_id($id) {
		$this->columns = ['date','pageviews','sessions','conversions'];
		return $this->exact_search($id,'id');
	}

	public function add($statsByDay, $id) {

		$this->db->delete($id); // delete existing Stats to prevent pile up with each refresh

		$this->articleID = $id;
		$statsByDay = array_map([$this,'map_analytics_data'], $statsByDay);

		foreach ($statsByDay as $day) {$this->db->create($day);}
	}

	public function map_analytics_data($analytics) {
		$output['id'] = $this->articleID;
		$output['date'] = $analytics['Date'];
		$output['pageviews'] = $analytics['Pageviews'];
		$output['sessions'] = $analytics['Sessions'];
		$output['conversions'] = $analytics['Itemquantity'];
		return $output;
	}


	public function detail_chart_data($stats) {

		if ($stats == null) {return null;}

		$dates = array_column($stats,'date');
		$pageviews = array_column($stats,'pageviews');
		$sessions = array_column($stats,'sessions');
		$conversions = array_column($stats,'conversions');

		$dates = array_reverse($dates);
		$pageviews = array_reverse($pageviews);
		$sessions = array_reverse($sessions);
		$conversions = array_reverse($conversions);

		$preDate = strtotime($dates[0] . ' - 1 day');
		array_unshift($dates, date('Y-m-d', $preDate));

		$dates = preg_filter('/^/', '\'', $dates);
		$dates = preg_filter('/$/', '\'', $dates);

		array_unshift($pageviews, '0');
		array_unshift($sessions, '0');
		array_unshift($conversions, '0');

		$dates = implode(',', $dates);
		$pageviews = implode(',', $pageviews);
		$sessions = implode(',', $sessions);
		$conversions = implode(',', $conversions);

		return ['dates' => $dates, 'pageviews' => $pageviews, 'sessions' => $sessions, 'conversions' => $conversions];

	}


	public function combined_kpis_filtered_chart($filter = null, $column = 'ressort') {

		$articles = $this->with_article_data();
		if (empty($articles)) {return;}

		if ($filter) {
			$articles = array_filter($articles, function($article) use ($filter, $column) {
				return $article[$column] == $filter;
			});
		}

		$dailyStats = $this->sum_stats_by_date($articles);

		if (!isset($dailyStats)) {return null;}

		$chartData['pageviews'] = implode(',', array_column($dailyStats,'pageviews'));
		$chartData['sessions'] = implode(',', array_column($dailyStats,'sessions'));
		$chartData['conversions'] = implode(',', array_column($dailyStats,'conversions'));
		$chartData['dates'] = "'".implode("','", array_keys($dailyStats)) ."'";

		$charts = new Chartengine();
		return $charts->render('charts/combined-kpis', $chartData);

	}


	private function sum_stats_by_date($stats) {

		$days = [];
		foreach ($stats as $item) {

			if (!isset($days[$item['date']]['pageviews'])) {
				$days[$item['date']]['pageviews'] = 0;
				$days[$item['date']]['sessions'] = 0;
				$days[$item['date']]['conversions'] = 0;
			}

			$days[$item['date']]['pageviews'] += $item['pageviews'];
			$days[$item['date']]['sessions'] += $item['sessions'];
			$days[$item['date']]['conversions'] += $item['conversions'];

		}

		return array_reverse($days);

	}


	public function with_article_data() {

		if (Session::get('from')) {$this->from = Session::get('from');}
		if (Session::get('to')) {$this->to = Session::get('to');}

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT `article_kpis`.*,

			 Articles.ressort as ressort,
			 Articles.type as type,
			 Articles.tag as tag,
			 Articles.audience as audience,
			 Articles.userneed as userneed,
			 Articles.author as author

			 FROM `article_kpis`

			 LEFT JOIN `articles` AS Articles
	 		 ON `article_kpis`.id = Articles.id

			 WHERE DATE(`date`) BETWEEN :startDate AND :endDate

			 ORDER BY date DESC"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}
		return $output;

	}


}
