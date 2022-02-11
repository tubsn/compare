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
			"SELECT DATE_FORMAT(date,'%Y-%m') as date,
			 paying, trial1month, trial3for3, reduced, yearly, orders, cancelled, (0-'cancelled') as negativcancelled,
			 app_user_android, app_user_ios, (app_user_android + app_user_ios) as app_user,
			 IFNULL(paying,0)+IFNULL(trial3for3,0)+IFNULL(trial1month,0)+IFNULL(yearly,0) as active FROM $tablename
			ORDER BY date ASC"
		);

		$SQLstatement->execute();
		$output = $SQLstatement->fetchAll(\PDO::FETCH_UNIQUE);
		return $output;

	}


}
