<?php

namespace app\models;
use \app\models\Charts;
use \flundr\database\SQLdb;
use \flundr\mvc\Model;
use \flundr\utility\Session;

class ArticleMeta extends Model
{

	public $from = '0000-00-00';
	public $to = '3000-01-01';

	private $articleID;
	protected $db;

	function __construct() {
		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'article_meta';
		$this->db->primaryIndex = 'article_id';
		$this->db->orderby = 'article_id';
		$this->db->order = 'DESC';

		$this->from = date('Y-m-d', strtotime('yesterday -6days'));
		$this->to = date('Y-m-d', strtotime('yesterday'));
	}


	public function emotions($id = null) {

		$emotions = $this->get($id,'article_emotion')['article_emotion'];
		$emotions = json_decode($emotions,true);

		$data = $this->chart_data($emotions);

		$data['color'] = '#d5707d';
		$data['showValues'] = true;
		$data['name'] = 'Emotions';
		$data['id'] = 'emotions';

		$chart = new Charts();
		return  $chart->render('charts/emo_chart', $data);

	}


	public function chart_data($data) {

		$dimensions = null;
		$metrics = null;

		ksort($data);
		//dd($data);


		foreach ($data as $key => $value) {

			$key = ucfirst(substr($key,4));

			$dimensions .= "'" . $key . "',";
			$metrics .= "'" . round($value,3) . "',";
		}

		$metrics = rtrim($metrics, ',');
		$dimensions = rtrim($dimensions, ',');

		//$dimension = "'" . implode("','", array_keys($data)) . "'";
		//$metric = implode(',', array_values($data));

		return ['metric' => $metrics, 'dimension' => $dimensions];

	}


	public function import() {
		$drive = $this->data_store();
		$drive = array_map([$this, 'map_drive_data'], $drive);
	}


	private function map_drive_data($data) {

		$data = ['article_id' => $data['article_publisher_id']] + $data;
		unset($data['article_publisher_id']);

		if (!empty($data['categories']['topic'])) {
			$data['type'] =  "'" . $data['categories']['topic'] . "'";
		}
		else {$data['type'] = 'null' ;}

		$data['article_pad'] = "'" . json_encode($data['article_pad']) . "'";
		$data['article_preview_pad'] = "'" . json_encode($data['article_preview_pad']) . "'";
		$data['article_emotion'] = "'" . json_encode($data['article_emotion']) . "'";
		$data['article_preview_emotion'] = "'" . json_encode($data['article_preview_emotion']) . "'";
		$data['categories'] = "'" . json_encode($data['categories']) . "'";

		$this->save($data);

	}


	public function save($data) {

		$table = $this->db->table;
		$keys = array_keys($data);

		$data = implode(", ",$data);
		$keys = implode(", ",$keys);

		//dd($keys);

		$stmt = $this->db->connection->prepare(
			"INSERT INTO `$table` ($keys) VALUES ($data)
			ON DUPLICATE KEY UPDATE
			`article_id`=VALUES(`article_id`),
			`article_pad`=VALUES(`article_pad`),
			`article_preview_pad`=VALUES(`article_preview_pad`),
			`article_emotion`=VALUES(`article_emotion`),
			`article_preview_emotion`=VALUES(`article_preview_emotion`),
			`categories`=VALUES(`categories`),
			`type`=VALUES(`type`)"
		);

		//dd($stmt);

		$stmt->execute();

	}


	private function data_store() {

		return json_decode($json,1);

	}

}
