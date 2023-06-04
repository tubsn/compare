<?php

namespace app\models;
use \flundr\mvc\Model;
use \flundr\database\SQLdb;
use \flundr\cache\RequestCache;
use \flundr\utility\Session;
use app\models\Cleverpush;
use \app\importer\CleverpushAPI;

class Pushes extends Model
{

	public $from = '0000-00-00';
	public $to = '3000-01-01';

	function __construct() {
		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'pushes';
		$this->db->orderby = 'sent_at';
		$this->db->limit = 100000;

		$this->from = date('Y-m-d', strtotime(DEFAULT_FROM));
		$this->to = date('Y-m-d', strtotime(DEFAULT_TO));

		if (Session::get('from')) {$this->from = Session::get('from');}
		if (Session::get('to')) {$this->to = Session::get('to');}

	}


	public function list_with_userneed() {

		$table = $this->db->table;
		$SQLstatement = $this->db->connection->prepare(
			"SELECT pushes.*, articles.userneed as userneed
			 FROM $table
			 LEFT JOIN articles on pushes.article_id = articles.id
			 #LIMIT 100
			"
		);
		$SQLstatement->execute();
		$output = $SQLstatement->fetchall();
		return $output;

	}

	public function clickrate_development($topic = 'politik') {

		$table = $this->db->table;
		$SQLstatement = $this->db->connection->prepare(
			"SELECT
				DATE_FORMAT(sent_at,'%Y-%m') as month,
				sum(clicks) as clicks,
				sum(delivered) as delivered,
				round((sum(clicks)/sum(delivered))*100,2) as avgclickrate,
				count(id) as amount
			 FROM $table
			 WHERE DATE(sent_at) BETWEEN :startDate AND :endDate
			 #AND title like '%ukraine%' or title like '%krieg%' or title like '%putin%' or title like '%moskau%'
			 #AND title like '%„Wachgehört“%' or title like '%Podcast%'
			 AND topic like :topic
			 #AND channel = 'app'
			 GROUP BY month
			 #HAVING amount > 2
			 ORDER BY month ASC
			"
		);
		$SQLstatement->execute([':startDate' => '2020-01-01' , ':endDate' => '2030-01-01', ':topic' => '%'.$topic.'%']);
		$output = $SQLstatement->fetchall(\PDO::FETCH_UNIQUE);
		return $output;

	}

	public function distinct_topics() {

		$table = $this->db->table;
		$SQLstatement = $this->db->connection->prepare(
			"SELECT distinct topic
			 FROM $table
			 WHERE topic != ''
			 ORDER BY topic ASC
			"
		);
		$SQLstatement->execute();
		$output = $SQLstatement->fetchall(\PDO::FETCH_COLUMN);
		return $output;

	}



	public function clickrate_and_time($column = 'topic') {

		$filter = '';

		if (isset($_GET['apponly'])) {
			$filter = "AND channel = 'app'";
		}

		if (isset($_GET['webonly'])) {
			$filter = "AND channel = 'web'";
		}

		$table = $this->db->table;
		$SQLstatement = $this->db->connection->prepare(
			"SELECT
				DATE_FORMAT(sent_at,'%H') as hour,
				if($column is null, 'leer', $column) as $column,
				round((sum(clicks)/sum(delivered))*100,2) as clickrate
			 FROM $table
			 WHERE DATE(sent_at) BETWEEN :startDate AND :endDate
			 $filter
			 GROUP BY hour, $column
			 ORDER BY hour ASC
			"
		);
		$SQLstatement->execute([':startDate' => $this->from , ':endDate' => $this->to]);
		$output = $SQLstatement->fetchall(\PDO::FETCH_GROUP);

		$out = [];
		foreach ($output as $key => $set) {

			foreach ($set as $data) {
				$out[$key][$data[$column]] = $data['clickrate'];
			}

		}

		return $out;

	}


	public function pushes_per_day() {

		$filter = '';

		if (isset($_GET['apponly'])) {
			$filter = "AND channel = 'app'";
		}

		if (isset($_GET['webonly'])) {
			$filter = "AND channel = 'web'";
		}		

		$table = $this->db->table;
		$SQLstatement = $this->db->connection->prepare(
			"SELECT
				DATE_FORMAT(sent_at,'%Y-%m') as month,
				round((sum(clicks)/sum(delivered))*100,2) as clickrate,
				count(id) as amount,
				round(count(id) / 31,2) as avg_per_day
			 FROM $table
			 WHERE DATE(sent_at) BETWEEN :startDate AND :endDate
			 $filter
			 GROUP BY month
			 ORDER BY month ASC
			"
		);
		$SQLstatement->execute([':startDate' => '2020-01-01' , ':endDate' => '2030-01-01']);
		$output = $SQLstatement->fetchall(\PDO::FETCH_UNIQUE);

		return $output;

	}

	public function stats() {

		$table = $this->db->table;
		$SQLstatement = $this->db->connection->prepare(
			"SELECT
				count(id) as amount,
				round((sum(clicks)/sum(delivered))*100,2) as clickrate
			 FROM $table
			 WHERE DATE(sent_at) BETWEEN :startDate AND :endDate
			 #AND channel = 'web'
			"
		);
		$SQLstatement->execute([':startDate' => $this->from , ':endDate' => $this->to]);
		$output = $SQLstatement->fetch();

		return $output;

	}


	public function clickrate_grouped_by($column = 'topic') {

		$filter = '';

		if (isset($_GET['apponly'])) {
			$filter = "AND channel = 'app'";
		}

		if (isset($_GET['webonly'])) {
			$filter = "AND channel = 'web'";
		}

		$table = $this->db->table;
		$SQLstatement = $this->db->connection->prepare(
			"SELECT if($column is null, 'leer', $column) as $column, 
				count(id) as notifications,
				#round(avg(clickrate),2) as clickrate,
				round((sum(clicks)/sum(delivered))*100,2) as clickrate,				
				round(avg(clicks)) as avg_clicks,
				sum(clicks) as clicks,
				round(avg(delivered)) as avg_delivered,
				round(avg(recievers)) as avg_recievers,
				round(avg(opt_outs)) as avg_opt_outs,
				sum(opt_outs) as opt_outs
			 FROM $table
			 WHERE DATE(sent_at) BETWEEN :startDate AND :endDate
			 $filter
			 AND $column IS NOT NULL
			 GROUP BY $column
			 HAVING notifications >= 10
			 #ORDER BY notifications DESC
			"
		);
		$SQLstatement->execute([':startDate' => $this->from , ':endDate' => $this->to]);
		$output = $SQLstatement->fetchall(\PDO::FETCH_UNIQUE);
		return $output;

	}



	public function import($from = 'today -1month', $to = 'yesterday', $channel = 'web') {

		if (PORTAL != 'LR') {$channel = 'web';} // App is not available for moz or swp

		$validChannels = ['web', 'app'];
		if (!in_array($channel, $validChannels)) {
			throw new \Exception("Channel Invalid", 404);
		}

		$from= date('Y-m-d', strtotime($from));
		$to = date('Y-m-d', strtotime($to));

		$cp = new Cleverpush();
		if ($channel == 'app') {$cp->switch_to_app();}

		$cp->from = $from;
		$cp->to = $to;

		$list = $cp->list(50000);

		$list = array_map(function ($push) use ($channel) {

			if ($push['status'] != 'sent') {return null;}

			$out = [];
			$out['id'] = $push['id'];
			$out['channel'] = $channel;
			$out['portal'] = strtolower(PORTAL);
			$out['title'] = $push['title'];
			$out['text'] = $push['text'];
			$out['sent_at'] = $push['sentAt'];
			$out['recievers'] = $push['subscriptionCount'];
			$out['delivered'] = $push['delivered'];
			$out['errors'] = $push['errors'];
			$out['opt_outs'] = $push['optOuts'];
			$out['clicks'] = $push['clicks'];
			$out['clickrate'] = $push['clickrate'];
			$out['article_id'] = $push['article_id'];
			$out['plus'] = $push['article']['plus'] ?? null;
			$out['discover'] = $push['article']['discover'] ?? null;
			$out['ressort'] = $push['article']['ressort'] ?? null;
			$out['topic'] = $push['article']['type'] ?? null;
			$out['tag'] = $push['article']['tag'] ?? null;
			$out['audience'] = $push['article']['audience'] ?? null;
			$out['pageviews'] = $push['article']['pageviews'] ?? null;
			$out['conversions'] = $push['article']['conversions'] ?? null;
			$out['buyintent'] = $push['article']['buyintent'] ?? null;
			$out['cancelled'] = $push['article']['cancelled'] ?? null;
			$out['mediatime'] = $push['article']['avgmediatime'] ?? null;

			return $out;
		}, $list);

		//dd($list);

		foreach ($list as $entry) {
			if (empty($entry)) {continue;}
			$this->create_or_update($entry);
		}

		return $list;

	}


}
