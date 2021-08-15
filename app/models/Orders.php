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
		$this->db->table = 'orders';
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

			"SELECT orders.*,
			 articles.title as article_title,
			 articles.type as article_type,
			 articles.tag as article_tag,
			 articles.author as article_author,
			 articles.kicker as article_kicker,
			 articles.pubdate as article_pubdate
			 #conversions.ga_source as ga_source,
			 #conversions.ga_city as ga_city
			 FROM orders
			 LEFT JOIN articles ON `id` = orders.article_id
			 #LEFT JOIN `conversions` ON `transaction_id` = orders.order_id

			 WHERE DATE(orders.order_date) BETWEEN :startDate AND :endDate
			 ORDER BY orders.order_date DESC
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
			 FROM `orders`
			 WHERE DATE(`order_date`) BETWEEN :startDate AND :endDate
			 ORDER BY order_date DESC
			 LIMIT 0, $limit"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
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


	public function untracked_orders_with_date() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT order_id FROM orders
			left join conversions
			on order_id = conversions.transaction_id
			WHERE conversions.transaction_id IS NULL
			AND DATE(orders.order_date) BETWEEN :startDate AND :endDate
			AND orders.article_id IS NOT NULL
			"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		return $SQLstatement->fetchall(\PDO::FETCH_COLUMN);

	}



	public function untracked_orders() {

		$SQLstatement = $this->db->connection->prepare(
			"SELECT order_id FROM orders
			left join conversions
			on order_id = conversions.transaction_id
			WHERE conversions.transaction_id IS NULL
			AND orders.article_id IS NOT NULL
			"
		);

		$SQLstatement->execute();
		return $SQLstatement->fetchall(\PDO::FETCH_COLUMN);

	}


	public function transactionIDs_by_article($articleID) {

		$SQLstatement = $this->db->connection->prepare(
			"SELECT `order_id`
			 FROM `orders`
			 WHERE `article_id` = :articleID"
		);

		$SQLstatement->execute([':articleID' => $articleID]);
		return $SQLstatement->fetchall(\PDO::FETCH_UNIQUE);

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
			if ($order['article_ressort'] == '') {return $order;}
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


}
