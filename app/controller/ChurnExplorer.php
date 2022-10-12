<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;
use flundr\cache\RequestCache;
use \app\models\helpers\MappingTool;

class ChurnExplorer extends Controller {

	public function __construct() {

		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->view->templates['footer'] = null;
		$this->models('Charts,Orders,Campaigns,Articles');
	}

	public function index() {

		//dd($this->Orders->cancelled_by_retention_days());

		$this->Orders->from = '2000-01-01';
		$this->Orders->to = date('Y-m-d');
		$this->Articles->from = '2000-01-01';
		$this->Articles->to = date('Y-m-d');

		$this->view->title = 'The Ultimate Churn-Rate Explorer phew phew!';
		$this->view->months = array_reverse($this->month_list());
		$this->view->segments = $this->Orders->order_segments();
		$this->view->products = $this->Orders->product_titles();
		$this->view->paymentMethods = $this->Orders->order_payment_methods();
		$this->view->ressorts = $this->Orders->order_ressorts();
		$this->view->groupedSources = $this->Orders->order_referer_source_grouped();
		$this->view->sources = $this->Orders->order_referer_source();
		$this->view->types = $this->Articles->list_distinct('Type');
		$this->view->audiences = $this->Articles->list_distinct('Audience');
		$this->view->origins = $this->Orders->order_origins();
		$this->view->testgroups = ['A','B'];

		$mapper = new MappingTool();

		$gaSources = $this->Orders->list_distinct('ga_source');
		$UTMmediums = $this->Campaigns->list_distinct('utm_medium');
		$this->view->mapping = $mapper->referer_overview(array_merge($UTMmediums,$gaSources));

		$this->view->render('orders/explorer/explorer-ui');
	}


	public function api() {

		$options = $this->sanitize_get_parameters();

		if (empty($options['to'])) {$options['to'] = date('Y-m-d');}
		if (empty($options['from'])) {$options['from'] = '2000-01-01';}

		if ($options['to'] < $options['from']) {
			$tmp = $options['to'];
			$options['to'] = $options['from'];
			$options['from'] = $tmp;
		}

		$this->Orders->from = $options['from'];
		$this->Orders->to = $options['to'];

		$product = $options['product'] ?? null;
		$compressed = false;
		if (isset($options['compressed']) && $options['compressed'] == 'true') {$compressed = true;}

		$retention = $options['days'] ?? null;
		$segment = $options['segment'] ?? null;
		$ressort = $options['ressort'] ?? null;
		$type = $options['type'] ?? null;
		$audience = $options['audience'] ?? null;
		$origin = $options['origin'] ?? null;
		$sourceGrouped = $options['source_grp'] ?? null;
		$source = $options['source'] ?? null;
		$testgroup = $options['testgroup'] ?? null;
		$paymethod = $options['paymethod'] ?? null;

		$orderFilters = [];
		$cancelFilters = [];

		if (!empty($product)) {
			// Should be a Comma seperated array
			$products = explode(',', $product);
			$productsString = '(subscription_internal_title = \'' . implode('\' || subscription_internal_title = \'', $products) .'\')';
			array_push($orderFilters, $productsString);
			array_push($cancelFilters, $productsString);
		}

		if (!empty($segment)) {
			array_push($orderFilters, "customer_order_segment = '$segment'");
			array_push($cancelFilters, "customer_order_segment = '$segment'");
		}

		if (!empty($ressort)) {
			array_push($orderFilters, "article_ressort = '$ressort'");
			array_push($cancelFilters, "article_ressort = '$ressort'");
		}

		if (!empty($type)) {
			array_push($orderFilters, "type = '$type'");
			array_push($cancelFilters, "type = '$type'");
		}

		if (!empty($audience)) {
			array_push($orderFilters, "audience = '$audience'");
			array_push($cancelFilters, "audience = '$audience'");
		}

		if (!empty($origin)) {
			array_push($orderFilters, "order_origin = '$origin'");
			array_push($cancelFilters, "order_origin = '$origin'");
		}

		if (!empty($sourceGrouped)) {
			array_push($orderFilters, "referer_source_grouped = '$sourceGrouped'");
			array_push($cancelFilters, "referer_source_grouped = '$sourceGrouped'");
		}

		if (!empty($source)) {
			array_push($orderFilters, "referer_source = '$source'");
			array_push($cancelFilters, "referer_source = '$source'");
		}

		if (!empty($testgroup)) {
			array_push($orderFilters, "customer_testgroup = '$testgroup'");
			array_push($cancelFilters, "customer_testgroup = '$testgroup'");
		}

		if (!empty($paymethod)) {
			array_push($orderFilters, "order_payment_method = '$paymethod'");
			array_push($cancelFilters, "order_payment_method = '$paymethod'");
		}

		if ($retention) {
			array_push($cancelFilters, "conversions.retention < $retention");
		}

		$orders = $this->Orders->count_with_article_join($this->build($orderFilters));
		$cancelled = $this->Orders->cancelled_by_retention_days_with_article_join($this->build($cancelFilters));

		$data['orders'] = $orders;
		$data['cancelled'] = $this->sum($cancelled);
		$data['retentiondays'] = array_sum(array_keys($cancelled));
		$data['retention'] = 0;
		if ($data['cancelled'] > 0) {
			$data['retention'] = round($data['retentiondays'] / $data['cancelled'],2);
		}

		
		if ($compressed == 'true') {
			$cancelled = $this->fill_gaps($cancelled);
		}
		

		$data['chart'] = $this->Charts->convert_as_integer($cancelled);

		$this->view->json($data);

	}


	private function build(array $filters) {
		if (empty($filters)) {return null;}
		return implode(' AND ', $filters);
	}

	private function sum($set) {
		return array_sum(array_column($set,'cancelled_orders'));
	}

	private function month_list($startDate = '2021-04-01') {

		$start = new \DateTime(date('Y-m-d', strtotime($startDate)));
		$interval = new \DateInterval('P1M');
		$end = new \DateTime(date('Y-m-d'));
		$period = new \DatePeriod($start, $interval, $end);

		$monthList = [];
		foreach ($period as $date) {
			$monthList[$date->format('M Y')]['start'] = $date->format('Y-m-') . '01';
			$monthList[$date->format('M Y')]['end'] = $date->format('Y-m-t');
		}

		return $monthList;

	}

	private function fill_gaps($array) {

		$maxDays = max(array_keys($array));
		$range = range(0,$maxDays);

		$out = [];
		foreach ($range as $day) {
			if (isset($array[$day])) {
				$out[$day] = $array[$day];
			}
			else {
				$out[$day]['cancelled_orders'] = 0;
			}
		}

		return $out;

	}

	private function sanitize_get_parameters() {
		$params = array_map('strip_tags', $_GET);
		//$params = array_map('htmlentities', $_GET); // Error With some Cats :/
		$valid = array_flip(['from', 'to', 'compressed' , 'product', 'segment', 'testgroup', 'ressort', 'type', 'audience', 'origin', 'source_grp', 'source', 'days', 'paymethod']);
		$params = array_intersect_key($params,$valid);

		foreach ($params as $key => $value) {
			if ($key=='days') {$value = intval($value);}
			$value = str_replace('--','', $value);
			$value = str_replace('#','', $value);
			$value = str_replace(';','', $value);
			$params[$key] = $value;
		}

		return $params;
	}

}
