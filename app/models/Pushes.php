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

		$this->from = date('Y-m-d', strtotime(DEFAULT_FROM));
		$this->to = date('Y-m-d', strtotime(DEFAULT_TO));

		if (Session::get('from')) {$this->from = Session::get('from');}
		if (Session::get('to')) {$this->to = Session::get('to');}

	}

	public function clickrate_and_time($column = 'topic') {

		$table = $this->db->table;
		$SQLstatement = $this->db->connection->prepare(
			"SELECT
				DATE_FORMAT(sent_at,'%H') as hour,
				if($column is null, 'leer', $column) as $column,
				round(avg(clickrate),2) as clickrate
				##if(clickrate is null, '0', round(avg(clickrate),2)) as clickrate

			 FROM $table
			 WHERE DATE(sent_at) BETWEEN :startDate AND :endDate
			 AND channel = 'web'
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


	public function clickrate_grouped_by($column = 'topic') {

		$table = $this->db->table;
		$SQLstatement = $this->db->connection->prepare(
			"SELECT if($column is null, 'leer', $column) as $column, count(id) as notifications,
				round(avg(clickrate),2) as clickrate,
				round(avg(clicks)) as avg_clicks,
				sum(clicks) as clicks,
				round(avg(delivered)) as avg_delivered,
				round(avg(recievers)) as avg_recievers,
				round(avg(opt_outs)) as avg_opt_outs,
				sum(opt_outs) as opt_outs
			 FROM $table
			 WHERE DATE(sent_at) BETWEEN :startDate AND :endDate
			 AND channel = 'web'
			 GROUP BY $column
			 #ORDER BY clickrate DESC
			"
		);
		$SQLstatement->execute([':startDate' => $this->from , ':endDate' => $this->to]);
		$output = $SQLstatement->fetchall(\PDO::FETCH_UNIQUE);
		return $output;

	}



	public function import() {

		$cp = new Cleverpush();

		$cp->from = '2022-10-01';
		$cp->to = '2022-12-31';

		$list = $cp->list(5000);

		dd($list);

		$list = array_map(function ($push) {

			if ($push['status'] != 'sent') {return;}

			$out = [];
			$out['id'] = $push['id'];
			$out['channel'] = 'web';
			$out['portal'] = 'lr';
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

		foreach ($list as $entry) {
			if (empty($entry)) {continue;}
			$this->create_or_update($entry);
		}

		return $list;

	}


}
