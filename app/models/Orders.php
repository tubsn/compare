<?php

namespace app\models;
use \flundr\mvc\Model;
use \flundr\database\SQLdb;
use \flundr\utility\Session;
use flundr\cache\RequestCache;

class Orders extends Model
{

	public $from = '0000-00-00';
	public $to = '3000-01-01';

	function __construct() {

		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'conversions';
		$this->db->primaryIndex = 'order_id';
		$this->db->orderby = 'order_id';

		$this->from = date('Y-m-d', strtotime('monday this week'));
		$this->to = date('Y-m-d', strtotime('sunday this week'));

		if (Session::get('from')) {$this->from = Session::get('from');}
		if (Session::get('to')) {$this->to = Session::get('to');}

	}

	public function list() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		$limit = 5000;

		$SQLstatement = $this->db->connection->prepare(

			"SELECT conversions.*,
			 articles.title as article_title,
			 articles.type as article_type,
			 articles.tag as article_tag,
			 ifnull(conversions.article_ressort, articles.ressort) as article_ressort,
			 articles.author as article_author,
			 articles.kicker as article_kicker,
			 articles.pubdate as article_pubdate

			 FROM conversions

			 LEFT JOIN articles ON `id` = conversions.article_id

			 WHERE DATE(conversions.order_date) BETWEEN :startDate AND :endDate
			 ORDER BY conversions.order_date DESC
			 LIMIT 0, $limit"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$orders = $SQLstatement->fetchall();

		return $orders;

	}


	public function list_plain() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		$limit = 5000;

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `conversions`
			 WHERE DATE(`order_date`) BETWEEN :startDate AND :endDate
			 ORDER BY order_date DESC
			 LIMIT 0, $limit"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		return $output;

	}

	public function orders_for_chart() {
		$rawOrders = $this->orders_by_date();

		$orders = null; $dates = null;
		foreach ($rawOrders as $order) {
			$orders .= $order['orders'] . ',';
			$dates .= "'" . $order['day'] . "'" . ',';
		}

		$chart['amount'] = rtrim($orders, ',');
		$chart['dates'] = rtrim($dates, ',');
		$chart['color'] = '#df886d';
		$chart['name'] = 'Conversions';

		return $chart;
	}

	public function orders_by_date() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT count(order_id) as 'orders', DATE(`order_date`) as 'day'
			 FROM `conversions`
			 WHERE DATE(`order_date`) BETWEEN :startDate AND :endDate
			 GROUP BY day
			 ORDER BY order_date ASC"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		return $output;

	}

	public function ressorts_for_chart() {
		$rawOrders = $this->orders_by_ressort();

		//dd($rawOrders);

		$orders = null; $ressorts = null;
		foreach ($rawOrders as $data) {
			if (empty($data['article_ressort'])) {continue;}
			$orders .= $data['orders'] . ',';
			$ressorts .= "'" . ucfirst($data['article_ressort']) . "'" . ',';
		}

		$chart['amount'] = rtrim($orders, ',');
		$chart['dates'] = rtrim($ressorts, ',');
		$chart['color'] = '#df886d';  // Orange #df886d - Blue #6088b4
		$chart['name'] = 'Bestellungen';
		$chart['showValues'] = 'true';


		return $chart;
	}

	public function orders_by_ressort() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT article_ressort, count(order_id) as 'orders'
			 FROM `conversions`
			 WHERE DATE(order_date) BETWEEN :startDate AND :endDate
			 GROUP BY article_ressort
			 ORDER BY article_ressort ASC"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();

		return $output;

	}


	public function latest_grouped($limit = 5) {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(

			"SELECT articles.id as id,
			 count(conversions.order_id) as conversions,
			 articles.title as title,
			 articles.plus as plus,
			 articles.type as type,
			 articles.image as image,
			 articles.pageviews as pageviews,
			 articles.subscribers as subscribers,
			 ifnull(conversions.article_ressort, articles.ressort) as ressort,
			 articles.author as author,
			 articles.kicker as kicker,
			 articles.cancelled as cancelled,
			 articles.avgmediatime as avgmediatime,
			 articles.pubdate as pubdate

			 FROM conversions
			 LEFT JOIN articles ON `id` = conversions.article_id
			 WHERE DATE(conversions.order_date) BETWEEN :startDate AND :endDate
			# AND ressort != 'plus'

			 GROUP BY articles.id
			 ORDER BY conversions DESC, pageviews DESC
			 LIMIT 0, $limit"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$orders = $SQLstatement->fetchall();

		return $orders;

	}


	public function count() {
		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT count(*) as orders
			 FROM `conversions`
			 WHERE DATE(`order_date`) BETWEEN :startDate AND :endDate"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetch();
		return $output['orders'];

	}

	public function without_ga_sources($dayCount = 5) {
		$SQLstatement = $this->db->connection->prepare(
			"SELECT order_id FROM `conversions` WHERE ga_source IS NULL
			 AND DATE(`order_date`) >= DATE_SUB(CURRENT_DATE, INTERVAL :dayCount DAY)
			 LIMIT 0, 500"
		);

		$SQLstatement->execute([':dayCount' => $dayCount]);
		$output = $SQLstatement->fetchall(\PDO::FETCH_UNIQUE);
		return $output;
	}


	public function group_by($index) {
		$orders = $this->list();
		$data = array_column($orders, $index);
		$data = @array_count_values($data); // @ Surpresses Warnings with Null Values
		arsort($data);
		return $data;
	}

	public function group_cancelled_orders_by($index) {
		$orders = $this->list();
		$orders = $this->filter_cancelled($orders);
		$data = array_column($orders,$index);
		$data = @array_count_values($data); // @ Surpresses Warnings with Null Values
		arsort($data);
		return $data;
	}

	public function group_average_retention($index) {
		$orders = $this->list();
		$orders = $this->filter_cancelled($orders);

		$columns = array_column($orders,$index);
		$columns = array_unique($columns);

		$retentionDays = [];

		foreach ($columns as $key => $column) {

			$items = array_filter($orders, function($order) use ($index, $column) {
				if ($order[$index] == $column) {return $order;}
			});

			$retentionOnly = array_column($items,'retention');
			$days = array_sum($retentionOnly);

			$retention = round($days / count($items), 2);

			$retentionDays[$column] = $retention;
		}

		arsort($retentionDays);
		return $retentionDays;
	}



	public function group_by_combined($index) {

		$combined = [];
		$active = $this->group_by($index);
		$cancelled = $this->group_cancelled_orders_by($index);
		$retention = $this->group_average_retention($index);

		foreach ($active as $key => $value) {

			$combined[$key]['all'] = $value;
			$combined[$key]['retention'] = $retention[$key] ?? null;

			if (isset($cancelled[$key])) {
				$combined[$key]['cancelled'] = $cancelled[$key];
			}
			else {
				$combined[$key]['cancelled'] = 0;
			}

			$combined[$key]['active'] = $value - $combined[$key]['cancelled'];

			if ($value) {
				$combined[$key]['quote'] = round(($combined[$key]['cancelled'] / $value) * 100, 2);
			}	else {$combined[$key]['quote'] = null;}

		}
		return $combined;
	}

	public function filter_cancelled($orders) {
		if (empty($orders)) {return [];}
		return array_filter($orders, function($order) {
			if ($order['cancelled']) {return $order;}
		});

	}

	public function filter_plus_only($orders) {
		if (empty($orders)) {return [];}
		return array_filter($orders, function($order) {
			if ($order['article_ressort'] == 'plus') {return $order;}
		});
	}

	public function filter_external($orders) {
		if (empty($orders)) {return [];}
		return array_filter($orders, function($order) {
			if ($order['article_ressort'] == '' && $order['article_id'] == '') {return $order;}
		});
	}


	public function sum_up($array, $key) {
		if (empty($array)) {return 0;}
		return array_sum(array_column($array,$key));
	}

	public function average($array, $key) {
		if (empty($array)) {return 0;}
		return (array_sum(array_column($array,$key)) / count(array_column($array,$key)) );
	}


	public function import($date, $client = 'LR') {

		// don't do Imports prior to Plenigo V3
		if ($date < '2021-03-23') {
			return ['Error' => 'Can´t Import prior to 2021-03-23 (PlenigoV3)'];
		}

		$plenigo = new Plenigo();
		$plenigo->api->client($client);

		$orderList = $plenigo->orders($date, $date, 100);

		$detailedOrders = [];
		foreach ($orderList as $order) {

			$cache = new RequestCache($order['order_id'], 5 * 60);
			$details = $cache->get();

			if (!$details) {
				$details = $plenigo->order_with_details($order['order_id']);
			}

			$cache->save($details);
			array_push($detailedOrders, $details);
		}

		// Save to DB
		foreach ($detailedOrders as $order) {

			if ($this->get($order['order_id'])) {
				$this->update($order, $order['order_id']);
			} else {$this->create($order);}

			if (isset($order['article_id'])) {
				$conversions = new Conversions();
				$conversions->articleID = $order['article_id'];
				$conversions->collect();
				$conversions->update_article();
			}

		}

		return $detailedOrders;

	}

}