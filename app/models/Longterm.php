<?php

namespace app\models;
use app\importer\PortalImport;
use \flundr\database\SQLdb;
use \flundr\mvc\Model;
use \flundr\utility\Session;
use \flundr\cache\RequestCache;

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

		$this->Portals = new PortalImport();
		$this->Orders = new Orders();
		$this->KPIs = new DailyKPIs();
		$this->Articles = new Articles();

	}


	public function portal_KPIs() {

		$cache = new RequestCache('portalkpis', 30*60);
		$portalData = $cache->get();

		if (empty($portalData)) {
			$portalData = $this->Portals->KPIs();
			$cache->save($portalData);
		}

		return $portalData;

	}

	public function portal_orders() {

		$cache = new RequestCache('portalorders', 30*60);
		$portalData = $cache->get();

		if (empty($portalData)) {
			$portalData = $this->Portals->orders();
			$cache->save($portalData);
		}

		return $portalData;

	}

	public function chartdata($kpi) {

		switch ($kpi) {
			case 'orders': $data = $this->orders(); break;
			default: break;
		}

		$charts = new Charts();
		return $charts->convert($data);

	}

	public function started_payment() {

		return $this->Orders->active_after_days();

	}

	public function kpis($start = null) {

		if (is_null($start)) {$start = '2020-10-01';}
		$periods = $this->monthly_date_range($start);

		$output = [];
		foreach ($periods as $period) {

			$dimension = $period['month']->format('Y-m');

			$this->KPIs->from = $period['from'];
			$this->KPIs->to = $period['to'];
			$this->Articles->from = $period['from'];
			$this->Articles->to = $period['to'];

			$pageviews = $this->KPIs->sum('pageviews');
			$articles = $this->Articles->count();
			$plus = $this->Articles->count('*', 'plus = 1');

			$spielmacher = $this->Articles->count('*', 'conversions>0 AND subscribers>=100' );
			$geister = $this->Articles->count('*', '(conversions IS NULL OR conversions=0) AND subscribers<=100' );

			$avgmediatime = $this->KPIs->avg('avgmediatime');

			$output[$dimension]['pageviews'] = $pageviews;
			$output[$dimension]['pageviewsmio'] = round($pageviews/1000000,2);
			$output[$dimension]['avgmediatime'] = round($avgmediatime,2);
			$output[$dimension]['articles'] = $articles;
			$output[$dimension]['plusarticles'] = $plus;
			$output[$dimension]['spielmacher'] = $spielmacher;
			$output[$dimension]['geister'] = $geister;

			if ($articles > 0) {
				$output[$dimension]['quoteSpielmacher'] = round($spielmacher / $articles * 100,2);
			} else {$output[$dimension]['quoteSpielmacher'] = 0;}

			if ($articles > 0) {
				$output[$dimension]['quoteGeister'] = round($geister / $articles * 100,2);
			} else {$output[$dimension]['quoteGeister'] = 0;}

		}

		return $output;

	}

	public function orders($start = null) {

		$cache = new RequestCache('cancellcationdata' . $start . PORTAL, 10*60);
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

			$output[$dimension]['activeAfter30'] = null;
			$output[$dimension]['activeAfter90'] = null;

			/*
			$output[$dimension]['quoteActiveAfter30'] = null;
			$output[$dimension]['quoteActiveAfter90'] = null;
			*/

		}

		$this->Orders->from = $start;
		$this->Orders->to = date('Y-m-d', strtotime('today'));
		$activeAfter30 = $this->Orders->active_after_days(30);
		$activeAfter90 = $this->Orders->active_after_days(90);

		foreach ($activeAfter30 as $month => $data) {
			$output[$month]['activeAfter30'] = $data['active'];
			$output[$month]['quoteActiveAfter30'] = round($data['active'] / $output[$month]['orders'] * 100,2);
		}

		foreach ($activeAfter90 as $month => $data) {
			$output[$month]['activeAfter90'] = $data['active'];
			$output[$month]['quoteActiveAfter90'] = round($data['active'] / $output[$month]['orders'] * 100,2);
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
