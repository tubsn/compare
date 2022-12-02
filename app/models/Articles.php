<?php

namespace app\models;
use \flundr\database\SQLdb;
use \flundr\mvc\Model;
use \flundr\utility\Session;
use \flundr\cache\RequestCache;

class Articles extends Model
{

	public $from = '0000-00-00';
	public $to = '3000-01-01';

	protected $db;
	protected $orderby = 'conversions';

	function __construct() {
		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'articles';
		$this->db->order = 'DESC';
		$this->db->limit = 50000;

		$this->from = date('Y-m-d', strtotime(DEFAULT_FROM));
		$this->to = date('Y-m-d', strtotime(DEFAULT_TO));

		if (Session::get('from')) {$this->from = Session::get('from');}
		if (Session::get('to')) {$this->to = Session::get('to');}
	}

	public function list() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		$limit = 2000;

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `articles`
			 WHERE DATE(`pubdate`) BETWEEN :startDate AND :endDate
			 ORDER BY pubdate DESC
			 LIMIT 0, $limit"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}
		return $output;

	}

	public function list_all() {
		$SQLstatement = $this->db->connection->prepare(
			"SELECT * FROM `articles` ORDER BY pubdate DESC"
		);
		$SQLstatement->execute();
		$output = $SQLstatement->fetchall();
		return $output;
	}

	public function list_unset() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `articles`
			 WHERE DATE(`pubdate`) BETWEEN :startDate AND :endDate AND type IS NULL
			 ORDER BY pubdate DESC
			 LIMIT 0, 100000"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}
		return $output;

	}

	public function list_no_audience() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `articles`
			 WHERE DATE(`pubdate`) BETWEEN :startDate AND :endDate AND audience IS NULL
			 ORDER BY pubdate DESC
			 LIMIT 0, 5000"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}
		return $output;

	}

	public function get_unset_ids() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT id FROM `articles` WHERE type IS NULL
			AND DATE(`pubdate`) BETWEEN :startDate AND :endDate AND type IS NULL
			LIMIT 0, 10000"
		);
		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall(\PDO::FETCH_COLUMN);
		return $output;
	}


	public function list_by($searchterm, $column, $order = 'pubdate') {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `articles`
			 WHERE `$column` = :term
			 AND DATE(`pubdate`) BETWEEN :startDate AND :endDate
			 ORDER BY $order DESC
			 LIMIT 0, 5000"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to, ':term' => $searchterm]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}
		return $output;

	}


	public function list_all_audiences() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `articles`
			 WHERE (`audience` is not NULL or `audience` != '')
			 AND DATE(`pubdate`) BETWEEN :startDate AND :endDate
			 ORDER BY 'pubdate' DESC
			 LIMIT 0, 25000"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}
		return $output;

	}	



	public function list_by_fuzzy($searchterm, $column, $order = 'pubdate') {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$searchterm = '%'.$searchterm.'%';

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `articles`
			 WHERE `$column` LIKE :term
			 AND DATE(`pubdate`) BETWEEN :startDate AND :endDate
			 ORDER BY $order DESC
			 LIMIT 0, 5000"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to, ':term' => $searchterm]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}
		return $output;

	}


	public function list_distinct($column) {

		$column = strip_tags($column);
		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT DISTINCT $column FROM `articles`
			 WHERE DATE(`pubdate`) BETWEEN :startDate AND :endDate
			 AND $column is not null
			 ORDER BY $column ASC"
		);
		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		return $SQLstatement->fetchall(\PDO::FETCH_COLUMN);

	}

	public function count_distinct($column) {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$distinct = $this->list_distinct($column);

		$out = [];
		foreach ($distinct as $group) {

			$SQLstatement = $this->db->connection->prepare(
				"SELECT count(id) as count FROM `articles`
				WHERE (DATE(`pubdate`) BETWEEN :startDate AND :endDate)
				AND $column = '$group'");
			$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);

			$count = $SQLstatement->fetch()['count'];
			$out[$group] = $count;

		}

		return $out;

	}

	public function bulk_change_cluster($oldClusterValue, $oldClusterGroup, $newClusterValue, $newClusterGroup) {

		if (empty($newClusterValue)) {throw new \Exception("Clustername darf nicht leer sein", 400);}
		if (empty($newClusterGroup)) {throw new \Exception("Neue Clustergruppe darf nicht leer sein", 400);}

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		$oldClusterGroup = strip_tags($oldClusterGroup);
		$newClusterGroup = strip_tags($newClusterGroup);

		// SQL Protection
		$oldClusterGroup = str_replace('--','', $oldClusterGroup);
		$oldClusterGroup = str_replace('#','', $oldClusterGroup);
		$oldClusterGroup = str_replace(';','', $oldClusterGroup);
		$newClusterGroup = str_replace('--','', $newClusterGroup);
		$newClusterGroup = str_replace('#','', $newClusterGroup);
		$newClusterGroup = str_replace(';','', $newClusterGroup);

		$SQLstatement = $this->db->connection->prepare(
			"UPDATE `articles`
			SET `$newClusterGroup` = :newClusterValue
			WHERE (DATE(`pubdate`) BETWEEN :startDate AND :endDate) AND `$oldClusterGroup` = :oldClusterValue");

		$SQLstatement->execute([
			':startDate' => $from,
			':endDate' => $to,
			':oldClusterValue' => $oldClusterValue,
			':newClusterValue' => $newClusterValue,
		]);

		return $SQLstatement->rowCount();

	}

	public function reset_cluster($clusterValue, $clusterGroup) {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		$clusterGroup = strip_tags($clusterGroup);
		$clusterGroup = str_replace('--','', $clusterGroup);
		$clusterGroup = str_replace('#','', $clusterGroup);
		$clusterGroup = str_replace(';','', $clusterGroup);

		$SQLstatement = $this->db->connection->prepare(
			"UPDATE `articles`
			SET `$clusterGroup` = NULL
			WHERE (DATE(`pubdate`) BETWEEN :startDate AND :endDate) AND `$clusterGroup` = :clusterValue");
		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to, ':clusterValue' => $clusterValue,]);
		return $SQLstatement->rowCount();

	}

	public function field($articleID, $fieldname) {
		return $this->get($articleID,$fieldname)[$fieldname];
	}

	public function sum_up($array, $key) {
		if (empty($array)) {return 0;}
		return array_sum(array_column($array,$key));
	}

	public function average_up($array, $key) {
		if (empty($array)) {return 0;}
		return array_sum(array_column($array,$key)) / count($array);
	}

	public function add_to_database($articles) {
		foreach ($articles as $article) {
			if (empty($article['id'])) {continue;}
			$this->create_or_update($article);
		}
	}

	public function add_stats($gaData, $id) {
		$stats = [
			'pageviews' => $gaData['Pageviews'] ?? 0,
			'sessions' => $gaData['Sessions'] ?? 0,
			'mediatime' => $gaData['Timeonpage'] ?? 0,
			'avgmediatime' => $gaData['Avgtimeonpage'] ?? 0,
			'refresh' => date('Y-m-d H:i:s'),
		];

		if (isset($gaData['buyintent'])) { $stats['buyintent'] = $gaData['buyintent']; }
		if (isset($gaData['subscriberviews'])) { $stats['subscriberviews'] = $gaData['subscriberviews']; }
		if (isset($gaData['Itemquantity'])) { $stats['conversions'] = $gaData['Itemquantity']; }

		$this->update($stats,$id);
	}

	public function plus_only(array $options = []) {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `articles`
			 WHERE `plus` = 1
			 AND DATE(`pubdate`) BETWEEN :startDate AND :endDate
			 ORDER BY `pubdate` DESC
			 LIMIT 0, 500"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}
		return $output;

	}

	public function conversions_only($limit=1000) {

		$limit = intval($limit);
		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `articles`
			 WHERE `conversions` > 0
			 AND DATE(`pubdate`) BETWEEN :startDate AND :endDate
			 ORDER BY `conversions` DESC
			 LIMIT 0, $limit"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}
		return $output;

	}

	public function pageviews_only($minimum = 1000, $limit=1000) {

		$minimum = intval($minimum);
		$limit = intval($limit);
		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `articles`
			 WHERE `pageviews` > $minimum
			 AND DATE(`pubdate`) BETWEEN :startDate AND :endDate
			 ORDER BY `pageviews` DESC
			 LIMIT 0, $limit"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}
		return $output;

	}

	public function mediatime_only($minimum = 150, $limit=1000) {

		$minimum = intval($minimum);
		$limit = intval($limit);
		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `articles`
			 WHERE `avgmediatime` > $minimum
			 AND DATE(`pubdate`) BETWEEN :startDate AND :endDate
			 ORDER BY `avgmediatime` DESC
			 LIMIT 0, $limit"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}
		return $output;

	}



	public function subscriber_only($limit=2000) {

		$limit = intval($limit);
		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `articles`
			 WHERE DATE(`pubdate`) BETWEEN :startDate AND :endDate
			 ORDER BY `subscriberviews` DESC
			 LIMIT 0, $limit"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}
		return $output;

	}


	public function kpi_grouped_by($kpi, $groupby = 'ressort', $operation = 'sum') {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		if ($operation) {
			// sum, count or average
			$kpi = $operation . '(' . $kpi . ') as ' . $kpi;
		}

		$SQLstatement = $this->db->connection->prepare(
			"SELECT $groupby, $kpi
			 FROM `articles`
			 WHERE DATE(`pubdate`) BETWEEN :startDate AND :endDate
			 GROUP BY $groupby
			 ORDER BY $groupby ASC"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();

		return $output;

	}


	public function by_date_range($start, $end = null) {

		if (is_null($end)) {$end = $start;}

		$start .= ' 00:00:00';
		$end .= ' 23:59:59';

		$SQLstatement = $this->db->connection->prepare(
			"SELECT id,pubdate
			 FROM `articles`
			 WHERE DATE(`pubdate`) BETWEEN :startDate AND :endDate
			 ORDER BY `pubdate` ASC
			 LIMIT 0, 300"
		);

		$SQLstatement->execute([':startDate' => $start, ':endDate' => $end]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}

		return $output;

	}

	public function conversions_only_by_date_range($start, $end = null) {

		if (is_null($end)) {$end = $start;}

		$start .= ' 00:00:00';
		$end .= ' 23:59:59';

		$SQLstatement = $this->db->connection->prepare(
			"SELECT id,pubdate
			 FROM `articles`
			 WHERE DATE(`pubdate`) BETWEEN :startDate AND :endDate
			 AND `conversions` > 0
			 ORDER BY `pubdate` ASC
			 LIMIT 0, 300"
		);

		$SQLstatement->execute([':startDate' => $start, ':endDate' => $end]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}

		return $output;

	}

	public function by_days_ago($daysAgo = 0) {

		$daysAgo = intval($daysAgo);
		$SQLstatement = $this->db->connection->prepare(
			"SELECT id,pubdate
			 FROM `articles`
			 WHERE DATE(`pubdate`) = CURDATE() - INTERVAL $daysAgo DAY
			 ORDER BY `pubdate` ASC
			 LIMIT 0, 500"
		);

		$SQLstatement->execute();
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}

		return $output;

	}

	public function by_weeks_ago($weeksAgo = 1) {

		$weeksAgo = intval($weeksAgo);
		$SQLstatement = $this->db->connection->prepare(
			"SELECT id,pubdate
			 FROM `articles`
			 WHERE YEARWEEK(`pubdate`,1) = YEARWEEK(NOW() - INTERVAL $weeksAgo WEEK,1)

			 ORDER BY `pubdate` ASC
			 LIMIT 0, 500"
		);

		$SQLstatement->execute();
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}

		return $output;

	}


	public function top_pageviews_days_ago($days = 1) {

		$from = date('Y-m-d', strtotime('-'.$days.'days'));
		$to = date('Y-m-d', strtotime('-1days'));

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `articles`
			 WHERE DATE(`pubdate`) BETWEEN :startDate AND :endDate
			 ORDER BY pageviews DESC
			 LIMIT 0, 5"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}
		return $output;

	}

	public function top_conversions_days_ago($days = 1) {

		$from = date('Y-m-d', strtotime('-'.$days.'days'));
		$to = date('Y-m-d', strtotime('-1days'));

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `articles`
			 WHERE (DATE(`pubdate`) BETWEEN :startDate AND :endDate)
			 AND conversions > 0
			 ORDER BY conversions DESC, pageviews DESC
			 LIMIT 0, 5"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}
		return $output;

	}


	public function top_articles_by_ressort_and_days_ago($ressorts = null, $days = 1) {

		$from = date('Y-m-d', strtotime('-'.$days.'days'));
		$to = date('Y-m-d', strtotime('-1days'));

		$ressortQuery = null;

		if ($ressorts) {
			$ressorts = explode_and_trim(',', $ressorts);
			foreach ($ressorts as $key => $ressort) {

				$ressort = "'" . $ressort . "'";
				if ($key == 0) {
					$ressortQuery = '&& (ressort = ' . $ressort;
				}
				else {$ressortQuery .= ' OR ressort = ' . $ressort;}
			}
			$ressortQuery .= ')';
		}

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `articles`
			 WHERE (DATE(`pubdate`) BETWEEN :startDate AND :endDate)
			 $ressortQuery
			 ORDER BY conversions DESC, pageviews DESC
			 LIMIT 0, 10"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}
		return $output;

	}

	public function audience_by_time($audience) {

		$SQLstatement = $this->db->connection->prepare(
			"SELECT DATE_FORMAT(pubdate,'%H') as hour, count(*) as articles
			 FROM `articles`
			 WHERE (DATE(`pubdate`) BETWEEN :startDate AND :endDate)
			 AND audience = :audience
			 GROUP BY hour
			 ORDER BY hour DESC
			 LIMIT 0, 1000"
		);

		$SQLstatement->execute([':startDate' => $this->from, ':endDate' => $this->to, ':audience' => $audience]);
		$output = $SQLstatement->fetchall(\PDO::FETCH_UNIQUE);
		if (empty($output)) {return null;}

		return $output;

	}

	public function produced_over_time($field, $periodIndicator = 'day') {

		switch ($periodIndicator) {
			case 'day':
				$set = $this->produced_groupby($field, '%Y-%m-%d');
				$range = $this->min_max_dates_from_array($set);
				$dates = $this->date_range($range['min'], $range['max'], 'Y-m-d');
				break;
			case 'weekday':
				$set = $this->produced_groupby($field, '%w');
				$dates = range(0,6);
				break;
			case 'hour':
				$set = $this->produced_groupby($field, '%k');
				$dates = range(0,23);
				break;							
			case 'week':
				$set = $this->produced_groupby($field, '%Y-%u');
				$range = $this->min_max_dates_from_array($set);
				$dates = $this->date_range($range['min'], $range['max'], 'Y-W');
				break;			
			case 'month':
				$set = $this->produced_groupby($field, '%Y-%m');
				$range = $this->min_max_dates_from_array($set);
				$dates = $this->date_range($range['min'], $range['max'], 'Y-m');
				break;
			default:
				break;
		}

		$out = [];
		foreach ($set as $fieldName => $data) {
		
			$articlesPerDay = array_column($data, 'articles', 'date');

			foreach ($dates as $date) {
				
				if ($periodIndicator == 'weekday') {
					$dayNames = ['So','Mo','Di','Mi','Do','Fr','Sa'];
					$out[$dayNames[$date]][$fieldName] = $articlesPerDay[$date] ?? 0;
					continue;
				}

				$out[$date][$fieldName] = $articlesPerDay[$date] ?? 0;

			}
		}

		if ($periodIndicator == 'weekday') {
			$sunday = array_shift($out);
			$out['So'] = $sunday;
		}

		return $out;

	}


	private function date_range($start, $end, $format = 'Y-m-d') {

		$periodFactor = 'P1D';

		if ($format == 'Y-m') {$periodFactor = 'P1M';}
		if ($format == 'Y-W') {
			$periodFactor = 'P1W';
			$start = explode('-', $start);
			$start = implode('W', $start);
			$end = explode('-', $end);
			$end = implode('W', $end);			
		}

        $start = new \DateTime($start);
        $end = new \DateTime($end . '+1day');

        $daterange = new \DatePeriod($start, new \DateInterval($periodFactor), $end);

        $dates = [];
        foreach($daterange as $date){
			array_push($dates, $date->format($format));
        }

        return $dates;

	}

	public function min_max_dates_from_array($array, $dateKey = 'date') {

		$min = null;
		$max = null;
		foreach ($array as $data) {
			$tempMax = max(array_column($data,'date'));
			$tempMin = min(array_column($data,'date'));
			if ($max <= $tempMax) {$max = $tempMax;}
			if (is_null($min)) {$min = $tempMin;}
			if ($tempMin <= $min) {$min = $tempMin;}
		}

		return ['min' => $min, 'max' => $max];

	}

	public function produced_groupby($field = 'type', $dateFormat = '%Y-%m-%d') {

		$SQLstatement = $this->db->connection->prepare(
			"SELECT $field, DATE_FORMAT(pubdate, '$dateFormat') as date ,count(*) as articles
			 FROM `articles`
			 WHERE (DATE(`pubdate`) BETWEEN :startDate AND :endDate)
			 AND $field is not null
			 GROUP BY $field, date
			 ORDER BY $field, date asc"
		);

		$SQLstatement->execute([':startDate' => $this->from, ':endDate' => $this->to]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}

		return array_group_by($field, $output);

	}

	public function produced_by($field = 'ressort', $dateFormat = '%Y-%m-%d') {

		$SQLstatement = $this->db->connection->prepare(
			"SELECT DATE_FORMAT(pubdate, '$dateFormat') as date ,count(*) as articles, $field
			 FROM `articles`
			 WHERE (DATE(`pubdate`) BETWEEN :startDate AND :endDate)
			 GROUP BY date, $field
			"
		);

		$SQLstatement->execute([':startDate' => $this->from, ':endDate' => $this->to]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}

		$output = array_group_by($field, $output);
		
		foreach ($output as $key => $value) {
			$output[$key] = array_column($value, 'articles', 'date');
		}

		return $output;

	}

	public function score_articles($minScore = 100, $filter = null) {

		$minScore = intval($minScore);
		if (!is_null($filter)) {$filter = "AND ($filter)";}

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *,
			 round(sum(
			 	(IFNULL(conversions, 0) * 20) + (IFNULL(pageviews, 0) / 1000 * 5) +
			 	((IFNULL(avgmediatime, 0) / 10) * 2) + (IFNULL(subscriberviews, 0) / 100 * 3)
			 ),1) as score
			 FROM `articles`
			 WHERE (DATE(`pubdate`) BETWEEN :startDate AND :endDate)
			 AND pageviews >= 100
			 $filter
			 GROUP BY id
			 HAVING score >= $minScore
			 ORDER BY score DESC, pageviews DESC
			 LIMIT 0, 1000"
		);

		$SQLstatement->execute([':startDate' => $this->from, ':endDate' => $this->to]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}

		return $output;

	}


	public function value_articles($group = 'ressort') {

		$SQLstatement = $this->db->connection->prepare(
			"SELECT $group,
			$group,
			 COUNT( if(conversions>0 AND subscriberviews>=100, 1, NULL) ) as spielmacher,
			 COUNT( if(conversions>0 AND (subscriberviews IS NULL OR subscriberviews<100), 1, NULL) ) as stuermer,
			 COUNT( if((conversions IS NULL OR conversions=0) AND subscriberviews>=100, 1, NULL) ) as abwehr,
			 COUNT( if((conversions IS NULL OR conversions=0) AND (subscriberviews IS NULL OR subscriberviews<100), 1, NULL) ) as geister,
			 COUNT(id) as artikel

			 FROM `articles`
			 WHERE (DATE(`pubdate`) BETWEEN :startDate AND :endDate)
			 AND $group != '' AND ressort != 'bilder' AND ressort != 'ratgeber'
			 GROUP BY $group
			 ORDER BY spielmacher DESC
			 LIMIT 0, 10000"
		);

		$SQLstatement->execute([':startDate' => $this->from, ':endDate' => $this->to]);
		$output = $SQLstatement->fetchall(\PDO::FETCH_UNIQUE);
		if (empty($output)) {return null;}

		return $output;

	}

	public function value_articles_by_audience() {

		$SQLstatement = $this->db->connection->prepare(
			"SELECT audience,
			audience,
			 COUNT( if(conversions>0 AND subscriberviews>=100, 1, NULL) ) as spielmacher,
			 COUNT( if(conversions>0 AND (subscriberviews IS NULL OR subscriberviews<100), 1, NULL) ) as stuermer,
			 COUNT( if((conversions IS NULL OR conversions=0) AND subscriberviews>=100, 1, NULL) ) as abwehr,
			 COUNT( if((conversions IS NULL OR conversions=0) AND (subscriberviews IS NULL OR subscriberviews<100), 1, NULL) ) as geister,
			 COUNT(id) as artikel

			 FROM `articles`
			 WHERE (DATE(`pubdate`) BETWEEN :startDate AND :endDate)
			 AND audience != ''
			 GROUP BY audience
			 ORDER BY spielmacher DESC
			 LIMIT 0, 1000"
		);

		$SQLstatement->execute([':startDate' => $this->from, ':endDate' => $this->to]);
		$output = $SQLstatement->fetchall(\PDO::FETCH_UNIQUE);
		if (empty($output)) {return null;}

		return $output;

	}

	public function valueables_by_group($group) {

		$filter = 'AND (conversions IS NULL OR conversions=0) AND (subscriberviews IS NULL OR subscriberviews<100)';
		if ($group == 'stuermer') {$filter = 'AND conversions>0 AND (subscriberviews IS NULL OR subscriberviews<100)';}
		if ($group == 'abwehr') {$filter = 'AND (conversions IS NULL OR conversions=0) AND subscriberviews>=100';}
		if ($group == 'spielmacher') {$filter = 'AND conversions>0 AND subscriberviews>=100';}

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		$limit = 5000;

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `articles`
			 WHERE DATE(`pubdate`) BETWEEN :startDate AND :endDate
			 AND ressort != '' AND ressort != 'bilder' AND ressort != 'ratgeber'
			 $filter
			 ORDER BY pubdate DESC
			 LIMIT 0, $limit"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}
		return $output;

	}


	public function stats_grouped_by($column = 'ressort', $orderby = 'conversions DESC, ressort ASC') {

		$column = strip_tags($column);
		$orderby = strip_tags($orderby);
		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(

			"SELECT $column,

       		(SELECT count(id)
        	FROM `articles` AS temptable
        	WHERE temptable.$column = maintable.$column
			AND DATE(`pubdate`) BETWEEN :startDate AND :endDate
			) as artikel,

       		(SELECT count(id)
        	FROM `articles` AS temptable
        	WHERE temptable.$column = maintable.$column
			AND `plus` IS NULL AND DATE(`pubdate`) BETWEEN :startDate AND :endDate) AS free,

       		(SELECT count(id)
        	FROM `articles` AS temptable
        	WHERE temptable.$column = maintable.$column
			AND `plus` = 1 AND DATE(`pubdate`) BETWEEN :startDate AND :endDate) as plus,

       		(SELECT sum(pageviews)
        	FROM `articles` AS temptable
        	WHERE temptable.$column = maintable.$column
			AND `pageviews` > 0 AND DATE(`pubdate`) BETWEEN :startDate AND :endDate) as pageviews,

       		(SELECT sum(subscriberviews)
        	FROM `articles` AS temptable
        	WHERE temptable.$column = maintable.$column
			AND `subscriberviews` > 0 AND DATE(`pubdate`) BETWEEN :startDate AND :endDate) as subscriberviews,

       		(SELECT sum(mediatime)
        	FROM `articles` AS temptable
        	WHERE temptable.$column = maintable.$column
			AND `mediatime` > 0 AND DATE(`pubdate`) BETWEEN :startDate AND :endDate) as mediatime,

       		(SELECT avg(avgmediatime)
        	FROM `articles` AS temptable
        	WHERE temptable.$column = maintable.$column
			AND `avgmediatime` > 0 AND DATE(`pubdate`) BETWEEN :startDate AND :endDate) as avgmediatime,

       		(SELECT sum(sessions)
        	FROM `articles` AS temptable
        	WHERE temptable.$column = maintable.$column
			AND `sessions` > 0 AND DATE(`pubdate`) BETWEEN :startDate AND :endDate) as sessions,

       		(SELECT sum(cancelled)
        	FROM `articles` AS temptable
        	WHERE temptable.$column = maintable.$column
			AND `cancelled` > 0 AND DATE(`pubdate`) BETWEEN :startDate AND :endDate) as cancelled,

       		(SELECT sum(buyintent)
        	FROM `articles` AS temptable
        	WHERE temptable.$column = maintable.$column
			AND `buyintent` > 0 AND DATE(`pubdate`) BETWEEN :startDate AND :endDate) as buyintents,

       		(SELECT sum(conversions)
        	FROM `articles` AS temptable
        	WHERE temptable.$column = maintable.$column
			AND `conversions` > 0 AND DATE(`pubdate`) BETWEEN :startDate AND :endDate) as conversions

			FROM `articles` AS maintable
			WHERE DATE(`pubdate`) BETWEEN :startDate AND :endDate
			GROUP BY $column ORDER BY $orderby");

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);

		$output = $SQLstatement->fetchall(\PDO::FETCH_UNIQUE);
		if (empty($output)) {return null;}

		unset($output['']); // somehow there´s some null Type exported

		return $output;

	}


	public function free_articles_by_ressort() {

		$SQLstatement = $this->db->connection->prepare(
			"SELECT ressort, count(*)
			 FROM `articles`
			 WHERE `plus` IS NULL
			 GROUP BY ressort
			 ORDER BY count(*) DESC"
		);

		$SQLstatement->execute();
		$output = $SQLstatement->fetchall(\PDO::FETCH_KEY_PAIR);
		if (empty($output)) {return null;}
		return $output;

	}

	public function audiences_by_ressort() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$audiences = $this->list_distinct('audience');

		$audienceArray = [];

		foreach ($audiences as $audience) {

			$SQLstatement = $this->db->connection->prepare(
				"SELECT ressort, count(*)
				 FROM `articles`
				 WHERE `audience` = '$audience'
				 AND DATE(`pubdate`) BETWEEN :startDate AND :endDate
				 GROUP BY ressort
				 ORDER BY count(*) DESC"
			);

			$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
			$output = $SQLstatement->fetchall(\PDO::FETCH_KEY_PAIR);
			$audienceArray[$audience] = $output;
		}

		return $audienceArray;

	}


	public function cluster_by_ressort($cluster = 'audience') {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$clusters = $this->list_distinct($cluster);

		$output = [];

		foreach ($clusters as $clusterName) {

			$SQLstatement = $this->db->connection->prepare(
				"SELECT ressort, count(id) as articles, sum(subscriberviews) as subscriberviews, sum(conversions) as conversions, sum(pageviews) as pageviews
				 FROM `articles`
				 WHERE `$cluster` = '$clusterName'
				 AND DATE(`pubdate`) BETWEEN :startDate AND :endDate
				 GROUP BY ressort
				 ORDER BY sum(subscriberviews) DESC"
			);

			$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
			$dbData = $SQLstatement->fetchall(\PDO::FETCH_UNIQUE);
			$output[$clusterName] = $dbData;
		}

		return $output;

	}

	public function paid_articles_by_ressort() {

		$SQLstatement = $this->db->connection->prepare(
			"SELECT ressort, count(*)
			 FROM `articles`
			 WHERE `plus` = 1
			 AND DATE(`pubdate`) BETWEEN :startDate AND :endDate
			 GROUP BY ressort
			 ORDER BY count(*) DESC"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall(\PDO::FETCH_KEY_PAIR);
		if (empty($output)) {return null;}
		return $output;

	}

	public function count($field = '*', $filter = '') {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		$field = strip_tags($field);

		if ($filter != '') {
			$filter = strip_tags($filter);
			$filter = 'AND ' . $filter . ' is not null';
		}

		$SQLstatement = $this->db->connection->prepare("SELECT count($field) FROM `articles` WHERE (DATE(`pubdate`) BETWEEN :startDate AND :endDate) $filter");
		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);

		$output = $SQLstatement->fetch();
		if (empty($output)) {return null;}
		return $output['count('.$field.')'];
	}

	public function count_with_filter($filter = null) {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT count(*) as amount FROM `articles`
			WHERE (DATE(`pubdate`) BETWEEN :startDate AND :endDate)
			AND $filter");
		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetch();
		if (empty($output)) {return null;}
		return $output['amount'];
	}


	public function sum($field, $filter = '') {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		$field = strip_tags($field);

		if ($filter != '') {
			$filter = strip_tags($filter);
			$filter = 'AND ' . $filter . ' is not null';
		}

		$SQLstatement = $this->db->connection->prepare("SELECT sum($field) FROM `articles` WHERE (DATE(`pubdate`) BETWEEN :startDate AND :endDate) $filter");
		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);

		$output = $SQLstatement->fetch();
		if (empty($output)) {return null;}

		return $output['sum('.$field.')'];
	}

	public function average($field, $filter = '') {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		$field = strip_tags($field);

		if ($filter != '') {
			$filter = strip_tags($filter);
			$filter = 'AND ' . $filter . ' is not null';
		}

		$SQLstatement = $this->db->connection->prepare("SELECT avg($field) FROM `articles` WHERE (DATE(`pubdate`) BETWEEN :startDate AND :endDate) $filter");
		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);

		$output = $SQLstatement->fetch();
		if (empty($output)) {return null;}

		return $output['avg('.$field.')'];
	}

}
