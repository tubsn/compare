<?php

namespace app\models;
use	\app\importer\DPA_Drive_User;

class Readers
{

	function __construct() {
		$this->api = new DPA_Drive_User();
	}

	public function get($id) {
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

}
