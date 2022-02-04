<?php

namespace app\models;
use \flundr\database\SQLdb;
use \flundr\mvc\Model;
use \flundr\utility\Session;

class SalesKPIs extends Model
{

	public $from = '0000-00-00';
	public $to = '3000-01-01';

	function __construct() {
		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'sales_kpis';
		$this->db->primaryIndex = 'date';
		$this->db->orderby = 'date';

		$this->from = date('Y-m-d', strtotime('yesterday -6days'));
		$this->to = date('Y-m-d', strtotime('yesterday'));

		if (Session::get('from')) {$this->from = Session::get('from');}
		if (Session::get('to')) {$this->to = Session::get('to');}
	}


	public function list() {

		$tablename = $this->db->table;

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *, IFNULL(paying,0)+IFNULL(trial3for3,0)+IFNULL(trial1month,0)+IFNULL(yearly,0) as active FROM $tablename
			ORDER BY date ASC"
		);

		$SQLstatement->execute();
		$output = $SQLstatement->fetchAll(\PDO::FETCH_UNIQUE);
		return $output;

	}



	public function list2() {

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




}
