<?php

namespace app\models;
use \flundr\mvc\Model;
use \flundr\database\SQLdb;
use \flundr\utility\Session;
use \flundr\cache\RequestCache;
use \app\models\Plenigo;
use \app\models\Orders;
use \app\models\DailyKPIs;
use \app\models\Conversions;

class Subscriptions extends Model
{

	public $from = '0000-00-00';
	public $to = '3000-01-01';

	function __construct() {

		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'conversions';
		$this->db->primaryIndex = 'subscription_id';
		$this->db->orderby = 'subscription_id';

		$this->from = date('Y-m-d', strtotime(DEFAULT_FROM));
		$this->to = date('Y-m-d', strtotime(DEFAULT_TO));

		if (Session::get('from')) {$this->from = Session::get('from');}
		if (Session::get('to')) {$this->to = Session::get('to');}

	}


	public function update_last_days() {

		$Plenigo = new Plenigo();
		$DailyKPIs = new DailyKPIs();
		$Orders = new Orders();

		$start = 'today -2 days';
		$end = 'today';

		$start = date("Y-m-d", strtotime($start));
		$end = date("Y-m-d", strtotime($end));

		$changed = $Plenigo->changed_subscriptions($start,$end);

		foreach ($changed['cancellations'] as $date => $cancellations) {
			$DailyKPIs->create_or_update(['date' => $date, 'cancellations' => $cancellations]);
		}

		$updatedOrderIDs = [];
		foreach ($changed['subscriptions'] as $subscription) {
			if ($subscription['order_id']) {
				array_push($updatedOrderIDs, $subscription['order_id']);

				// Calculate the Retention Date
				if ($subscription['subscription_cancellation_date']) {
					$currentOrder = $Orders->get($subscription['order_id'],['order_date']);
					$start = new \DateTime(formatDate($currentOrder['order_date'], 'Y-m-d'));
					$end = new \DateTime(formatDate($subscription['subscription_cancellation_date'], 'Y-m-d'));
					$interval = $start->diff($end);
					$subscription['retention'] = $interval->format('%r%a');
				}

				$Orders->update($subscription, $subscription['order_id']);
			}
		}

		foreach ($updatedOrderIDs as $orderID) {
			$this->update_article_table($orderID);
		}

		return $updatedOrderIDs;

	}

	private function update_article_table($orderID) {

		$Orders = new Orders();
		$order = $Orders->get($orderID);
		if (empty($order)) {return;}

		if (isset($order['article_id'])) {
			$conversions = new Conversions();
			$conversions->articleID = $order['article_id'];
			$conversions->collect();
			$conversions->update_article();
		}

	}

}
