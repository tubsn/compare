<?php

namespace app\models;
use \flundr\mvc\Model;
use	\app\importer\CleverpushAPI;
use	\app\models\Articles;
use \flundr\cache\RequestCache;
use \flundr\utility\Session;

class Cleverpush
{

	public $from = '0000-00-00';
	public $to = '3000-01-01';
	public $cacheDirectory = ROOT . 'cache' . DIRECTORY_SEPARATOR . 'push';

	function __construct() {
		$this->api = new CleverpushAPI();
		$this->Articles = new Articles();

		$this->from = date('Y-m-d', strtotime(DEFAULT_FROM));
		$this->to = date('Y-m-d', strtotime(DEFAULT_TO));

		if (Session::get('from')) {$this->from = Session::get('from');}
		if (Session::get('to')) {$this->to = Session::get('to');}

	}



	public function click_stats() {

		$cache = new RequestCache('pushHourly-' . PORTAL , 24*60*60);
		$cache->cacheDirectory = $this->cacheDirectory;
		$stats = $cache->get();

		if (!$stats) {

			$notifications = $this->api->notifications($this->from, $this->to, 500);

			$stats = [];
			foreach ($notifications as $notification) {
				$statsData = $this->api->notification_stats($notification['_id']);
				if (empty($statsData)) {continue;}

				$sentAtHour = date("H", strtotime($notification['sentAt']));

				$statsData = $this->clicks_by_hour($statsData);
				$statsData[$sentAtHour]['created'] = 1;

				array_push($stats, $statsData);
			}

			$cache->save($stats);

		}

		$out = [];
		foreach ($this->hours_with_zero() as $hour) {

			$out[$hour]['created'] = 0;
			$out[$hour]['delivered'] = 0;
			$out[$hour]['clicks'] = 0;
			$out[$hour]['clickrate'] = 0;
			$out[$hour]['clickCreateRate'] = 0;

			foreach ($stats as $set) {
				if (isset($set[$hour])) {
					$out[$hour]['delivered'] = $out[$hour]['delivered'] + $set[$hour]['delivered'] ?? 0;
					$out[$hour]['clicks'] = $out[$hour]['clicks'] + $set[$hour]['clicks'] ?? 0;
					if (isset($set[$hour]['created'])) {
						$out[$hour]['created'] = $out[$hour]['created'] + $set[$hour]['created'];
					}
				}
			}

			if ($out[$hour]['created'] > 3) {
				$out[$hour]['clickrate'] = round($out[$hour]['clicks'] / $out[$hour]['delivered'],4);
				$out[$hour]['clickCreateRate'] = round($out[$hour]['clicks'] / $out[$hour]['delivered'],4);
			}

		}

		return $out;
	}

	private function hours_with_zero() {

		return array_map( function( $hour ) {
		    return str_pad( $hour, 2, '0', STR_PAD_LEFT );
		}, range(0, 23) );
	}


	public function stats() {

		$cache = new RequestCache('pushStats-' . PORTAL , 30*60);
		$cache->cacheDirectory = $this->cacheDirectory;
		$stats = $cache->get();

		if (!$stats) {
			$stats = $this->api->channel_stats();
			$cache->save($stats);
		}

		return $stats;

	}

	public function subscribers() {
		return $this->api->channel_subscription_stats();
	}

	public function segment_stats() {
		$segments = $this->segments();
		$stats = [];
		foreach ($segments as $segment) {
			$stats[$segment['name']] = [
				'subscriptions' => $segment['subscriptions'],
				'inactiveSubscriptions' => $segment['inactiveSubscriptions']
			];
		}

		return $stats;
	}

	public function segments() {
		$segments = $this->api->channel_segments();
		$segments = array_map([$this, 'map_channel_segment'], $segments);
		return $segments;
	}

	public function get($id = 'oFvWgvX5JKLv8nCZG') {
		$notification = $this->api->notification($id);
		return $this->map_notification_with_article($notification);
	}


	public function list($limit = null) {
		$notifications = $this->api->notifications($this->from, $this->to, $limit);
		$notifications = array_map([$this, 'map_notification_with_article'], $notifications);
		return $notifications;
	}

	public function today() {
		$notifications = $this->api->notifications_today();
		$notifications = array_map([$this, 'map_notification_with_article'], $notifications);
		return $notifications;
	}

	private function map_notification_with_article($data) {
		return $this->map_notification($data, true);
	}


	private function map_channel_segment($segment) {
		$out['id'] = $segment['_id'];
		$out['name'] = $segment['name'];
		$out['subscriptions'] = $segment['subscriptions'];
		$out['inactiveSubscriptions'] = $segment['inactiveSubscriptions'];
		return $out;
	}

	private function map_notification($data, $embedArticleData = false) {

		$out['id'] = $data['_id'] ?? null;
		$out['status'] = $data['status'] ?? null;
		$out['title'] = $data['title'] ?? null;
		$out['thumb'] = $data['mediaUrl'] ?? null;
		$out['url'] = $data['url'] ?? null;
		$out['text'] = $data['text'] ?? null;
		$out['expire'] = date("Y-m-d H:i:s", strtotime($data['expiresAt']));
		$out['created'] = date("Y-m-d H:i:s", strtotime($data['createdAt']));
		$out['queuedAt'] = date("Y-m-d H:i:s", strtotime($data['queuedAt']));

		$out['sentAt'] = null;
		if (isset($data['sentAt'])) {$out['sentAt'] = date("Y-m-d H:i:s", strtotime($data['sentAt']));}

		$out['subscriptionCount'] = $data['subscriptionCount'] ?? null;
		$out['subscriptionCountEstimated'] = $data['subscriptionCountEstimated'] ?? null;
		$out['delivered'] = $data['delivered'] ?? null;
		$out['errors'] = $data['errorCount'] ?? null;
		$out['optOuts'] = $data['optOuts'] ?? null;
		$out['optOutsTotal'] = $data['optOutsTotal'] ?? null;
		$out['clicks'] = $data['clicked'] ?? null;

		$out['clickrate'] = null;
		$out['clickrate_sort'] = null;
		$out['clickrate_level'] = 0;

		if ($out['clicks']) {
			$out['clickrate'] = percentage($out['clicks'], $out['delivered'],1);
			$out['clickrate_sort'] = number_format($out['clicks'] / $out['delivered'],3);

			if ($out['clickrate_sort'] <= 0.01) {$out['clickrate_level'] = 0;}
			if ($out['clickrate_sort'] >= 0.01) {$out['clickrate_level'] = 1;}
			if ($out['clickrate_sort'] >= 0.013) {$out['clickrate_level'] = 2;}
			if ($out['clickrate_sort'] >= 0.018) {$out['clickrate_level'] = 3;}
			if ($out['clickrate_sort'] >= 0.025) {$out['clickrate_level'] = 4;}
			if ($out['clickrate_sort'] >= 0.05) {$out['clickrate_level'] = 5;}
		}

		$out['paywall_clicks'] = null;
		if (isset($data['conversionEvents'])) {
			$out['paywall_clicks'] = array_sum(array_column($data['conversionEvents'], 'conversions'));
		}

		$out['hourly_stats'] = null;
		if (isset($data['stats'])) {
			$out['hourly_stats'] = $this->clicks_by_timestamp($data['stats']);
		}

		$out['article_id'] = $this->extract_id($data['url']);

		if ($embedArticleData) {
			$out['article'] = $this->Articles->get($out['article_id']);
		}

		return $out;

	}

	private function extract_id($url) {
		// Regex search for the ID = -8Digits.html
		$searchPattern = "/-(\d{8}).html/";
		preg_match($searchPattern, $url, $matches);
		if (isset($matches[1])) {
			return $matches[1]; // First Match should be the ID
		}
		return null;
	}

	private function clicks_by_timestamp($stats) {
		$out = [];
		foreach ($stats as $data) {
			$date = date("Y-m-d H:i", strtotime($data['date']));
			$out[$date]['clicks'] = $data['clicked'] ?? 0;
			$out[$date]['delivered'] = $data['delivered'] ?? 0;
		}

		return $out;
	}

	private function clicks_by_hour($stats) {

		$out = [];
		foreach ($stats as $data) {

			$date = date("H", strtotime($data['date']));
			if (!isset($out[$date]['clicks'])) {$out[$date]['clicks'] = [];}
			if (!isset($out[$date]['delivered'])) {$out[$date]['delivered'] = [];}

			$out[$date]['clicks'] = $data['clicked'] ?? 0;
			$out[$date]['delivered'] = $data['delivered'] ?? 0;

			//array_push($out[$date]['clicks'], $data['clicked'] ?? 0);
			//array_push($out[$date]['delivered'], $data['delivered'] ?? 0);
		}

		ksort($out);
		return $out;
		//return array_column($stats, 'clicked','date');

	}

}
