<?php

namespace app\importer;

class AnalyticsReaderAdapter
{

	private $jsonDataPath = APP . '/readers/';

	function __construct($jsonFileName = null) {

		$readerData = $this->load_json_from_file($jsonFileName);

		$this->id = pathinfo($jsonFileName)['filename'];
		$this->articles = $this->extract_articles($readerData);

	}


	private function extract_articles($readerData) {

		// Each readerinfo is divided into several Sessions on multiple Dates

		$articles = [];
		foreach ($readerData['dates'] as $date) {
			$articles = array_merge($articles,$this->extract_read_articles_per($date));
		}

		return $articles;

	}

	private function extract_read_articles_per($date) {

		$articles = [];

		// Each Session is divided into several Activities
		foreach ($date['sessions'] as $session) {

			// The Sessions include Events and other "Activities" that need to be filterred
			$views = $this->filter_by($session['activities'], 'PAGEVIEW');
			$clicks = [];

			foreach ($views as $key => $view) {
				$artikelID = $this->extract_id($view['details'][0]['Seiten-URL'][0]);

				if (!$artikelID) {continue;} // Don't Import Indexpages for now

				$pageTitle = $view['details'][0]['Seitentitel'][0];
				$clicks[$key]['id'] = $artikelID;
				$clicks[$key]['url'] = $view['details'][0]['Seiten-URL'][0];
				$clicks[$key]['title'] = $pageTitle;
				$clicks[$key]['channel'] = $session['channel'];
				$clicks[$key]['device'] = $session['deviceCategory'];
				$clicks[$key]['date'] = strtotime($date['date'] . ' ' . $view['time']);
			}

			$articles = array_merge($articles,$clicks);

		}

		return $articles;

	}


	private function filter_by($data, $filterBy = null) {
		$data = array_filter($data, function($set) use ($filterBy) {
			if ($set['type'] == $filterBy) { return true; }
		});

		$data = array_values($data); // Reindex the Array

		return $data;
	}

	private function load_json_from_file($filename) {
		$jsonData = file_get_contents($this->jsonDataPath . $filename);
		return json_decode($jsonData, true);
	}

	private function extract_id($url) {
		// Regex search for the ID = -8Digits.html
		$searchPattern = "/-(\d{8}).html/";
		preg_match($searchPattern, $url, $matches);

		if (isset($matches[1])) {
			return $matches[1];
		}
	}


}
