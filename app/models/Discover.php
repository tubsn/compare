<?php

namespace app\models;
use \flundr\mvc\Model;
use \flundr\database\SQLdb;
use \flundr\utility\Session;
use \app\importer\DiscoverImport;

class Discover extends Model
{

	public $from = '0000-00-00';
	public $to = '3000-01-01';

	function __construct() {
		$this->api = new DiscoverImport();
		$this->articleDB = new Articles();
		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'article_meta';
		$this->db->primaryIndex = 'article_id';

		$this->from = date('Y-m-d', strtotime(DEFAULT_FROM));
		$this->to = date('Y-m-d', strtotime(DEFAULT_TO));

		if (Session::get('from')) {$this->from = Session::get('from');}
		if (Session::get('to')) {$this->to = Session::get('to');}

	}

	public function list() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		//if (!is_null($filter)) {$filter = 'AND ' . strip_tags($filter);}

		$SQLstatement = $this->db->connection->prepare(

			"SELECT *

			 FROM article_meta
			 RIGHT JOIN articles ON `id` = article_meta.article_id

			 WHERE DATE(articles.pubdate) BETWEEN :startDate AND :endDate
			 AND article_meta.discover = 1
			 ORDER BY articles.pubdate DESC"

		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$orders = $SQLstatement->fetchall();

		return $orders;


	}

	public function list_only() {
		return $this->exact_search('1', 'discover');
	}

	public function import($filePath) {

		$discoverData = $this->api->import($filePath);

		if (count($discoverData) > 0 && isset($discoverData[0]['article_id']) && !empty($discoverData[0]['article_id'])) {

			foreach ($discoverData as $article) {
				if (empty($article['article_id'])) {continue;}
				$this->create_or_update($article);
				$this->articleDB->update(['discover' => 1], $article['article_id']);
			}

		}

		else {
			dump($discoverData);
			throw new \Exception('Import Failed - Bitte die Seiten.csv Datei hochladen! WICHTIG CSV!!!', 400);
		}

		return $discoverData;

	}

}
