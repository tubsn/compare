<?php

namespace app\models\helpers;

use \flundr\utility\Session;
use \flundr\cache\RequestCache;

class UTMCampaign
{

	public $dateField;
	public $mainMetric;
	private $db;

	function __construct($mainMetric = null, $datefield = null) {
			$this->mainMetric = $mainMetric ?? 'Totalevents';
			$this->dateField = $dateField ?? 'Date';
	}

	public function dimension($dimension) {
		$dimension = strtolower($dimension);
		return $this->db[$dimension];
	}

	public function by_date() {
		return $this->db['Date'];
	}

	public function total() {
		return $this->db['Totals'];
	}

	public function totals_for($dimension) {
		$dimension = strtolower($dimension);
		$out = [];
		foreach ($this->db[$dimension] as $name => $data) {
			$out[$name] = array_sum($data);
		}

		arsort($out);
		return $out;
	}

	public function import($gaData) {

		$this->db = $this->transform_event_data($gaData,$this->dateField, $this->mainMetric);

	}



	private function transform_event_data($gaData, $dateField, $valueField) {

		$out = $this->group_metrics_with_date($gaData, $dateField, $valueField);
		$out['Date'] = $this->group_by_date($gaData, null, null, $dateField, $valueField);
		$out['Totals'] = array_sum($out['Date']);

		return $out;

	}

	private function group_metrics_with_date(array $googleApiData, $dateField, $valueField) {

		$keys = array_keys($googleApiData[0]);

		$metrics = [];
		foreach ($keys as $key) {
			if ($key == $dateField || $key == $valueField) {continue;}
			$metrics[$key] = array_unique(array_column($googleApiData, $key));
		}

		$out = [];
		foreach ($metrics as $metric => $values) {

			//dd($googleApiData);

			foreach ($values as $value) {

				$out[strtolower($metric)][$value] = $this->group_by_date($googleApiData, $metric, $value, $dateField, $valueField);

			}

		}

		return $out;

	}

	private function group_by_date(array $data, $filterField = null, $filterValue = null, $dateField, $valueField) {

		$out = [];

		foreach ($data as $set) {

			$date = date('Y-m-d', strtotime($set[$dateField]));

			if (empty($filterField)) {
				if (isset($out[$date])) {
					$out[$date] = $out[$date] + $set[$valueField];
				}
				else {$out[$date] = $set[$valueField];}
			}

			else {
				if ($set[$filterField] == $filterValue) {
					if (isset($out[$date])) {
						$out[$date] = $out[$date] + $set[$valueField];
					}
					else {$out[$date] = $set[$valueField];}
				}
			}

		}

		return $out;

	}


}
