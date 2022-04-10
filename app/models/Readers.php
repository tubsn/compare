<?php

namespace app\models;
use \flundr\mvc\Model;
use \flundr\database\SQLdb;
use	\app\importer\DPA_Drive_User;
use \app\importer\BigQuery;
use	\app\models\Orders;
use \flundr\cache\RequestCache;

class Readers extends Model
{

	function __construct() {
		$this->api = new DPA_Drive_User();
		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'readers';
		$this->db->primaryIndex = 'user_id';
		$this->db->orderby = 'date';
	}

	public function add_segment_to_latest_orders() {

		$orders = new Orders();
		$orders->from = date('Y-m-d', strtotime('today -4 days'));
		$orders->to = date('Y-m-d', strtotime('today'));
		$orderList = $orders->list();

		$updated = 0;
		foreach ($orderList as $order) {
			$userData = $this->collect_segment_data($order);
			if (is_null($userData)) {continue;}
			$orders->update($userData, $order['order_id']);
			$updated++;
		}

		echo '[' . $updated . '/' . count($orderList) . '] Bestelleintraege erneuert | ' . date('H:i:s') . "\r\n";

	}

	public function add_segement_to_latest_cancellations() {

		$orders = new Orders();
		$orderList = $orders->cancelled_days_ago(4);

		$updated = 0;
		foreach ($orderList as $order) {
			$userData = $this->collect_cancellation_segment_data($order);
			if (is_null($userData)) {continue;}
			$orders->update($userData, $order['order_id']);
			$updated++;
		}

		echo '[' . $updated . '/' . count($orderList) . '] Kuendigereintraege erneuert | ' . date('H:i:s') . "\r\n";

	}

	private function collect_segment_data($order, $cancellations = false) {

		$customerID = $order['customer_id'];
		$user = $this->get($customerID);

		// Look at Orderdate or Cancellationdate for usefullness of Segments
		$activityDate = $order['order_date'];
		if ($cancellations) {$activityDate = $order['subscription_cancellation_date'];}

		// Check if BigQueryDB User is too old use DPA User API
		if (!$this->userdata_is_usefull($user, $activityDate)) {
			$user = $this->get_drive_api_segment_data($customerID);
		}

		// If DPA User is also to old don't do anything at all
		if (!$this->userdata_is_usefull($user, $activityDate)) {return null;}

		if ($user['user_segment'] == 'unknown') {$user['user_segment'] = null;}

		if ($cancellations) {
			$data['customer_cancel_segment'] = $user['user_segment'];
			$data['customer_cancel_mediatime'] = round($user['engagement_time'],2);
		}
		else {
			$data['customer_order_segment'] = $user['user_segment'];
			$data['customer_order_mediatime'] = round($user['engagement_time'],2);
		}

		return $data;

	}

	private function collect_cancellation_segment_data($customerID) {
		return $this->collect_segment_data($customerID,true);
	}	

	private function userdata_is_usefull($user, $orderDate) {
		if (empty($user)) {return false;}
		if ($this->number_of_days($user['date'], $orderDate) > 6) {return false;}
		return true;
	}

	private function number_of_days($firstDate, $secondDate) {

		$firstDate  = new \DateTime($firstDate);
		$secondDate = new \DateTime($secondDate);
		$interval = $firstDate->diff($secondDate);
		return $interval->days;

	}


	public function get_from_api($id) {
		$user = $this->api->get($id);
		$user['lastArticles'] = $this->read_articles($user['articles_read_last_week']);
		$user['segment'] = $user['classifications']['engagement_segment'] ?? null;
		$user['media_time_last_week'] = $user['engagement']['media_time_last_week'] ?? null;
		$user['media_time_total'] = $user['engagement']['media_time_total'] ?? null;
 		
		return $user;
	}

	public function get_drive_api_segment_data($id) {

		try {$data = $this->api->get($id);}
		catch (\Exception $e) {return null;}

		$user['user_segment'] = $data['classifications']['engagement_segment'];
		$user['engagement_time'] = $data['engagement']['media_time_last_week'];
		$user['days_active'] = null;
		$user['date'] = date('Y-m-d', strtotime($data['last_seen']));

		return $user;
		
	}

	private function read_articles($IDs) {
		if (empty($IDs)) {return null;}
		$articleDB = new Articles();
		return $articleDB->get($IDs);
	}


	public function import_readers() {
		$cache = new RequestCache('userlist', 0 * 60);
		$users = $cache->get();

		if (!$users) {
			$users = $this->drive_user_list();
			$cache->save($users);			
		}

		foreach ($users as $user) {
			if (empty($user['user_segment']) || $user['user_segment'] == 'unknown') {continue;}
			$this->create_or_update($user);
		}

	}

	private function drive_user_list() {

		$bigQueryApi = new BigQuery;
		$publisher = PORTAL;

		$query = 
			"SELECT date, RIGHT(inferred_user_id,12) as user_id, days_active_per_week as days_active, time_engaged_per_week as engagement_time, user_engagement_segment as user_segment
			FROM `artikel-reports-tool.DPA_Drive.dpa_drive_users`
			WHERE publisher = '$publisher'
			AND user_type = 'premium'
			AND RIGHT(inferred_user_id,12) LIKE '801%'
			and date >= DATE_SUB(CURRENT_DATE, INTERVAL 2 DAY)
			LIMIT 10000
			";

		$data = $bigQueryApi->sql($query);

		return  $data;
	}


}
