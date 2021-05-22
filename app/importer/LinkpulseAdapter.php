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

		$data = $data['data']; // Api returns Nested data set
		return array_map([$this, 'map_fields'],$data);

	}

	public function map_fields($data) {

		if (!is_array($data)) {return null;}

		$data = $data['attributes']; // Stats are nested in Attributes

		$output['pageviews'] = $data['pageviews'] ?? null;
		$output['sessions'] = 0;
		$output['conversions'] = $data['converted_usercount'] ?? null;
		$output['subscribers'] = $data['subscribers'] ?? null;

		return $output;

	}

}
