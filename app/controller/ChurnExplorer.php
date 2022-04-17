<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;
use flundr\cache\RequestCache;

class ChurnExplorer extends Controller {

	public function __construct() {

		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->view->templates['footer'] = null;
		$this->models('Charts,Orders,Articles');
	}

	public function index() {

		//dd($this->Orders->cancelled_by_retention_days());

		$this->view->title = 'The Ultimate Churn-Rate Explorer phew phew!';
		$this->view->segments = $this->Orders->order_segments();
		$this->view->products = $this->Orders->product_titles();
		$this->view->ressorts = $this->Orders->order_ressorts();
		$this->view->types = $this->Articles->list_distinct('Type');
		$this->view->audiences = $this->Articles->list_distinct('Audience');
		$this->view->origins = $this->Orders->order_origins();
		$this->view->render('orders/explorer/explorer-ui');
	}


	public function api() {

		$options = $this->sanitize_get_parameters();

		if (empty($options['to'])) {$options['to'] = date('Y-m-d');}
		if (empty($options['from'])) {$options['from'] = date('Y-m-d', strtotime('2000-01-01'));}

		if ($options['to'] < $options['from']) {
			$tmp = $options['to'];
			$options['to'] = $options['from'];
			$options['from'] = $tmp;
		}

		$this->Orders->from = $options['from'];
		$this->Orders->to = $options['to'];

		$product = $options['product'] ?? null;
		$retention = $options['days'] ?? null;
		$segment = $options['segment'] ?? null;
		$ressort = $options['ressort'] ?? null;
		$type = $options['type'] ?? null;
		$audience = $options['audience'] ?? null;
		$origin = $options['origin'] ?? null;

		$orderFilters = [];
		$cancelFilters = [];

		if (!empty($product)) {
			array_push($orderFilters, "subscription_internal_title = '$product'");
			array_push($cancelFilters, "subscription_internal_title = '$product'");
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

		if ($retention) {
			array_push($cancelFilters, "conversions.retention < $retention");
		}

		$orders = $this->Orders->count_with_article_join($this->build($orderFilters));

		$cancelled = $this->Orders->cancelled_by_retention_days_with_article_join($this->build($cancelFilters));

		$data['orders'] = $orders;
		$data['cancelled'] = $this->sum($cancelled);
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

	private function sanitize_get_parameters() {
		$params = array_map('strip_tags', $_GET);
		//$params = array_map('htmlentities', $_GET); // Error With some Cats :/
		$valid = array_flip(['from', 'to', 'product', 'segment', 'ressort', 'type', 'audience', 'origin', 'days']);
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
