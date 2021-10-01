<?php

namespace app\models;
use	\app\importer\LinkpulseImport;

class Linkpulse
{

	function __construct() {
		$this->api = new LinkpulseImport();
	}

	public function stats_today($id) {
		return $this->api->article_today($id);
	}

	public function subscribers($id, $pubDate) {
		return $this->api->subscribers($id, $pubDate);
	}

	public function today($client = null) {

		if ($client) {
			$this->api->client($client);
		}

		$liveData = $this->api->live();
		$liveData = $this->convert_to_chart_data($liveData);

		return $liveData;
	}

	public function articles_today() {
		return $this->api->articles_today();
	}

	private function convert_to_chart_data($data) {

		$pageviews = null;
		$values = null;
		$time = null;
		$counter = 0;

		foreach ($data as $moment) {

			$pageviews += $moment['attributes']['pageviews'];

			$counter++;
			if ($counter % 3 != 0) {continue;}

			$values .= $moment['attributes']['pageviews'] . ',';
			$timestring = date('H:i', strtotime($moment['id']));
			$time .= "'" . $timestring."'" . ',';

		}

		return [
			'values' => $values,
			'timestamps' => $time,
			'pageviews' => $pageviews,
		];

	}

}
