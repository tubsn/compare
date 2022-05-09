<?php

namespace app\importer;

use Google\Cloud\BigQuery\BigQueryClient;
use Google\Cloud\Core\ExponentialBackoff;

class BigQuery
{

	public $projectID = 'artikel-reports-tool';
	private $api;

	public function __construct() {
		$this->api = new BigQueryClient([
		    'projectId' => $this->projectID,
            'keyFile' => json_decode(file_get_contents(ANALYTICS_SERVICE_ACCOUNT_FILE), true),
		]);
	}

	public function sql($query) {

		$results = $this->fetch($query);
		return $this->results_to_array($results);

	}

	private function results_to_array($results) {

		$output = [];
		foreach ($results as $row) {

			$rowData = [];
		    foreach ($row as $column => $value) {

				if (is_a($value, 'DateTime')) {
	    			$rowData[$column] = $value->format('Y-m-d');
					continue;
				}

		    	if ($column == 'date') {
		    		$rowData[$column] = $value->formatAsString();
		    		continue;
		    	}

		    	$rowData[$column] = $value;
		    }
		    array_push($output, $rowData);
		}

		return $output;
	}

	private function fetch($query) {
		$jobConfig = $this->api->query($query);
		$job = $this->api->startQuery($jobConfig);
		return $job->queryResults();
	}

}
