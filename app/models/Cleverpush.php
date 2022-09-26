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
			$out['hourly_stats'] = $this->clicks_by_hour($data['stats']);
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

	private function clicks_by_hour($stats) {

		$out = [];
		foreach ($stats as $data) {
			$date = date("Y-m-d H:i", strtotime($data['date']));
			$out[$date]['clicks'] = $data['clicked'] ?? 0;
			$out[$date]['delivered'] = $data['delivered'] ?? 0;
		}

		return $out;
		//return array_column($stats, 'clicked','date');

	}

}
