<?php

namespace app\models;
use \flundr\database\SQLdb;
use \flundr\mvc\Model;
use \flundr\utility\Session;

class DailyKPIs extends Model
{

	public $from = '0000-00-00';
	public $to = '3000-01-01';


	function __construct() {
		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'daily_kpis';
		$this->db->primaryIndex = 'date';
		$this->db->orderby = 'date';

		$this->from = date('Y-m-d', strtotime('yesterday -6days'));
		$this->to = date('Y-m-d', strtotime('yesterday'));

		if (Session::get('from')) {$this->from = Session::get('from');}
		if (Session::get('to')) {$this->to = Session::get('to');}
	}

	public function stats() {

		$tablename = $this->db->table;
		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT sum(pageviews) as pageviews, sum(subscribers) as subscribers, sum(mediatime) as mediatime, avg(avgmediatime) as avgmediatime
			 FROM $tablename
			 WHERE DATE(`date`) BETWEEN :startDate AND :endDate"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetch();
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



	public function import($daysAgo = 5) {

		$ga = new Analytics();
		$gaKPIs = $ga->page_data($daysAgo);
		$gaKPIs = array_map([$this, 'ga_adapter'], $gaKPIs);

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

		foreach ($dates as $date => $subscribers) {
			if (empty($date)) {continue;}
			$set['date'] = $date;
			$set['subscribers'] = $subscribers;
			$this->create_or_update($set);
		}

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
