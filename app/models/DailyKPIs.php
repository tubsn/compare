<?php

namespace app\models;
use \flundr\database\SQLdb;
use \flundr\mvc\Model;
use \flundr\utility\Session;
use app\models\Orders;

class DailyKPIs extends Model
{

	public $from = '0000-00-00';
	public $to = '3000-01-01';


	function __construct() {
		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'daily_kpis';
		$this->db->primaryIndex = 'date';
		$this->db->orderby = 'date';

		$this->from = date('Y-m-d', strtotime(DEFAULT_FROM));
		$this->to = date('Y-m-d', strtotime(DEFAULT_TO));

		if (Session::get('from')) {$this->from = Session::get('from');}
		if (Session::get('to')) {$this->to = Session::get('to');}
	}

	public function stats() {

		$tablename = $this->db->table;
		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT sum(pageviews) as pageviews, sum(subscriberviews) as subscriberviews, sum(mediatime) as mediatime, avg(avgmediatime) as avgmediatime
			 FROM $tablename
			 WHERE DATE(`date`) BETWEEN :startDate AND :endDate"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetch();
		if (empty($output)) {return null;}
		return $output;

	}


	private function timeframe($from, $to) {
		$from = new \DateTime($from);
		$to = new \DateTime($to);
		$timeframe = $from->diff($to);
		$days = $timeframe->format('%a');
		return $days;
	}


	public function segments() {

		$tablename = $this->db->table;
		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$days = $this->timeframe($from, $to);

		if ($days < 31) {

			$SQLstatement = $this->db->connection->prepare(
				"SELECT
				    #DATE_FORMAT(date,'%Y-%V') as datum,
				    date as datum,
					champions, champions_reg,
					high_usage_irregulars, high_usage_irregulars_reg,
					low_usage_irregulars, low_usage_irregulars_reg,
					loyals, loyals_reg, flybys, nonengaged, nonengaged_reg, unknown, unknown_reg

				 FROM $tablename
				 WHERE DATE(`date`) BETWEEN :startDate AND :endDate
				# GROUP BY datum
				 ORDER BY date ASC"
			);

		}

		else {

			$SQLstatement = $this->db->connection->prepare(
				"SELECT
				    DATE_FORMAT(date,'%Y-%v') as datum,
				    date as datum,
					sum(champions) as champions,
					sum(high_usage_irregulars) as high_usage_irregulars,
					sum(low_usage_irregulars) as low_usage_irregulars,
					sum(loyals) as loyals,
					sum(flybys) as flybys,
					sum(nonengaged) as nonengaged,
					sum(unknown) as unknown

				 FROM $tablename
				 WHERE DATE(`date`) BETWEEN :startDate AND :endDate
				 GROUP BY datum
				 HAVING champions IS NOT NULL OR unknown IS NOT NULL
				 ORDER BY date ASC"
			);

		}

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchAll(\PDO::FETCH_UNIQUE);
		if (empty($output)) {return [];}
		return $output;

	}

	public function premium_users() {

		$tablename = $this->db->table;
		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$days = $this->timeframe($from, $to);

		if ($days < 31) {

			$SQLstatement = $this->db->connection->prepare(
				"SELECT date as datum, sum(users) as users, sum(subscribers) as subscribers
				 FROM $tablename
				 WHERE DATE(`date`) BETWEEN :startDate AND :endDate
				 GROUP BY datum
				 HAVING users IS NOT NULL OR subscribers IS NOT NULL
				 ORDER BY date ASC"
			);

		}

		else {

			$SQLstatement = $this->db->connection->prepare(
				"SELECT
				    DATE_FORMAT(date,'%Y-%m') as datum,
					round(avg(users)) AS users,	round(avg(subscribers)) AS subscribers
				 FROM $tablename
				 WHERE DATE(`date`) BETWEEN :startDate AND :endDate
				 GROUP BY datum
				 HAVING users IS NOT NULL OR subscribers IS NOT NULL
				 ORDER BY date ASC"
			);

		}

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchAll(\PDO::FETCH_UNIQUE);
		if (empty($output)) {return [];}
		return $output;

	}


	public function quote_of_premium_users() {

		$tablename = $this->db->table;
		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$days = $this->timeframe($from, $to);

		if ($days < 31) {

			$SQLstatement = $this->db->connection->prepare(
				"SELECT
				    date as datum,
					round(avg(subscribers) /
					avg(users) * 100 ,2) as reg_quote
				 FROM $tablename
				 WHERE DATE(`date`) BETWEEN :startDate AND :endDate
				 GROUP BY datum
				 ORDER BY date ASC"
			);

		}

		elseif ($days < 300) {

			$SQLstatement = $this->db->connection->prepare(
				"SELECT
				    DATE_FORMAT(date,'%Y-%v') as datum,
					round(
						avg(subscribers) /
						avg(users) * 100, 2
					) as reg_quote
				 FROM $tablename
				 WHERE DATE(`date`) BETWEEN :startDate AND :endDate
				 GROUP BY datum
				 HAVING reg_quote IS NOT NULL
				 ORDER BY date ASC"
			);

		}

		else {

			$SQLstatement = $this->db->connection->prepare(
				"SELECT
				    DATE_FORMAT(date,'%Y-%m') as datum,
					round(avg(subscribers) /
					avg(users) * 100 ,2) as reg_quote
				 FROM $tablename
				 WHERE DATE(`date`) BETWEEN :startDate AND :endDate
				 GROUP BY datum
				 HAVING reg_quote IS NOT NULL
				 ORDER BY date ASC"
			);

		}

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchAll(\PDO::FETCH_UNIQUE);
		if (empty($output)) {return null;}
		return $output;

	}




	public function segments_quote() {

		$tablename = $this->db->table;
		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT date,
			ROUND(champions / users * 100,3) as champions,
			ROUND(high_usage_irregulars / (users) * 100,3) as high_usage_irregulars,
			ROUND(flybys / (users) * 100,3) as flybys,
			ROUND(low_usage_irregulars / (users) * 100,3) as low_usage_irregulars,
			ROUND(loyals / (users) * 100,3) as loyals,
			ROUND(nonengaged / (users) * 100,3) as nonengaged

			FROM $tablename

			WHERE DATE(`date`) BETWEEN :startDate AND :endDate
			ORDER BY date ASC"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchAll(\PDO::FETCH_UNIQUE);
		if (empty($output)) {return null;}
		return $output;

	}


	public function list() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		$tablename = $this->db->table;

		$SQLstatement = $this->db->connection->prepare(
			"SELECT * FROM $tablename
			WHERE DATE(`date`) BETWEEN :startDate AND :endDate
			ORDER BY date DESC"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchAll();
		return $output;
	}

	public function sum($field) {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		$tablename = $this->db->table;

		$SQLstatement = $this->db->connection->prepare(
			"SELECT sum($field) as $field FROM $tablename
			WHERE DATE(`date`) BETWEEN :startDate AND :endDate"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetch();
		return $output[$field];
	}

	public function avg($field) {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		$tablename = $this->db->table;

		$SQLstatement = $this->db->connection->prepare(
			"SELECT avg($field) as $field FROM $tablename
			WHERE DATE(`date`) BETWEEN :startDate AND :endDate"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetch();
		return $output[$field];
	}


	public function kpi_grouped_by($kpi, $groupby = "DATE_FORMAT(date,'%Y-%m')", $operation = 'sum') {

		$tablename = $this->db->table;
		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		if ($operation) {
			// sum, count or average
			$kpi = $operation . '(' . $kpi . ') as ' . $kpi;
		}

		$SQLstatement = $this->db->connection->prepare(
			"SELECT $groupby, $kpi
			 FROM $tablename
			 WHERE DATE(`date`) BETWEEN :startDate AND :endDate
			 GROUP BY $groupby
			 ORDER BY $groupby ASC"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();

		return $output;

	}

	public function switch_db($dbSettings) {
		$this->db = new SQLdb($dbSettings);
		$this->db->table = 'daily_kpis';
		$this->db->primaryIndex = 'date';
		$this->db->orderby = 'date';
	}

	public function mediatime() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		$tablename = $this->db->table;

		$SQLstatement = $this->db->connection->prepare(
			"SELECT date, avgmediatime FROM $tablename
			WHERE DATE(`date`) BETWEEN :startDate AND :endDate"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchAll();
		return $output;
	}


	public function import($daysAgo = 5) {

		$ga = new Analytics();
		$gaKPIs = $ga->page_data($daysAgo);
		$gaKPIs = array_map([$this, 'ga_adapter'], $gaKPIs);

		//dd($gaKPIs);

		foreach ($gaKPIs as $data) {
			if ($this->get($data['date'])) {
				$this->update($data, $data['date']);
			}
			else {
				$this->create($data);
			}
		}

	}

	public function import_subscribers($from, $to) {

		$kilkaya = new Kilkaya();
		$dates = $kilkaya->subscribers_grouped_by_date($from, $to);

		if (!is_array($dates)) {
			throw new \Exception("Import Failed", 404);
		}

		foreach ($dates as $date => $subscriberviews) {
			if (empty($date)) {continue;}
			$set['date'] = $date;
			$set['subscriberviews'] = $subscriberviews;
			$this->create_or_update($set);
		}

	}


	public function count_conversiontable_to_daily_kpis($from, $to) {

		$Orders = new Orders();

		$start = $this->create_date_object($from);
		$end = $this->create_date_object($to);
		$end->setTime(0,0,1); // set Date to +1second / next day to include itself 

		$interval = new \DateInterval('P1D');
		$period = new \DatePeriod($start, $interval, $end);

		$monthList = [];
		foreach ($period as $date) {
			$date = $date->format('Y-m-d');

			$Orders->from = $date;
			$Orders->to = $date;
			$conversions = $Orders->count();
			$this->update(['conversions' => $conversions],$date);

		}


	}

	private function create_date_object($dateString = null) {
		if (is_null($dateString)) {return new \DateTime(date('Y-m-d'));}
		return new \DateTime(date('Y-m-d', strtotime($dateString)));
	}




	private function ga_adapter($in) {
		$out['date'] = date('Y-m-d', strtotime($in['Date']));
		$out['pageviews'] = $in['Pageviews'] ?? 0;
		$out['sessions'] = $in['Sessions'] ?? 0;
		$out['mediatime'] = $in['Timeonpage'] ?? 0;
		$out['avgmediatime'] = $in['Avgtimeonpage'] ?? 0;
		return $out;
	}


}
