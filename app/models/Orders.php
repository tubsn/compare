<?php

namespace app\models;
use \flundr\mvc\Model;
use \flundr\database\SQLdb;
use \flundr\utility\Session;
use \flundr\cache\RequestCache;
use \app\models\Charts;
use \app\models\Campaigns;
use \app\models\helpers\MappingTool;

class Orders extends Model
{

	public $from = '0000-00-00';
	public $to = '3000-01-01';

	function __construct() {

		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'conversions';
		$this->db->primaryIndex = 'order_id';
		$this->db->orderby = 'order_id';

		$this->from = date('Y-m-d', strtotime(DEFAULT_FROM));
		$this->to = date('Y-m-d', strtotime(DEFAULT_TO));

		if (Session::get('from')) {$this->from = Session::get('from');}
		if (Session::get('to')) {$this->to = Session::get('to');}

	}

	public function list($filter = null) {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		if (!is_null($filter)) {$filter = 'AND ' . strip_tags($filter);}

		$SQLstatement = $this->db->connection->prepare(

			"SELECT conversions.*,
			 articles.title as article_title,
			 articles.type as article_type,
			 articles.audience as article_audience,
			 articles.tag as article_tag,
			 ifnull(conversions.article_ressort, articles.ressort) as article_ressort,
			 articles.author as article_author,
			 articles.kicker as article_kicker,
			 articles.pubdate as article_pubdate,
			 campaigns.utm_source as utm_source,
			 campaigns.utm_medium as utm_medium,
			 campaigns.utm_campaign as utm_campaign

			 FROM conversions

			 LEFT JOIN articles ON `id` = conversions.article_id
			 LEFT JOIN campaigns ON campaigns.order_id = conversions.order_id

			 WHERE DATE(conversions.order_date) BETWEEN :startDate AND :endDate
			 $filter
			 ORDER BY conversions.order_date DESC"

		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$orders = $SQLstatement->fetchall();

		return $orders;

	}


	public function list_by_day($filter = null) {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		if (!is_null($filter)) {$filter = 'AND ' . strip_tags($filter);}

		//$from = date('Y-m-d', strtotime('-3 days'));
		//$to = date('Y-m-d', strtotime('today'));

		$SQLstatement = $this->db->connection->prepare(

			"SELECT DATE_FORMAT(order_date, '%Y-%m-%d') as day, conversions.*,
			 articles.title as article_title,
			 articles.type as article_type,
			 articles.audience as article_audience,
			 articles.tag as article_tag,
			 ifnull(conversions.article_ressort, articles.ressort) as article_ressort,
			 articles.author as article_author,
			 articles.kicker as article_kicker,
			 articles.image as article_image,
			 articles.conversions as article_conversions,
			 articles.cancelled as article_cancellations,
			 articles.pubdate as article_pubdate

			 FROM conversions
			 LEFT JOIN articles ON `id` = conversions.article_id

			 WHERE DATE(conversions.order_date) BETWEEN :startDate AND :endDate
			 $filter
			 ORDER BY day DESC, conversions.order_date DESC"

		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$orders = $SQLstatement->fetchall();

		return $orders;

	}



	public function list_plain() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `conversions`
			 WHERE DATE(`order_date`) BETWEEN :startDate AND :endDate
			 ORDER BY order_date DESC"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		return $output;

	}

	public function cancellations_plain() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `conversions`
			 WHERE DATE(`subscription_cancellation_date`) BETWEEN :startDate AND :endDate
			 ORDER BY subscription_cancellation_date DESC"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		return $output;

	}

	public function by_customer($customerID) {
		$customerID = strip_tags($customerID);
		return $this->list("customer_id = $customerID");
	}

	public function customers_with_multiple_orders() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT customer_id, count(order_id) as orders
			 FROM `conversions`
			 WHERE DATE(`order_date`) BETWEEN :startDate AND :endDate
			 GROUP BY customer_id
			 HAVING orders > 1
			 ORDER BY orders DESC"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$customers = $SQLstatement->fetchall(\PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);

		$orders = $this->orders_of_customer_list(array_keys($customers));

		$output = [];
		foreach ($orders as $order) {
			$output[$order['customer_id']][$order['order_id']] = $order;
		}

		return $output;

	}

	public function orders_of_customer_list(array $ids) {

		$table = $this->db->table;
		$listOfIds = implode(',', $ids);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `$table`
			 WHERE `customer_id` IN ($listOfIds)
			 ORDER BY FIELD(`customer_id`, $listOfIds)
			 "
		);
		$SQLstatement->execute();
		return $SQLstatement->fetchall();

	}

	public function audience_by_time($audience) {

		$table = $this->db->table;
		$SQLstatement = $this->db->connection->prepare(
			"SELECT DATE_FORMAT(pubdate,'%H') as hour, count(*) as orders
			 FROM `$table`
			 LEFT JOIN articles ON `id` = conversions.article_id
			 WHERE (DATE(`pubdate`) BETWEEN :startDate AND :endDate)
			 AND articles.audience = :audience
			 GROUP BY hour
			 ORDER BY hour DESC
			 LIMIT 0, 1000"
		);

		$SQLstatement->execute([':startDate' => $this->from, ':endDate' => $this->to, ':audience' => $audience]);
		$output = $SQLstatement->fetchall(\PDO::FETCH_UNIQUE);
		if (empty($output)) {return null;}

		return $output;

	}



	public function kpi_grouped_by($kpi, $groupby = 'ressort', $operation = 'sum', $filter = null) {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		// sum, count or average
		$operation = $operation . '(' . $kpi . ')';

		if (!is_null($filter)) {
			$filter = 'AND ' . $filter;
		}

		$SQLstatement = $this->db->connection->prepare(
			"SELECT $groupby, $operation as $kpi
			 FROM `conversions`
			 WHERE DATE(`order_date`) BETWEEN :startDate AND :endDate
			 $filter
			 GROUP BY $groupby
			 ORDER BY $groupby ASC"
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
			 articles.audience as audience,
			 articles.image as image,
			 articles.pageviews as pageviews,
			 articles.subscriberviews as subscriberviews,
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

	public function list_sources_with_utm() {

		$SQLstatement = $this->db->connection->prepare(
			"SELECT
			conversions.order_id,
			conversions.order_origin,
			conversions.ga_source,
			campaigns.utm_source,
			campaigns.utm_medium

			 FROM `conversions`
			 LEFT JOIN campaigns ON campaigns.order_id = conversions.order_id
			 "
		);
		$SQLstatement->execute();
		return $SQLstatement->fetchall();
	}

	public function assign_sources($days = null) {

		if ($days) {
			$this->from = date('Y-m-d', strtotime('today -' . $days . ' day'));
			$this->to = date('Y-m-d');
		}

		else {
			$this->from = '2000-01-01';
			$this->to = date('Y-m-d');
		}

		$mapper = new MappingTool();

		$campaigns = $this->get_campaign_mediums();
		$orders = $this->get_order_referals();

		//Orders to Referer
		foreach ($orders as $orderID => $input) {
			$referer = $mapper->referer($input);
			$this->update($referer, $orderID);
		}

		//UTM Campaigns to Referer
		foreach ($campaigns as $orderID => $input) {
			$referer = $mapper->referer($input);
			$this->update($referer, $orderID);
		}

	}

	public function get_campaign_mediums() {
		$campaignDB = new Campaigns();
		$campaignDB->from = $this->from;
		$campaignDB->to = $this->to;
		return $campaignDB->medium_by_id();
	}

	public function get_order_referals() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT order_id, ga_source FROM `conversions`
			 WHERE DATE(`order_date`) BETWEEN :startDate AND :endDate
			 AND ga_source is not NULL
			 ORDER BY `order_date` DESC"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$orders = $SQLstatement->fetchall(\PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);
		return $orders;

	}


	public function count($filter = null) {
		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		if (!is_null($filter)) {
			$filter = strip_tags($filter);
			$filter = 'AND ' . $filter;
		}

		$SQLstatement = $this->db->connection->prepare(
			"SELECT count(*) as orders
			 FROM `conversions`
			 WHERE DATE(`order_date`) BETWEEN :startDate AND :endDate
			 $filter"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetch();
		return $output['orders'];

	}

	public function count_with_article_join($filter = null) {
		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		if (!is_null($filter)) {
			$filter = strip_tags($filter);
			$filter = 'AND ' . $filter;
		}

		$SQLstatement = $this->db->connection->prepare(
			"SELECT count(*) as orders
			 FROM `conversions`
			 LEFT JOIN articles ON articles.id = conversions.article_id
			 WHERE DATE(`order_date`) BETWEEN :startDate AND :endDate
			 $filter"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetch();

		return $output['orders'];

	}

	public function count_cancelled() {
		return $this->count('cancelled = 1');
	}

	public function count_pluspage() {
		return $this->count("order_origin = 'Plusseite'");
	}

	public function count_yearly_into_default_subscription() {

		$SQLstatement = $this->db->connection->prepare(
			"SELECT count(*) as orders
			 FROM `conversions`
			 WHERE `subscription_internal_title` LIKE '%12M%' 
			 AND `subscription_price` < 10 
			 AND (`subscription_end_date` is null || `subscription_end_date` > CURRENT_DATE)"
		);

		$SQLstatement->execute();
		$output = $SQLstatement->fetch();
		return $output['orders'];
	}

	public function list_distinct($column) {

		$column = strip_tags($column);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT DISTINCT $column FROM `conversions`
			 WHERE $column is not null
			 ORDER BY $column ASC"
		);
		$SQLstatement->execute();
		return $SQLstatement->fetchall(\PDO::FETCH_COLUMN);

	}

	public function products() {
		return $this->list_distinct('subscription_title');
	}

	public function product_titles() {
		return $this->list_distinct('subscription_internal_title');
	}

	public function order_payment_methods() {
		return $this->list_distinct('order_payment_method');
	}

	public function order_segments() {
		return $this->list_distinct('customer_order_segment');
	}

	public function order_ressorts() {
		return $this->list_distinct('article_ressort');
	}

	public function order_origins() {
		return $this->list_distinct('order_origin');
	}

	public function order_referer_source() {
		return $this->list_distinct('referer_source');
	}

	public function order_referer_source_grouped() {
		return $this->list_distinct('referer_source_grouped');
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


	public function group_by($index, $filter = null) {
		$orders = $this->list($filter);
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


	public function active_after_days($days = 30) {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT DATE_FORMAT(`order_date`, '%Y-%m') as order_month, count(*) as active
			 FROM `conversions`
			 WHERE DATE(`order_date`) BETWEEN :startDate AND :endDate
			 #AND (`cancelled` IS NULL OR `cancelled` = 0)
			 #AND DATEDIFF(CURDATE(), order_date) > :days
			 AND (DATEDIFF(subscription_cancellation_date, order_date) > :days OR (`cancelled` IS NULL OR `cancelled` = 0))
			 AND DATEDIFF(CURDATE(), order_date) > :days
			 GROUP BY order_month
			 ORDER BY order_date DESC
			 "
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to, ':days' => $days]);
		$output = $SQLstatement->fetchall(\PDO::FETCH_UNIQUE);

		return $output;

	}

	public function customers_timespan($timeunit = 'month', $filterCancelled = true) {

		$from = '2021-04-01';
		$to = '2050-01-01';

		$timeunit = strip_tags($timeunit);
		switch ($timeunit) {
			case 'day': $timeunit = 'day'; break;
			case 'week': $timeunit = 'week'; break;
			case 'year': $timeunit = 'year'; break;
			case 'quarter': $timeunit = 'quarter'; break;
			default: $timeunit = 'month'; break;
		}

		if ($filterCancelled == true) {
			$filterCancelled = "AND (`cancelled` IS NULL OR `cancelled` = 0)";
		} else {$filterCancelled = '';}

		$SQLstatement = $this->db->connection->prepare(
			"SELECT TIMESTAMPDIFF($timeunit, CURDATE(), order_date)*-1 as timespan, count(*) as orders

			 FROM `conversions`
			 WHERE DATE(`order_date`) BETWEEN :startDate AND :endDate
			 $filterCancelled
			 AND CURDATE() > order_date

			 GROUP BY timespan
			 ORDER BY order_date DESC
			 "
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall(\PDO::FETCH_UNIQUE);

		return $output;

	}

	public function product_development($productName = '12m') {

		$longterm = new Longterm();
		$orders = new Orders();
		$periods = $longterm->monthly_date_range('2021-04-01', date('Y-m-d', strtotime('last day of previous month')));

		$productName = strip_tags($productName);

		$output = [];
		foreach ($periods as $period) {
			$orders->from = $period['from'];
			$orders->to = $period['to'];
			$amount = $orders->count("`subscription_internal_title` LIKE '%$productName%'");

			if ($amount == 0) {continue;}
			$dimension = $period['month']->format('Y-m');
			$output[$dimension][$productName] = $amount;
		}

		return $output;

	}


	public function cancelled_days_ago($days = 3) {

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `conversions`
			 WHERE DATE(`subscription_cancellation_date`) >= DATE_SUB(CURRENT_DATE, INTERVAL :days DAY)
			 "
		);

		$SQLstatement->execute([':days' => $days]);
		$output = $SQLstatement->fetchall();

		return $output;

	}

	public function cancelled_by_retention_days($filter = null) {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		if (!is_null($filter)) {$filter = 'AND ' . strip_tags($filter);}

		$SQLstatement = $this->db->connection->prepare(

			"SELECT
			 conversions.retention as days,
			 count(conversions.order_id) as cancelled_orders

			 FROM conversions
			 WHERE DATE(conversions.order_date) BETWEEN :startDate AND :endDate
			 AND conversions.cancelled != 0
			 $filter

			 GROUP BY conversions.retention
			 ORDER BY CAST(days AS UNSIGNED) ASC"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$orders = $SQLstatement->fetchall(\PDO::FETCH_UNIQUE);

		return $orders;

	}

	public function cancelled_by_retention_days_with_article_join($filter = null) {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		if (!is_null($filter)) {$filter = 'AND ' . strip_tags($filter);}

		// Join Articles deactivated until needed
		$SQLstatement = $this->db->connection->prepare(

			"SELECT
			 conversions.retention as days,
			 count(conversions.order_id) as cancelled_orders

			 FROM conversions
			 LEFT JOIN articles ON articles.id = conversions.article_id
			 WHERE DATE(conversions.order_date) BETWEEN :startDate AND :endDate
			 AND conversions.cancelled != 0
			 $filter

			 GROUP BY conversions.retention
			 ORDER BY CAST(days AS UNSIGNED) ASC"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$orders = $SQLstatement->fetchall(\PDO::FETCH_UNIQUE);
		return $orders;

	}

	public function cancelled_by_retention_days_chart($filter = null) {
		$charts = new Charts();
		return $charts->cancelled_by_retention_days();
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

	public function is_first_order($customerID, $orderDate) {

		$SQLstatement = $this->db->connection->prepare(

			"SELECT * FROM conversions
			 WHERE customer_id = :customerID
			 AND order_date < :orderDate"
		);

		$SQLstatement->execute([':customerID' => $customerID, ':orderDate' => $orderDate]);
		$orders = $SQLstatement->fetchall();

		if (count($orders) == 0) {return true;}
		return false;
	}


	public function filter_cancelled($orders) {
		if (empty($orders)) {return [];}
		return array_filter($orders, function($order) {
			if ($order['cancelled']) {return $order;}
		});
	}

	public function filter_first_time($orders) {
		if (empty($orders)) {return [];}
		return array_filter($orders, function($order) {
			if ($order['order_first']) {return $order;}
		});
	}

	public function filter_buy_segment($orders, $segment = '') {
		if (empty($orders)) {return [];}
		return array_filter($orders, function($order) use ($segment) {
			if ($order['customer_order_segment'] == $segment) {return $order;}
		});
	}

	public function filter_cancel_segment($orders, $segment = '') {
		if (empty($orders)) {return [];}
		return array_filter($orders, function($order) use ($segment) {
			if ($order['customer_cancel_segment'] == $segment) {return $order;}
		});
	}

	public function filter_cancel_reason($orders, $reason = '') {
		if (empty($orders)) {return [];}
		return array_filter($orders, function($order) use ($reason) {
			if ($order['customer_order_segment'] == $reason) {return $order;}
		});
	}

	public function filter_plus_only($orders) {
		if (empty($orders)) {return [];}
		return array_filter($orders, function($order) {
			if ($order['order_origin'] == 'Plusseite') {return $order;}
		});
	}

	public function filter_push_only($orders) {
		if (empty($orders)) {return [];}
		return array_filter($orders, function($order) {
			if ($order['referer_source'] == 'Push') {return $order;}
		});
	}

	public function filter_external($orders) {
		if (empty($orders)) {return [];}
		return array_filter($orders, function($order) {
			if ($order['order_origin'] == 'Extern') {return $order;}
		});
	}

	public function filter_aboshop($orders) {
		if (empty($orders)) {return [];}
		return array_filter($orders, function($order) {
			if ($order['order_origin'] == 'Aboshop') {return $order;}
		});
	}

	public function filter_yearly($orders) {
		if (empty($orders)) {return [];}
		return array_filter($orders, function($order) {
			$stringPosition = strpos($order['subscription_internal_title'], '12M');
			if ($stringPosition !== false) {return $order;}
		});
	}

	public function filter_umwandlung($orders) {
		if (empty($orders)) {return [];}
		return array_filter($orders, function($order) {
			if ($order['order_origin'] == 'Umwandlung') {return $order;}
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


	public function import($date, $ignoreCancelled = false) {

		// don't do Imports prior to Plenigo V3
		if ($date < '2021-03-23') {
			return ['Error' => 'Can´t Import prior to 2021-03-23 (PlenigoV3)'];
		}

		$plenigo = new Plenigo();
		$orderList = $plenigo->orders($date, $date);

		if ($ignoreCancelled) {
			$orderList = $this->filter_cancelled_from($orderList);
		}

		$detailedOrders = [];
		foreach ($orderList as $order) {
			$details = $plenigo->order_with_details($order['order_id']);
			array_push($detailedOrders, $details);
		}

		// Save to DB
		foreach ($detailedOrders as $order) {

			// Check if the Order is a first time Customer
			$order['order_first'] = $this->is_first_order($order['customer_id'], $order['order_date']);
			$this->create_or_update($order);

			if (isset($order['article_id'])) {
				$conversions = new Conversions();
				$conversions->articleID = $order['article_id'];
				$conversions->collect();
				$conversions->update_article();
			}

		}

		return $detailedOrders;

	}

	private function filter_cancelled_from($orders) {

		$orderIDs = array_column($orders,'order_id');
		$ordersInDB = $this->get($orderIDs, ['order_id', 'cancelled']);

		$cancelledOrders = array_filter($ordersInDB, function($order) {
			if ($order['cancelled'] == true) {return $order;}
		});

		$cancelledOrders = array_column($cancelledOrders, 'order_id');

		foreach ($orders as $key => $order) {
			if (in_array($order['order_id'], $cancelledOrders)) {
				unset($orders[$key]);
			}
		}

		sort($orders);
		return $orders;

	}


	public function yearly_running() {

		$SQLstatement = $this->db->connection->prepare(
			"
			 SELECT count(*) as abos
			 FROM `conversions`
			 #WHERE subscription_internal_title like '%12m%'
			 WHERE subscription_price > 10
			 AND (subscription_end_date IS NULL OR subscription_end_date = '' OR subscription_end_date >= NOW())
			"
		);

		$SQLstatement->execute();
		$output = $SQLstatement->fetch(\PDO::FETCH_COLUMN);

		return $output;

	}

	public function probe_running() {

		$probepreis = 7;
		if (PORTAL == 'SWP') {$probepreis = 9;}

		$SQLstatement = $this->db->connection->prepare(
			"
			 SELECT count(*) as abos
			 FROM `conversions`
			 WHERE subscription_price < $probepreis
			 AND LENGTH(order_id) < 10
			 AND (subscription_end_date IS NULL OR subscription_end_date = '' OR subscription_end_date >= NOW())
			 AND order_date <= NOW()
			"
		);

		$SQLstatement->execute();
		$output = $SQLstatement->fetch(\PDO::FETCH_COLUMN);

		return $output;

	}


}
