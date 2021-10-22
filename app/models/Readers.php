<?php

namespace app\models;
use \flundr\mvc\Model;
use \flundr\database\SQLdb;
use	\app\importer\DPA_Drive_User;
use \app\importer\BigQuery;
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

	public function get_from_api($id) {
		$user = $this->api->get($id);
		$user['lastArticles'] = $this->read_articles($user['articles_read_last_week']);
		$user['segment'] = $user['classifications']['engagement_segment'] ?? null;
		$user['media_time_last_week'] = $user['engagement']['media_time_last_week'] ?? null;
		$user['media_time_total'] = $user['engagement']['media_time_total'] ?? null;
 		
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
			$this->create_or_update($user);
		}

	}

	private function drive_user_list() {

		$bigQueryApi = new BigQuery;
		$publisher = PORTAL;
		$query = 
			"SELECT date, RIGHT(inferred_user_id,12) as user_id, days_active_per_week as days_active, time_engaged_per_week as engagement_time, user_engagement_segment as user_segment
			FROM `artikel-reports-tool.DPA_Drive.dpa_drive_users`
			WHERE site.publisher = '$publisher'
			AND user_type = 'premium'
			AND RIGHT(inferred_user_id,12) LIKE '801%'
			and date >= DATE_SUB(CURRENT_DATE, INTERVAL 2 DAY)
			LIMIT 10000
			";

		$data = $bigQueryApi->sql($query);
		return  $data;
	}


}
