<?php

namespace app\models;
use \flundr\mvc\Model;
use \flundr\cache\RequestCache;
use \app\models\Orders;
use \app\models\Articles;

class TeaserPositions
{

	private $data = [];

	function __construct() {
		//$this->Orders = new Orders();
		$this->Articles = new Articles();
		$this->import_csv();
	}

	public function get($date = 'today', $hour = 0) {

		//$date = date('j.n.y', strtotime($date));
		$date = date('d.m.Y', strtotime($date));
		$hour = intval($hour);

		$this->apply_filter('day_partition', $date, 1);
		$this->apply_filter('hour', $hour);

		if (empty($this->data)) {return [];}

		$max = max(array_column($this->data,'article_position')); // Max Positions

		$positions = array_fill(1, $max, [[]]);

		foreach ($this->positions() as $pos) {
			$posData = $this->position_with_article($pos);
			$positions[$pos] = $posData;
		}

		return $positions;

	}

	public function position_with_article($pos) {
		$positions = array_values($this->filter('article_position', $pos));

		// Sort by a Value
		$exposures = array_column($positions, 'total_exposures');
		array_multisort($exposures, SORT_DESC, $positions);

		return array_map([$this, 'combine_teaser_with_article'], $positions);
	}

	public function combine_teaser_with_article($set) {

		$article = $this->Articles->get($set['article_id']);
		if (empty($article)) {
			$article['title'] = 'Artikel nicht in Compare';
			$article['id'] = $set['article_id'];
		}

		$out = $article;

		$out['position'] = $set['article_position'];
		$out['exposures'] = $set['total_exposures'];
		$out['clicks'] = $set['total_conversions'];

		$out['CTR'] = round(str_replace(',','.', $set['CTR']),2);

		return $out;

	}


	public function positions() {
		$positions = array_unique(array_column($this->data, 'article_position'));
		sort($positions);
		return $positions;
	}

	public function import_csv() {

		$cache = new RequestCache('driveklicks' . PORTAL, 60*60);
		
		$cachedData = $cache->get();
		if ($cachedData) {
			$this->data = $cachedData;
			return;
		}

		$path = ROOT . 'import/drive_clickrates.csv';

		if (!file_exists($path)) {
			throw new \Exception($path . ' Not found', 500);
		}

		$data = file($path, FILE_IGNORE_NEW_LINES);
		$header = str_getcsv(array_shift($data),';');
		//$csv = array_map('str_getcsv', $data);
		$csv = array_map(function($set){
			return str_getcsv($set,";");
		},$data);

		foreach ($csv as $key => $row) {
		    $csv[$key] = array_combine($header, $row);
		}

		$cache->save($csv);
		$this->data = $csv;

	}

	public function apply_filter($index, $value, $onlyDays = false) {

		if ($onlyDays) {
			$set = array_filter($this->data, function($data) use ($index, $value){
				$date = explode(' ', $data[$index])[0];
				return $date == $value;
			});
		}

		else {
			$set = array_filter($this->data, function($data) use ($index, $value){
				return $data[$index] == $value;
			});
		}

		$this->data = array_values($set);

	}

	public function filter($index, $value) {
		return array_filter($this->data, function($data) use ($index, $value){
			return $data[$index] == $value;
		});
	}

}
