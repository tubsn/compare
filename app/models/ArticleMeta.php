<?php

namespace app\models;
use \app\models\Charts;
use \app\importer\BigQuery;
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
		$this->db->limit = 100000;

		$this->from = date('Y-m-d', strtotime(DEFAULT_FROM));
		$this->to = date('Y-m-d', strtotime(DEFAULT_TO));
	}


	public function average_emo() {

		$table = $this->db->table;
		$SQLstatement = $this->db->connection->prepare(
			"SELECT avg(JSON_EXTRACT(article_emotion, '$.emo_aerger')) as Ärger
			FROM `$table`"
		);

		$SQLstatement->execute();
		$data = $SQLstatement->fetchall();

		dd($data);

	}

	public function list_emotions($IDs) {

		$IDs = implode(',', array_map('intval', $IDs)); // Intval for all IDs
		$table = $this->db->table;

		$SQLstatement = $this->db->connection->prepare(
			"SELECT article_id as id, article_emotion FROM `$table` WHERE `article_id` IN ($IDs) ORDER BY FIELD(`article_id`, $IDs)"
		);

		$SQLstatement->execute();
		$data = $SQLstatement->fetchall(\PDO::FETCH_KEY_PAIR);

		$out = [];
		foreach ($data as $id => $set) {
			$out[$id] = json_decode($set,1);
		}

		return($out);

	}

	public function topics_for($IDs) {

		if (empty($IDs)) {throw new \Exception("No Article IDs given", 404);}
		$IDs = implode(',', array_map('intval', $IDs)); // Intval for all IDs
		$table = $this->db->table;
		$SQLstatement = $this->db->connection->prepare(
			"SELECT article_id as id, type FROM `$table` WHERE `article_id` IN ($IDs)"
		);

		$SQLstatement->execute();
		$topics = $SQLstatement->fetchall(\PDO::FETCH_KEY_PAIR);

		//return array_map([$this, 'map_topics'], $topics);
		return $topics;

	}

	public function userneeds_for($IDs) {

		if (empty($IDs)) {throw new \Exception("No Article IDs given", 404);}
		$IDs = implode(',', array_map('intval', $IDs)); // Intval for all IDs
		$table = $this->db->table;
		$SQLstatement = $this->db->connection->prepare(
			"SELECT article_id as id, userneed FROM `$table` WHERE `article_id` IN ($IDs)"
		);

		$SQLstatement->execute();
		$topics = $SQLstatement->fetchall(\PDO::FETCH_KEY_PAIR);

		return $topics;

	}


	private function map_topics($topic) {

		// The Topic Mapping is not Used at the moment!!!

		if (PORTAL == 'LR') {
			switch ($topic) {
				case 'Vermischtes: Soziales': 			$topic = 'Soziales'; break;
				case 'Verkehr/Infrastruktur': 			$topic = 'Politik/Wirtschaft'; break;
				case 'Vermischtes: Freizeit/Hobbys': 	$topic = 'Tourismus'; break;
				case 'Sport: Nicht-Fußball': 			$topic = 'Sport'; break;
				case 'Bildung/Erziehung': 				$topic = 'Bildung'; break;
				case 'Wirtschaft: Unternehmen': 		$topic = 'Politik/Wirtschaft'; break;
				case 'Kultur': 							$topic = 'Kultur'; break;
				case 'Vermischtes: Sonstiges': 			$topic = 'Sonstiges'; break;
				case 'Vermischtes: Gesundheit': 		$topic = 'Gesundheit'; break;
				case 'Sport: Fußball': 					$topic = 'Sport'; break;
				case 'Wirtschaft: Verbraucher': 		$topic = 'Politik/Wirtschaft'; break;
				case 'Politik': 						$topic = 'Politik/Wirtschaft'; break;
				case 'Justiz/Kriminalität': 			$topic = 'Crime/Blaulicht'; break;
				case 'Vermischtes: Wissenschaft': 		$topic = 'Bildung'; break;
				case 'Katastrophe/Unglück': 			$topic = 'Crime/Blaulicht'; break;
				case 'Vermischtes: Leute': 				$topic = 'Soziales'; break;
				default: return null; break;
			}
		}

		if (PORTAL == 'MOZ') {
			switch ($topic) {
				case 'Vermischtes: Soziales': 			$topic = 'Soziales'; break;
				case 'Verkehr/Infrastruktur': 			$topic = 'Pendler'; break;
				case 'Vermischtes: Freizeit/Hobbys': 	$topic = 'Freizeit'; break;
				case 'Sport: Nicht-Fußball': 			$topic = 'Sportfans'; break;
				case 'Bildung/Erziehung': 				$topic = 'Bildung'; break;
				case 'Wirtschaft: Unternehmen': 		$topic = 'Wirtschaft'; break;
				case 'Kultur': 							$topic = 'Kulturfreunde'; break;
				case 'Vermischtes: Sonstiges': 			$topic = 'Sonstiges'; break;
				case 'Vermischtes: Gesundheit': 		$topic = 'Gesundheit'; break;
				case 'Sport: Fußball': 					$topic = 'Fußballfans'; break;
				case 'Wirtschaft: Verbraucher': 		$topic = 'Verbraucher'; break;
				case 'Politik': 						$topic = 'Politik'; break;
				case 'Justiz/Kriminalität': 			$topic = 'Crime/Blaulicht'; break;
				case 'Vermischtes: Wissenschaft': 		$topic = 'Innovationsfreunde'; break;
				case 'Katastrophe/Unglück': 			$topic = 'Crime/Blaulicht'; break;
				case 'Vermischtes: Leute': 				$topic = 'Soziales'; break;
				default: return null; break;
			}
		}


		if (PORTAL == 'SWP') {
			switch ($topic) {
				case 'Vermischtes: Soziales': 			$topic = 'Soziales'; break;
				case 'Verkehr/Infrastruktur': 			$topic = 'Verkehr, Transport & Pendler'; break;
				case 'Vermischtes: Freizeit/Hobbys': 	$topic = null; break;
				case 'Sport: Nicht-Fußball': 			$topic = null; break;
				case 'Bildung/Erziehung': 				$topic = null; break;
				case 'Wirtschaft: Unternehmen': 		$topic = 'Lokale Wirtschaft & Unternehmer'; break;
				case 'Kultur': 							$topic = 'Stadtgespräch'; break;
				case 'Vermischtes: Sonstiges': 			$topic = 'Sonstiges'; break;
				case 'Vermischtes: Gesundheit': 		$topic = null; break;
				case 'Sport: Fußball': 					$topic = null; break;
				case 'Wirtschaft: Verbraucher': 		$topic = 'Lokale Wirtschaft & Unternehmer'; break;
				case 'Politik': 						$topic = 'Lokal- Und Landespolitik'; break;
				case 'Justiz/Kriminalität': 			$topic = 'Crime, Justiz & Katastrophe'; break;
				case 'Vermischtes: Wissenschaft': 		$topic = null; break;
				case 'Katastrophe/Unglück': 			$topic = 'Crime, Justiz & Katastrophe'; break;
				case 'Vermischtes: Leute': 				$topic = null; break;
				default: return null; break;
			}
		}

		return $topic;

	}

	public function emotions($id = null) {

		$emotions = $this->get($id,'article_emotion')['article_emotion'] ?? false;
		if (!$emotions) {return null;}

		$emotions = json_decode($emotions,true);

		$data = $this->chart_data($emotions);

		$data['color'] = '#5f7894';
		$data['showValues'] = true;
		$data['name'] = 'Emotions';
		$data['id'] = 'emotions';

		$chart = new Chartengine();
		return  $chart->render('charts/emo_chart', $data);

	}


	public function chart_data($data) {

		$dimensions = null;
		$metrics = null;

		ksort($data);

		foreach ($data as $key => $value) {

			$key = ucfirst(substr($key,4));

			$dimensions .= "'" . $key . "',";
			$metrics .= "'" . round($value,3) . "',";
		}

		$metrics = rtrim($metrics, ',');
		$dimensions = rtrim($dimensions, ',');

		return ['metric' => $metrics, 'dimension' => $dimensions];

	}


	public function import_drive_data() {
		$drive = $this->drive_data_from_bigquery();

		foreach ($drive as $article) {
			$this->create_or_update($article);
		}

	}

	private function drive_data_from_bigquery() {
		$bigQueryApi = new BigQuery;
		$publisher = PORTAL;
		$query =
			"SELECT
			article_publisher_id as article_id,
			article_length,
			header_length,
			number_of_words,
			article_locality,
			article_genre,
			article_user_need as userneed,
			article_topic as type
			FROM `artikel-reports-tool.DPA_DriveV2.dpa_drive_articles`
			WHERE publisher = '$publisher'
			and published_at_local >= DATE_SUB(CURRENT_DATE, INTERVAL 4 DAY)
			LIMIT 5000";
		$data = $bigQueryApi->sql($query);
		return  $data;
	}


}
