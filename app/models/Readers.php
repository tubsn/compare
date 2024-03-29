<?php

namespace app\models;
use \flundr\mvc\Model;
use \flundr\database\SQLdb;
use	\app\importer\DPA_Drive_User;
use \app\importer\BigQuery;
use	\app\models\Orders;
use	\app\models\DailyKPIs;
use \flundr\cache\RequestCache;
use \flundr\utility\Session;

class Readers extends Model
{

	function __construct() {
		$this->api = new DPA_Drive_User();
		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'readers';
		$this->db->primaryIndex = 'user_id';
		$this->db->orderby = 'date';
	}


	public function list() {

		$table = $this->db->table;
		$SQLstatement = $this->db->connection->prepare("SELECT * FROM $table");
		$SQLstatement->execute();
		$output = $SQLstatement->fetchall();
		return $output;

	}

	public function stats() {

		$table = $this->db->table;
		$SQLstatement = $this->db->connection->prepare(
			"SELECT order_fav_thema, count(user_id) as users
			FROM $table

			#LEFT JOIN conversions ON `user_id` = conversions.customer_id
			GROUP BY order_fav_thema
			"
		);
		$SQLstatement->execute();
		$output = $SQLstatement->fetchall(\PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
		return $output;

	}



	public function update_latest_orders() {

		$orders = new Orders();
		$orders->from = date('Y-m-d', strtotime('today -4 days'));
		$orders->to = date('Y-m-d', strtotime('today'));
		$orderList = $orders->list();

		$updated = 0;
		foreach ($orderList as $order) {
			$userData = $this->collect_user_data($order, 'order_date');
			if (is_null($userData)) {continue;}

			$favoriteData = $this->collect_reader_favorites($userData, 'order');
			if (!empty($favoriteData)) {$this->create_or_update($favoriteData);}

			$orderData = $this->order_table_field_mapping($userData);
			$orders->update($orderData, $order['order_id']);

			$updated++;
		}

		echo '[' . $updated . '/' . count($orderList) . '] Bestelleintraege erneuert | ' . date('H:i:s') . "\r\n";

	}

	public function update_latest_cancellations() {

		$orders = new Orders();
		$orderList = $orders->cancelled_days_ago(4);

		$updated = 0;
		foreach ($orderList as $order) {
			$userData = $this->collect_user_data($order, 'subscription_cancellation_date');
			if (is_null($userData)) {continue;}

			$favoriteData = $this->collect_reader_favorites($userData, 'cancel');
			if (!empty($favoriteData)) {$this->create_or_update($favoriteData);}

			$cancellationData = $this->cancellation_table_field_mapping($userData);
			$orders->update($cancellationData, $order['order_id']);

			$updated++;
		}

		echo '[' . $updated . '/' . count($orderList) . '] Kuendigereintraege erneuert | ' . date('H:i:s') . "\r\n";

	}

	private function order_table_field_mapping($user) {
		$data['customer_order_segment'] = $user['user_segment'];
		$data['customer_order_mediatime'] = round($user['media_time_last_week'],2);
		return $data;
	}

	private function cancellation_table_field_mapping($user) {
		$data['customer_cancel_segment'] = $user['user_segment'];
		$data['customer_cancel_mediatime'] = round($user['media_time_last_week'],2);
		return $data;
	}

	private function collect_user_data($order, $activityDateField = 'order_date') {

		$customerID = $order['customer_id'];
		//$user = $this->get_drive_api_segment_data($customerID);

		try {$data = $this->api->get($customerID);}
		catch (\Exception $e) {return null;}

		// Look at Orderdate or Cancellationdate for usefullness of Segments
		if (!$this->userdata_is_usefull($data, $order[$activityDateField])) {return null;}

		$user['user_id'] = $customerID;
		$user['articles_read'] = $data['articles_read_last_week'];
		$user['user_segment'] = $data['classifications']['engagement_segment'];
		if ($user['user_segment'] == 'unknown') {$user['user_segment'] = null;}
		$user['conversion_score'] = $data['scores']['conversion_propensity_score'] ?? null;
		$user['media_time_last_week'] = $data['engagement']['media_time_last_week'] ?? null;
		$user['media_time_total'] = $data['engagement']['media_time_total'] ?? null;
		$user['date'] = date('Y-m-d', strtotime($data['last_seen']));

		return $user;

	}

	private function userdata_is_usefull($user, $orderDate) {
		if (empty($user)) {return false;}
		if ($this->number_of_days($user['last_seen'], $orderDate) > 6) {return false;}
		return true;
	}

	private function collect_reader_favorites($data, $prefix = '') {

		if (empty($data['articles_read'])) {return [];}

		$user['user_id'] = $data['user_id'];
		$articleData = $this->read_articles($data['articles_read']);

		if (!empty($prefix)) {$prefix = $prefix . '_';}
		$user[$prefix . 'articles_read'] = json_encode($data['articles_read']);
		$user[$prefix . 'fav_ressort'] = key($this->favorites($articleData, 'ressort'));
		$user[$prefix . 'fav_audience'] = key($this->favorites($articleData, 'audience'));
		$user[$prefix . 'fav_thema'] = key($this->favorites($articleData, 'type'));

		return $user;

	}

	public function filter_active($orders) {

		$subscription = array_filter($orders, function($order) {
			if ($order['subscription_status'] == 'ACTIVE') {return $order;}
		});
		return $subscription[0] ?? null;

	}

	public function favorites($data, $key = 'article_ressort', $backupKey = null) {
		if (is_null($data)) {return null;}

		$sources = [];
		foreach ($data as $set) {
			if (isset($set[$key]) && !empty($set[$key])) {
				array_push($sources, $set[$key]);
				continue;
			}

			if (isset($set[$backupKey]) && !empty($set[$backupKey])) {
				array_push($sources, $set[$backupKey]);
			}
		}
		$favorites = array_count_values($sources);
		arsort($favorites);
		return $favorites;
	}


	private function number_of_days($firstDate, $secondDate) {

		$firstDate  = new \DateTime($firstDate);
		$secondDate = new \DateTime($secondDate);
		$interval = $firstDate->diff($secondDate);
		return $interval->days;

	}


	public function live_from_api($id) {

		try {$user = $this->api->get($id);}
		catch (\Exception $e) {
			$user['user_id'] = $id;
			$user['portal'] = null;
			$user['first_seen'] = null;
			$user['last_seen'] = null;
			$user['articles_read'] = null;
			$user['user_segment'] = 'unbekannt';
			$user['media_time_last_week'] = 0;
			$user['media_time_total'] = 0;
			$user['conversion_score'] = 0;
			$user['error'] = '(Drive-API: ' . $e->getMessage() . ')';
			return $user;
		}

		$user['articles_read'] = $this->read_articles($user['articles_read_last_week']);
		$user['user_segment'] = $user['classifications']['engagement_segment'] ?? null;
		$user['media_time_last_week'] = $user['engagement']['media_time_last_week'] ?? null;
		$user['media_time_total'] = $user['engagement']['media_time_total'] ?? null;
		$user['conversion_score'] = $user['scores']['conversion_propensity_score'] ?? null;

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

	public function import_user_segments($from, $to) {

		$dailyKPIs = new DailyKPIs();
		$days = $this->drive_active_user_segments($from, $to);

		foreach ($days as $day) {
			//$date = $day['date']; unset($day['date']);
			$dailyKPIs->create_or_update($day);
		}

		return $days;
	}

	public function import_user_segments_old($from, $to) {
		$segments = $this->drive_active_user_segments($from, $to);
		$this->save_segments_to_db($segments);
		return $segments;
	}


	private function save_segments_to_db($csv) {

		$segmentDatabaseMapping = [
			'low-usage-irregular' => 'low_usage_irregulars',
			'high-usage-irregular' => 'high_usage_irregulars',
			'loyal' => 'loyals',
			'champion' => 'champions',
			'fly-by' => 'flybys',
			'non-engaged' => 'nonengaged',
			'unknown' => 'unknown',
		];

		$segmentFieldName = 'user_engagement_segment';
		$usersFieldName = 'users';
		$registeredUsersFieldName = 'registered_users';
		$registeredSuffix = '_reg';
		$dateFieldName = 'date';

		$data = array_group_by($dateFieldName, $csv);

		$segmentsByDate = [];
		foreach ($data as $date => $segmentSets) {

			foreach ($segmentSets as $sets) {
				$mappedSegment = $segmentDatabaseMapping[$sets[$segmentFieldName]];
				$segmentsByDate[$date][$mappedSegment] = $sets[$usersFieldName];
				$segmentsByDate[$date][$mappedSegment . $registeredSuffix] = $sets[$registeredUsersFieldName];
			}

			$segmentsByDate[$date]['users'] = array_sum(array_column($segmentSets, $usersFieldName));
			$segmentsByDate[$date]['subscribers'] = array_sum(array_column($segmentSets, $registeredUsersFieldName));

		}

		ksort($segmentsByDate);

		$dailyKPIs = new DailyKPIs();
		foreach ($segmentsByDate as $day => $segmentData) {
			$dailyKPIs->update($segmentData,$day);
		}

	}

	public function drive_active_user_segments($from, $to) {

		$bigQueryApi = new BigQuery;
		$publisher = PORTAL_PUBLISHER_ID;

		$query =
			"
			SELECT DATE(`start_tstamp`) AS `date`,

			count(distinct domain_userid) as users,
			count(distinct if(user_type = 'premium', domain_userid, null)) as subscribers,

			count(distinct if(user_engagement_group = 'champion', domain_userid, null)) as champions,
			count(distinct if(user_engagement_group = 'champion' and user_engagement_group = 'champion', domain_userid, null)) as champions_reg,

			count(distinct if(user_engagement_group = 'loyal', domain_userid, null)) as loyals,
			count(distinct if(user_engagement_group = 'premium' and user_engagement_group = 'loyal', domain_userid, null)) as loyals_reg,

			count(distinct if(user_engagement_group = 'high-usage-irregular', domain_userid, null)) as high_usage_irregulars,
			count(distinct if(user_engagement_group = 'premium' and user_engagement_group = 'high-usage-irregular', domain_userid, null)) as high_usage_irregulars_reg,

			count(distinct if(user_engagement_group = 'low-usage-irregular', domain_userid, null)) as low_usage_irregulars,
			count(distinct if(user_engagement_group = 'premium' and user_engagement_group = 'low-usage-irregular', domain_userid, null)) as low_usage_irregulars_reg,

			count(distinct if(user_engagement_group = 'fly-by', domain_userid, null)) as flybys,
			count(distinct if(user_engagement_group = 'premium' and user_engagement_group = 'fly-by', domain_userid, null)) as flybys_reg,

			FROM `artikel-reports-tool.DPA_DriveV2.dpa_drive_sessions` 
			WHERE DATE(start_tstamp) >= '$from' AND DATE(start_tstamp) <= '$to'
			AND publisher_id = '$publisher'
			GROUP BY date
			ORDER BY date ASC
			LIMIT 5000
		";

		$data = $bigQueryApi->sql($query);
		return  $data;

	}


	private function drive_active_user_segments_old_drive_DB($from, $to) {

		$bigQueryApi = new BigQuery;
		$publisher = PORTAL;

		$query =
			"SELECT DATE_TRUNC(`page_view_start_local`, DAY) AS `date`,
			       `user_engagement_segment` AS `user_engagement_segment`,
			       COUNT(DISTINCT inferred_user_id) AS `users`,
				   COUNT(DISTINCT CASE WHEN user.user_type='premium' THEN inferred_user_id END) AS `registered_users`
			FROM `artikel-reports-tool.DPA_Drive.dpa_drive_pageviews`
			JOIN
			  (SELECT `user_engagement_segment` AS `user_engagement_segment__`,
			          COUNT(DISTINCT inferred_user_id) AS `mme_inner__`
			   FROM `artikel-reports-tool.DPA_Drive.dpa_drive_pageviews`
			   WHERE `publisher` = '$publisher'
			     AND `page_view_start_local` >= CAST('$from' AS DATE)
			     AND `page_view_start_local` < CAST('$to' AS DATE)
			   GROUP BY `user_engagement_segment__`
			   ORDER BY `mme_inner__` DESC
			   LIMIT 100) AS `anon_1` ON `user_engagement_segment` = `user_engagement_segment__`
			WHERE `page_view_start_local` >= CAST('$from' AS DATE)
			  AND `page_view_start_local` < CAST('$to' AS DATE)
			  AND `publisher` = '$publisher'
			GROUP BY `user_engagement_segment`,
			         `date`
			ORDER BY `users` DESC
			LIMIT 50000;
		";

		$data = $bigQueryApi->sql($query);
		return  $data;

	}

	private function drive_user_list() {

		$bigQueryApi = new BigQuery;
		$publisher = PORTAL;

		$query =
			"SELECT date, RIGHT(inferred_user_id,12) as user_id, days_active_per_week as days_active, time_engaged_per_week as media_time_last_week, user_engagement_segment as user_segment
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


	public function audience_sizes() {

		//$this->splitt_audience_field('LR_Crime_Fans_Registered');

		$data = $this->dwh_audience_sizes();

		$data = array_map(function($day) {
			foreach ($day as $key => $value) {
				if (strTolower($key) == 'datum') {continue;}
				if (strTolower(substr($key,0,2)) != strTolower(substr(PORTAL,0,2))) {unset($day[$key]);}
			}
			return $day;

		}, $data);

		$output = [];
		foreach ($data as $set) {
			$key = $set['Datum']; 
			unset($set['Datum']);
			$output[$key] = $set;
		}
		
		return $output;
	}


	private function splitt_audience_field($fieldName) {
		$lastUnderscorePosition = strrpos($fieldName, '_');
		$firstUnderscorePosition = strpos($fieldName, '_') +1;
		$name = substr($fieldName,0,$lastUnderscorePosition);
		$name = substr($name,$firstUnderscorePosition);
		$name = str_replace('_', ' ', $name);
		return $name;
	}


	private function dwh_audience_sizes() {

		$cache = new RequestCache('audienceDevelopment' . PORTAL, 60 * 60);
		$audiences = $cache->get();
		if (!empty($audiences)) {return $audiences;}

		$bigQueryApi = new BigQuery;
		$query = "
			SELECT * EXCEPT (Datum), FORMAT_DATE('%F', Datum ) AS `Datum`
			FROM `artikel-reports-tool.NDI_Audiences.dpa_drive_audiences_size` 
			ORDER BY Datum ASC
			LIMIT 1000
			";
		$data = $bigQueryApi->sql($query);
		$cache->save($data);

		return  $data;

	}

	public function zip_codes() {

		$bigQueryApi = new BigQuery;
		$query ="
			SELECT day, uid, publisher, city, zipcode FROM `artikel-reports-tool.NDI_Drive.dpa_drive_geo_locations` 
			WHERE publisher = 'LR' AND day = '2022-09-03' LIMIT 1000
		";

		$data = $bigQueryApi->sql($query);

		return  $data;

	}


	public function users_by_city() {
		$bigQueryApi = new BigQuery;
		$query ="
			SELECT count(uid) as users, city FROM `artikel-reports-tool.NDI_Drive.dpa_drive_geo_locations` 
			WHERE 
			publisher = 'LR'
			AND day = '2022-09-03' 
			group by city order by users DESC
			LIMIT 1000
		";

		$data = $bigQueryApi->sql($query);

		return  $data;

	}

	public function users_by_zipcode() {


		$from = date('Y-m-d', strtotime(DEFAULT_FROM));
		$to = date('Y-m-d', strtotime(DEFAULT_TO));

		if (Session::get('from')) {$from = Session::get('from');}
		if (Session::get('to')) {$to = Session::get('to');}

		$cache = new RequestCache('zipcodes'.PORTAL.$from.$to, 60 * 60);
		$zipcodes = $cache->get();

		if (!empty($zipcodes)) {return $zipcodes;}

		$bigQueryApi = new BigQuery;
		$query ="
			SELECT zipcode, count(uid) as users FROM `artikel-reports-tool.NDI_Drive.dpa_drive_geo_locations` 
			WHERE 
			publisher = '".PORTAL."'
			AND day between '$from' AND '$to' 
			group by zipcode order by users DESC
			LIMIT 1000
		";

		$data = $bigQueryApi->sql($query);
		$zipcodes = array_column($data, 'users', 'zipcode');
		$cache->save($zipcodes);
		return $zipcodes;

	}



}
