<?php

namespace app\models;
use \flundr\database\SQLdb;
use \flundr\mvc\Model;
use \flundr\utility\Session;
use flundr\cache\RequestCache;

class Longterm extends Model
{

	public $from = '0000-00-00';
	public $to = '3000-01-01';

	private $articleID;
	protected $db;

	function __construct() {
		/*
		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'longterm_kpis';
		$this->db->orderby = 'date';
		$this->db->order = 'DESC';
		*/

		$this->Orders = new Orders();

	}

	public function chartdata($kpi) {

		switch ($kpi) {
			case 'orders': $data = $this->orders(); break;
			default: break;
		}

		$charts = new Charts();
		return $charts->convert($data);

	}


	public function orders($start = null) {

		$cache = new RequestCache('cancellcationdata' . $start . PORTAL, 5*60);
		$cachedData = $cache->get();
		if ($cachedData) {return $cachedData;}

		if (is_null($start)) {$start = '2021-04-01';}
		$periods = $this->monthly_date_range($start);

		$output = [];
		foreach ($periods as $period) {

			$dimension = $period['month']->format('Y-m');

			$this->Orders->from = $period['from'];
			$this->Orders->to = $period['to'];

			$orders = $this->Orders->count();
			$cancelled = $this->Orders->count_cancelled();

			$churnSameDay = $this->Orders->sum_up($this->Orders->cancelled_by_retention_days('retention = 0'),'cancelled_orders');
			$churn30 = $this->Orders->sum_up($this->Orders->cancelled_by_retention_days('retention < 31'),'cancelled_orders');
			$churn90 = $this->Orders->sum_up($this->Orders->cancelled_by_retention_days('retention < 90'),'cancelled_orders');
			$churnAfter90 = $this->Orders->sum_up($this->Orders->cancelled_by_retention_days('retention > 90'),'cancelled_orders');

			$output[$dimension]['orders'] = $orders;
			$output[$dimension]['cancelled'] = $cancelled;
			$output[$dimension]['cancelledNegative'] = $cancelled * -1;
			$output[$dimension]['active'] = $orders - $cancelled;
			$output[$dimension]['quote'] = round($cancelled / $orders * 100,2);
			$output[$dimension]['churnSameDay'] = $churnSameDay;
			$output[$dimension]['quoteChurnSameDay'] = round($churnSameDay / $orders * 100,2);
			$output[$dimension]['churn30'] = $churn30;
			$output[$dimension]['quoteChurn30'] = round($churn30 / $orders * 100,2);
			$output[$dimension]['churn90'] = $churn90;
			$output[$dimension]['quoteChurn90'] = round($churn90 / $orders * 100,2);
			$output[$dimension]['churnAfter90'] = $churnAfter90;
			$output[$dimension]['quoteChurnAfter90'] = round($churnAfter90 / $orders * 100,2);
		}

		$cache->save($output);
		return $output;

	}

	public function monthly_date_range($from, $to = null) {

		if (is_null($to)) {$to = date('Y-m-d', strtotime('today'));}

		$start    = new \DateTime($from);
		$first = $start->format("Y-m-d");
		$start->modify('first day of this month');

		$end      = new \DateTime($to);
		$last = $end->format("Y-m-d");
		$end->modify('first day of next month');

		$interval = \DateInterval::createFromDateString('1 month');
		$period   = new \DatePeriod($start, $interval, $end);

		$dates = [];
		foreach ($period as $dt) {
			$date['from'] = $dt->format("Y-m-d");
			$date['to'] = $dt->modify('last day of this month')->format("Y-m-d");
			$date['month'] = $dt;
		    array_push($dates, $date);
		}

		$dates[0]['from'] = $first;
		$dates[array_key_last($dates)]['to'] = $last;

		return $dates;

	}


}
