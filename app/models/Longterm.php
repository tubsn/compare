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

		$this->Portals = new PortalImport();
		$this->Orders = new Orders();
		$this->KPIs = new DailyKPIs();
		$this->Articles = new Articles();

	}

	public function portal_KPIs() {

		$cache = new RequestCache('portalkpis', 30*60);
		$cache->cacheDirectory = ROOT . 'cache' . DIRECTORY_SEPARATOR . 'portals';
		$portalData = $cache->get();

		if (empty($portalData)) {
			$portalData = $this->Portals->KPIs();
			$cache->save($portalData);
		}

		return $portalData;

	}

	public function portal_orders() {

		$cache = new RequestCache('portalorders', 30*60);
		$cache->cacheDirectory = ROOT . 'cache' . DIRECTORY_SEPARATOR . 'portals';
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

		$cache = new RequestCache('kpidata' . $start . PORTAL, 30*60);
		$cache->cacheDirectory = ROOT . 'cache' . DIRECTORY_SEPARATOR . 'portals';
		$cachedData = $cache->get();
		//if ($cachedData) {return $cachedData;}

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

			$pageviews = $this->KPIs->sum('pageviews') ?? 0;
			$sessions = $this->KPIs->sum('sessions') ?? 0;
			$subscribers = $this->KPIs->sum('subscribers') ?? 0;

			$articles = $this->Articles->count();
			$buyintents = $this->Articles->sum('buyintent');
			$plus = $this->Articles->count_with_filter('plus = 1');

			$scoreArticles = count($this->Articles->score_articles() ?? []);

			$spielmacher = $this->Articles->count_with_filter('conversions>0 AND subscribers>=100');
			$geister = $this->Articles->count_with_filter('(conversions IS NULL OR conversions=0) AND subscribers < 100');

			$avgmediatime = $this->KPIs->avg('avgmediatime');

			$output[$dimension]['sessions'] = $sessions;
			$output[$dimension]['pageviews'] = $pageviews;
			$output[$dimension]['pageviewsmio'] = round($pageviews/1000000,2);
			$output[$dimension]['subscribers'] = $subscribers;
			$output[$dimension]['avgmediatime'] = round($avgmediatime,2);
			$output[$dimension]['buyintents'] = $buyintents;
			$output[$dimension]['articles'] = $articles;
			$output[$dimension]['plusarticles'] = $plus;
			$output[$dimension]['spielmacher'] = $spielmacher;
			$output[$dimension]['geister'] = $geister;
			$output[$dimension]['scoreArticles'] = $scoreArticles;

			$output[$dimension]['quoteSubscribers'] = percentage($subscribers, $pageviews);
			$output[$dimension]['quoteScore'] = percentage($scoreArticles, $articles);
			$output[$dimension]['quotePlus'] = percentage($plus, $articles);
			$output[$dimension]['quoteSpielmacher'] = percentage($spielmacher, $articles);
			$output[$dimension]['quoteGeister'] = percentage($geister, $articles);

		}

		$cache->save($output);

		return $output;

	}

	public function orders($start = null) {

		$cache = new RequestCache('orderdata' . $start . PORTAL, 30*60);
		$cache->cacheDirectory = ROOT . 'cache' . DIRECTORY_SEPARATOR . 'portals';
		$cachedData = $cache->get();
		if ($cachedData) {return $cachedData;}

		if (is_null($start)) {$start = '2021-04-01';}

		if (date('j') <= 15) {
			$to = date('Y-m-d', strtotime('last day of last month'));
		} else {$to = date('Y-m-d', strtotime('today'));}

		$periods = $this->monthly_date_range($start, $to);

		$output = [];
		foreach ($periods as $period) {

			$dimension = $period['month']->format('Y-m');

			$this->Orders->from = $period['from'];
			$this->Orders->to = $period['to'];

			$orders = $this->Orders->count();
			$plusPageOrders = $this->Orders->count_pluspage();
			$cancelled = $this->Orders->count_cancelled();

			$churnSameDay = $this->Orders->sum_up($this->Orders->cancelled_by_retention_days('retention = 0'),'cancelled_orders');
			$churn30 = $this->Orders->sum_up($this->Orders->cancelled_by_retention_days('retention < 31'),'cancelled_orders');
			$churn90 = $this->Orders->sum_up($this->Orders->cancelled_by_retention_days('retention < 90'),'cancelled_orders');
			$churn6M = $this->Orders->sum_up($this->Orders->cancelled_by_retention_days('retention < 180'),'cancelled_orders');

			$output[$dimension]['orders'] = $orders;
			$output[$dimension]['plusPageOrders'] = $plusPageOrders;
			$output[$dimension]['cancelled'] = $cancelled;
			$output[$dimension]['cancelledNegative'] = $cancelled * -1;
			$output[$dimension]['active'] = $orders - $cancelled;
			$output[$dimension]['churnSameDay'] = $churnSameDay;
			$output[$dimension]['churn30'] = $churn30;
			$output[$dimension]['churn90'] = $churn90;
			$output[$dimension]['churn6M'] = $churn6M;
			$output[$dimension]['churnProbe'] = $churn30;

			$output[$dimension]['quote'] = percentage($cancelled, $orders);
			$output[$dimension]['quotePlusPage'] = percentage($plusPageOrders, $orders);
			$output[$dimension]['quoteChurnSameDay'] = percentage($churnSameDay, $orders);
			$output[$dimension]['quoteChurn30'] = percentage($churn30, $orders);
			$output[$dimension]['quoteChurn90'] = percentage($churn90, $orders);
			$output[$dimension]['quoteChurn6M'] = percentage($churn6M, $orders);
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

		// Probezeitraum 3für3 LR
		if (PORTAL == 'LR') {
			foreach ($output as $month => $data) {
				$monthAsNumber = date('m', strtotime($month));
				$year = date('Y', strtotime($month));
				if ($year >= 2022 || ($year == 2021 && $monthAsNumber >=8) ) {
					$output[$month]['churnProbe'] = $data['churn90'] ?? null;
					$output[$month]['quoteChurnProbe'] = $data['quoteChurn90'] ?? null;
					$output[$month]['activeAfterProbe'] = $data['activeAfter90'] ?? null;
					$output[$month]['quoteActiveAfterProbe'] = $data['quoteActiveAfter90'] ?? null;
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

	public function remove_before($data, $month) {
		$start = array_search($month, array_keys($data));
		return array_slice($data, $start);
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

	public function compare_with_past($data) {

		foreach ($data as $month => $content) {
			$prevMonth = $this->shift_date($month);

			if (isset($data[$prevMonth])) {
				$data[$month]['past'] = $data[$prevMonth];
			}

		}
		return $data;
	}

	public function compare_fields_with_past($data, $fields = ['pageviews'], $removeOverhead = true) {

		$remove = [];

		foreach ($data as $month => $content) {
			$prevMonth = $this->shift_date($month);

			if (!isset($data[$prevMonth])) {
				array_push($remove, $month);
				continue;
			}

			foreach ($fields as $field) {
				$data[$month][$field . '_past'] = $data[$prevMonth][$field];
			}

		}

		if ($removeOverhead) {
			$data = array_diff_key($data, array_flip($remove));
		}

		return $data;

	}


	public function shift_date($date, $amount = '-1year', $format = 'Y-m') {
		$amount = ' ' . $amount;
		return date($format, strtotime($date . $amount));
	}

}
