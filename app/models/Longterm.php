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

		if (date('j') <= 15) {
			$to = date('Y-m-d', strtotime('last day of last month'));
		} else {$to = null;}

		$periods = $this->monthly_date_range($start, $to);

		$output = [];
		foreach ($periods as $period) {

			$dimension = $period['month']->format('Y-m');

			$this->KPIs->from = $period['from'];
			$this->KPIs->to = $period['to'];
			$this->Articles->from = $period['from'];
			$this->Articles->to = $period['to'];

			$pageviews = $this->KPIs->sum('pageviews');
			$articles = $this->Articles->count();
			$plus = $this->Articles->count_with_filter('plus = 1');

			$scoreArticles = count($this->Articles->score_articles());

			$spielmacher = $this->Articles->count_with_filter('conversions>0 AND subscribers>=100');
			$geister = $this->Articles->count_with_filter('(conversions IS NULL OR conversions=0) AND subscribers <= 100');

			$avgmediatime = $this->KPIs->avg('avgmediatime');

			$output[$dimension]['pageviews'] = $pageviews;
			$output[$dimension]['pageviewsmio'] = round($pageviews/1000000,2);
			$output[$dimension]['avgmediatime'] = round($avgmediatime,2);
			$output[$dimension]['articles'] = $articles;
			$output[$dimension]['plusarticles'] = $plus;
			$output[$dimension]['spielmacher'] = $spielmacher;
			$output[$dimension]['geister'] = $geister;
			$output[$dimension]['scoreArticles'] = $scoreArticles;

			$output[$dimension]['quoteScore'] = percentage($scoreArticles, $articles);
			$output[$dimension]['quotePlus'] = percentage($plus, $articles);
			$output[$dimension]['quoteSpielmacher'] = percentage($spielmacher, $articles);
			$output[$dimension]['quoteGeister'] = percentage($geister, $articles);

		}

		return $output;

	}

	public function orders($start = null) {

		$cache = new RequestCache('cancellcationdata' . $start . PORTAL, 30*60);
		$cachedData = $cache->get();
		if ($cachedData) {return $cachedData;}

		if (is_null($start)) {$start = '2021-04-01';}

		if (date('j') <= 15) {
			$to = date('Y-m-d', strtotime('last day of last month'));
		} else {$to = null;}

		$periods = $this->monthly_date_range($start, $to);

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
			$output[$dimension]['churnSameDay'] = $churnSameDay;
			$output[$dimension]['churn30'] = $churn30;
			$output[$dimension]['churn90'] = $churn90;
			$output[$dimension]['churnAfter90'] = $churnAfter90;
			$output[$dimension]['churnProbe'] = $churn30;

			$output[$dimension]['quote'] = percentage($cancelled, $orders);
			$output[$dimension]['quoteChurnSameDay'] = percentage($churnSameDay, $orders);
			$output[$dimension]['quoteChurn30'] = percentage($churn30, $orders);
			$output[$dimension]['quoteChurn90'] = percentage($churn90, $orders);
			$output[$dimension]['quoteChurnAfter90'] = percentage($churnAfter90, $orders);
			$output[$dimension]['quoteChurnProbe'] = percentage($churn30, $orders);

			$output[$dimension]['activeAfterProbe'] = null;
			$output[$dimension]['activeAfter30'] = null;
			$output[$dimension]['activeAfter90'] = null;
			$output[$dimension]['activeAfter6M'] = null;

		}

		$this->Orders->from = $start;
		$this->Orders->to = $to;
		$activeAfter30 = $this->Orders->active_after_days(30);
		$activeAfter90 = $this->Orders->active_after_days(90);
		$activeAfter6M = $this->Orders->active_after_days(180);

		foreach ($activeAfter30 as $month => $data) {
			$output[$month]['activeAfter30'] = $data['active'];
			$output[$month]['activeAfterProbe'] = $data['active'];
			$output[$month]['quoteActiveAfter30'] = percentage($data['active'], $output[$month]['orders']);
			$output[$month]['quoteActiveAfterProbe'] = percentage($data['active'], $output[$month]['orders']);
		}

		foreach ($activeAfter90 as $month => $data) {
			$output[$month]['activeAfter90'] = $data['active'];
			$output[$month]['quoteActiveAfter90'] = percentage($data['active'], $output[$month]['orders']);
		}

		foreach ($activeAfter6M as $month => $data) {
			$output[$month]['activeAfter6M'] = $data['active'];
			$output[$month]['quoteActiveAfter6M'] = percentage($data['active'], $output[$month]['orders']);
		}

		if (PORTAL == 'LR') {
			foreach ($output as $month => $data) {
				$monthAsNumber = date('m', strtotime($month));
				if ($monthAsNumber >=8) {
					$output[$month]['churnProbe'] = $data['churn90'];
					$output[$month]['quoteChurnProbe'] = $data['quoteChurn90'];
					$output[$month]['activeAfterProbe'] = $data['activeAfter90'];
					$output[$month]['quoteActiveAfterProbe'] = $data['quoteActiveAfter90'];
				}
			}
		}

		$cache->save($output);

		return $output;

	}

	public function combine_portal_data($orderData, $kpiData) {

		$output = [];
		foreach ($orderData as $portal => $void) {
			$output[$portal] = $this->combine($orderData[$portal], $kpiData[$portal]);
		}

		return $output;
	}

	public function combine($orderData, $kpiData) {
		$combined = [];
		foreach ($orderData as $month => $dataset) {

			$traffic = $kpiData[$month]['pageviews'];
			$articles = $kpiData[$month]['articles'];
			$plus = $kpiData[$month]['plusarticles'];
			$orders = $orderData[$month]['orders'];
			$active = $orderData[$month]['active'];
			$active30 = $orderData[$month]['activeAfter30'];
			$activeProbe = $orderData[$month]['activeAfterProbe'] ?? 0;

			$combined[$month]['plusOrderQuote'] = percentage($orders, $plus);
			$combined[$month]['articlesOrderQuote'] = percentage($orders, $articles);
			$combined[$month]['plusActiveQuote'] = percentage($active, $plus);
			$combined[$month]['articlesActiveQuote'] = percentage($active, $articles);
			$combined[$month]['articlesActiveAfterProbe'] = percentage($activeProbe, $articles);
			$combined[$month]['trafficOrdersQuote'] = percentage($orders, $traffic,4);
			$combined[$month]['trafficActiveQuote'] = percentage($active, $traffic,4);
		}
		return $combined;
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
