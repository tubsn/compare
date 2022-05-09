<?php

namespace app\models\helpers;

use \flundr\cache\RequestCache;

class CSVImports
{

	function __construct() {}

	public function import_drive_experiment_data_from_csv() {

		$path = ROOT . 'import/user-groups.csv';

		if (!file_exists($path)) {
			throw new \Exception($path . ' Not found', 500);
		}

		$data = file($path, FILE_IGNORE_NEW_LINES);
		$header = str_getcsv(array_shift($data),',');

		$csv = array_map(function($set){
			return str_getcsv($set,",");
		},$data);

		foreach ($csv as $key => $row) {
			$csv[$key] = array_combine($header, $row);
		}

		$index = 'publisher';
		$value = PORTAL_NAME;

		$csv = array_filter($csv, function($data) use ($index, $value){
			return $data[$index] == $value;
		});

		$startSegment = array_column($csv, 'user_engagement_segment_subscription_start', 'order_id');
		$cancelSegment = array_column($csv, 'user_engagement_segment_subscription_cancellation', 'order_id');
		$testingGroup = array_column($csv, 'mode_experiment_group', 'order_id');

		foreach ($testingGroup as $id => $value) {
			if (empty($value)) {continue;}
			$this->Orders->update(['customer_testgroup' => $value], $id);
		}

		foreach ($cancelSegment as $id => $value) {
			if (empty($value) || $value == 'unknown') {
				$this->Orders->update(['customer_cancel_segment' => null], $id);
				continue;
			}
			$this->Orders->update(['customer_cancel_segment' => $value], $id);
		}

		foreach ($startSegment as $id => $value) {
			if (empty($value) || $value == 'unknown') {
				$this->Orders->update(['customer_cancel_segment' => null], $id);
				continue;
			}
			$this->Orders->update(['customer_order_segment' => $value], $id);
		}

	}


	public function csv_import_segments_by_date() {

		$pathNormal = ROOT . 'import/temp/lr-alle.csv';
		$pathPremium = ROOT . 'import/temp/lr-premium.csv';
		$normalUsers = $this->read_csv_with_header($pathNormal);
		$premiumUsers = $this->read_csv_with_header($pathPremium);

		$this->save_segments_to_db($normalUsers);
		$this->save_segments_to_db($premiumUsers, '_reg');

	}

	private function read_csv_with_header($filepath) {

		if (!file_exists($filepath)) {
			throw new \Exception($filepath . ' Not found', 500);
		}

		$data = file($filepath, FILE_IGNORE_NEW_LINES);
		$header = str_getcsv(array_shift($data),',');
		$csv = array_map(function($set){
			return str_getcsv($set,",");
		},$data);

		foreach ($csv as $key => $row) {
			$csv[$key] = array_combine($header, $row);
		}

		return $csv;

	}

	private function save_segments_to_db($csv, $suffix = null) {

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
		$amountFieldName = 'Anzahl aktive User';
		$dateFieldName = '__timestamp';

		$data = array_group_by($dateFieldName, $csv);

		$segmentsByDate = [];
		foreach ($data as $date => $segmentSets) {

			foreach ($segmentSets as $sets) {
				$mappedSegment = $segmentDatabaseMapping[$sets[$segmentFieldName]];
				if ($suffix) {$mappedSegment = $mappedSegment . $suffix;}
				$segmentsByDate[$date][$mappedSegment] = $sets[$amountFieldName];
			}

		}

		ksort($segmentsByDate);

		//dd($segmentsByDate);

		foreach ($segmentsByDate as $day => $segmentData) {
			$this->DailyKPIs->update($segmentData,$day);
		}

	}



}
