<?php

namespace app\importer;

class LinkpulseAdapter
{

	function __construct() {

	}

	public function convert($rawJson) {

		$data = json_decode($rawJson, true);

		if (!isset($data['data'])) {
			return null;
		}
		
		$analyticData = $data['data']; // Api returns Nested data set
		$analyticData = array_map([$this, 'map_analytics'], $analyticData);

		if (isset($data['included'])) {
			$includedInfo = array_map([$this, 'map_included_info'], $data['included']);
			$analyticData = array_map('array_merge', $includedInfo, $analyticData);
		}

		return $analyticData;

	}

	public function map_included_info($data) {

		$data = $data['attributes']; // Stats are nested in Attributes

		$output['title'] =  $data['title'] ?? null;
		$output['description'] =  $data['description'] ?? null;
		$output['image'] =  $data['image'] ?? null;
		$output['url'] =  $data['url'] ?? null;
		$output['pubdate'] =  $data['published'] ?? null;
		$output['ressort'] =  $data['section'] ?? null;

		return $output;

	}

	public function map_analytics($data) {

		if (!is_array($data)) {return null;}

		$data = $data['attributes']; // Stats are nested in Attributes

		$output['pageviews'] = $data['pageviews'] ?? null;
		$output['sessions'] = 0;
		$output['conversions'] = $data['converted_usercount'] ?? null;
		$output['subscribers'] = $data['subscribers'] ?? null;

		$output['ressort'] = $data['section'] ?? null;
		$output['url'] = $data['url'] ?? null;
		$output['mediatime'] = $data['inscreencnt'] ?? null;
		$output['avgmediatime'] = $data['viewtimeavgmed'] ?? null;

		$output['id'] = null;
		if ($output['url']) {
			$output['id'] = $this->extract_id($output['url']);
		}

		return $output;

	}

	private function extract_id($url) {
		// Regex search for the ID = -8Digits.html
		$searchPattern = "/-(\d{8}).html/";
		preg_match($searchPattern, $url, $matches);
		return $matches[1]; // First Match should be the ID
	}

}
