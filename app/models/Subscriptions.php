<?php

namespace app\models;
use \flundr\mvc\Model;
use \flundr\database\SQLdb;
use \flundr\utility\Session;
use \flundr\cache\RequestCache;
use \app\models\Plenigo;
use \app\models\Orders;
use \app\models\DailyKPIs;

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
				$Orders->update($subscription, $subscription['order_id']);
			}
		}

		return $updatedOrderIDs;

	}

}
