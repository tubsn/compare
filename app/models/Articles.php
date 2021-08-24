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

		$this->from = date('Y-m-d', strtotime('yesterday -6days'));
		$this->to = date('Y-m-d', strtotime('yesterday'));

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
			 LIMIT 0, 5000"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}
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
		$this->save_to_db($articles);
	}


	public function add_stats($gaData, $id) {
		$stats = [
			'pageviews' => $gaData['Pageviews'] ?? 0,
			'sessions' => $gaData['Sessions'] ?? 0,
			'subscribers' => $gaData['subscribers'] ?? 0,
			'buyintent ' => $gaData['buyintent'] ?? null,
			'conversions' => $gaData['Itemquantity'] ?? 0,
			'mediatime' => $gaData['Timeonpage'] ?? 0,
			'avgmediatime' => $gaData['Avgtimeonpage'] ?? 0,
			'refresh' => date('Y-m-d H:i:s'),
		];

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
			 ORDER BY `subscribers` DESC
			 LIMIT 0, $limit"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		if (empty($output)) {return null;}
		return $output;

	}

	public function subscribers_for_chart() {
		$rawData = $this->subscribers_by_date();

		$subscribers = null; $dates = null;
		foreach ($rawData as $data) {
			$subscribers .= $data['subscribers'] . ',';
			$dates .= "'" . $data['day'] . "'" . ',';
		}

		$chart['amount'] = rtrim($subscribers, ',');
		$chart['dates'] = rtrim($dates, ',');
		$chart['color'] = '#314e6f';
		$chart['name'] = 'Subscribers';

		return $chart;
	}

	public function subscribers_by_date() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT sum(subscribers) as 'subscribers', DATE(`pubdate`) as 'day'
			 FROM `articles`
			 WHERE DATE(`pubdate`) BETWEEN :startDate AND :endDate
			 GROUP BY day
			 ORDER BY pubdate ASC"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		return $output;

	}



	public function subscribers_by_ressort_chart() {
		$rawData = $this->subscribers_by_ressort();

		$subscribers = null; $ressorts = null;
		foreach ($rawData as $data) {
			$subscribers .= $data['subscribers'] . ',';
			$ressorts .= "'" . ucfirst($data['ressort']) . "'" . ',';
		}

		$chart['amount'] = rtrim($subscribers, ',');
		$chart['dates'] = rtrim($ressorts, ',');
		$chart['color'] = '#314e6f'; // lighter? #6e94bd
		$chart['name'] = 'Subscribers';

		return $chart;
	}


	public function subscribers_by_ressort() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT ressort, sum(subscribers) as 'subscribers'
			 FROM `articles`
			 WHERE DATE(`pubdate`) BETWEEN :startDate AND :endDate
			 GROUP BY ressort
			 ORDER BY ressort ASC"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		return $output;

	}


	public function mediatime_by_ressort_chart() {
		$rawData = $this->mediatime_by_ressort();

		$mediatime = null; $ressorts = null;
		foreach ($rawData as $data) {
			$mediatime .= $data['mediatime'] . ',';
			$ressorts .= "'" . ucfirst($data['ressort']) . "'" . ',';
		}

		$chart['amount'] = rtrim($mediatime, ',');
		$chart['dates'] = rtrim($ressorts, ',');
		$chart['color'] = '#6ea681';
		$chart['name'] = 'Mediatime';

		return $chart;
	}


	public function mediatime_by_ressort() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT ressort, sum(mediatime) as mediatime
			 FROM `articles`
			 WHERE DATE(`pubdate`) BETWEEN :startDate AND :endDate
			 GROUP BY ressort
			 ORDER BY ressort ASC"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();

		return $output;

	}

	public function pageviews_by_ressort_chart() {
		$rawData = $this->pageviews_by_ressort();

		$pageviews = null; $ressorts = null;
		foreach ($rawData as $data) {
			$pageviews .= $data['pageviews'] . ',';
			$ressorts .= "'" . ucfirst($data['ressort']) . "'" . ',';
		}

		$chart['amount'] = rtrim($pageviews, ',');
		$chart['dates'] = rtrim($ressorts, ',');
		$chart['color'] = '#6088b4';
		$chart['name'] = 'Pageviews';

		return $chart;
	}


	public function pageviews_by_ressort() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT ressort, sum(pageviews) as pageviews
			 FROM `articles`
			 WHERE DATE(`pubdate`) BETWEEN :startDate AND :endDate
			 GROUP BY ressort
			 ORDER BY ressort ASC"
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

		/* Date Stuff
	 	#WHERE `pubdate` >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY
		#AND `pubdate` < curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY

		WHERE MONTH(columnName) = MONTH(CURRENT_DATE())
		AND YEAR(columnName) = YEAR(CURRENT_DATE())

		Vor 2 Wochen
		WHERE YEARWEEK(`pubdate`,1) = YEARWEEK(NOW() - INTERVAL 2 WEEK,1)
		*/

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
				if ($key == 0) {
					$ressortQuery = "AND (ressort = '" . $ressort . "'";
				}
				else {$ressortQuery .= " OR ressort = '" . $ressort . "'";}
			}
			$ressortQuery .= ')';
		}

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `articles`
			 WHERE (DATE(`pubdate`) BETWEEN :startDate AND :endDate)
			 $ressortQuery
			 ORDER BY conversions DESC, pageviews DESC
			 LIMIT 0, 5"
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

		$cacheExpireMinutes = 1;
		$statsCache = new RequestCache($column, $cacheExpireMinutes * 60);
		$cachedData = $statsCache->get();
		if ($cachedData) {return $cachedData;}

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

       		(SELECT sum(subscribers)
        	FROM `articles` AS temptable
        	WHERE temptable.$column = maintable.$column
			AND `subscribers` > 0 AND DATE(`pubdate`) BETWEEN :startDate AND :endDate) as subscribers,

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

		$statsCache->save($output);

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

	public function count($field = '*') {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		$field = strip_tags($field);

		$SQLstatement = $this->db->connection->prepare("SELECT count($field) FROM `articles` WHERE DATE(`pubdate`) BETWEEN :startDate AND :endDate");
		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);

		$output = $SQLstatement->fetch();
		if (empty($output)) {return null;}
		return $output['count('.$field.')'];
	}


	public function sum($field) {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		$field = strip_tags($field);

		$SQLstatement = $this->db->connection->prepare("SELECT sum($field) FROM `articles` WHERE DATE(`pubdate`) BETWEEN :startDate AND :endDate");
		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);

		$output = $SQLstatement->fetch();
		if (empty($output)) {return null;}

		return $output['sum('.$field.')'];
	}

	public function average($field) {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		$field = strip_tags($field);

		$SQLstatement = $this->db->connection->prepare("SELECT avg($field) FROM `articles` WHERE DATE(`pubdate`) BETWEEN :startDate AND :endDate");
		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);

		$output = $SQLstatement->fetch();
		if (empty($output)) {return null;}

		return $output['avg('.$field.')'];
	}


	private function save_to_db(array $articles) {

		if (count($articles) < 1 ) {return null;}

		// Implode Fieldnames and add `Backticks`
		$fieldNames = array_keys($articles[0]);
		$PDOValueFieldNames = preg_filter('/^/', ':', $fieldNames);
		$fieldNames = preg_filter('/^/', '`', $fieldNames);
		$fieldNames = preg_filter('/$/', '`', $fieldNames);
		$fields = '(' . implode($fieldNames,',') . ')';
		$PDOValueFieldNames = '(' . implode($PDOValueFieldNames,', ') . ')';

		$updateFields = [];
		foreach ($fieldNames as $key => $name) {
			$updateFields[$key] = $name . '=VALUES(' . $name . ')';
		}

		$updateFields = implode($updateFields,', ');

		$db = $this->db->connection;
		$stmt = $db->prepare(
			"INSERT INTO `articles` $fields
			VALUES $PDOValueFieldNames
			ON DUPLICATE KEY UPDATE $updateFields"
		);

		foreach ($articles as $article) {

			$valuesForBinding = [];
			foreach ($article as $key => $value) {
				if (empty($value)) {
					$value = null;
				}
				$valuesForBinding[':'.$key] = $value;
			}

			try {
				$stmt->execute($valuesForBinding);
			} catch (\Exception $e) {
				echo $e;
			}

		}

		return $stmt->rowCount();
	}

}
