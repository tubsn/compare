<?php

namespace app\importer;

class DiscoverImport
{

	//public $importFilePath = PUBLICFOLDER . 'imports/' . PORTAL . '/Seiten.csv';

	function __construct() {

	}


	public function import($filepath) {

		$discoverData = $this->import_data_from_file($filepath);
		$discoverData = array_map([$this,'map_import'], $discoverData);
		return $discoverData;

	}

	private function map_import($data) {

		$out['article_id'] = $this->extract_id($data[0]);
		$out['discover_clicks'] = $data[1];
		$out['discover_impressions'] = $data[2];
		$out['discover_ctr'] = $data[3];
		$out['discover'] = 1;

		return $out;

	}


	private function import_data_from_file($path) {

		$fileContent = file($path);
		array_shift($fileContent); // Remove Header Line
		return array_map('str_getcsv', $fileContent);

	}

	private function extract_id($url) {
		// Regex search for the ID = -8Digits.html
		$searchPattern = "/-(\d{8}).html/";
		preg_match($searchPattern, $url, $matches);
		return $matches[1]; // First Match should be the ID
	}


}
