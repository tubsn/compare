<?php

namespace app\models;
use \flundr\database\SQLdb;
use \flundr\mvc\Model;
use \flundr\utility\Session;

class Conversions extends Model
{

	public $from = '0000-00-00';
	public $to = '3000-01-01';

	public $articleID = null;
	public $pubDate = '30daysAgo';

	private $transactions = [];
	private $orderIDs = [];
	private $analyticsData = [];

	function __construct() {
		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'conversions';
		$this->db->primaryIndex = 'order_id';
		$this->db->orderby = 'order_date';
		$this->analytics = new Analytics();
		$this->plenigo = new Plenigo();

		$this->from = date('Y-m-d', strtotime(DEFAULT_FROM));
		$this->to = date('Y-m-d', strtotime(DEFAULT_TO));
	}


	public function collect() {

		$this->transactions = $this->load_from_db();
		return $this->transactions;

	}

	public function refresh($withGoogleAnalytics = true) {

		$this->transactions = [];
		$this->orderIDs = [];
		$this->analyticsData = [];

		if ($withGoogleAnalytics) {
			$this->collect_analytics_data();
		}

		$this->merge_archived_transactions();
		$this->enrich_transactions();

		$this->save_to_db();
		$this->update_article();

	}

	public function refresh_without_google_analytics() {$this->refresh(false);}

	public function update_article() {
		$article = new Articles();

		// Set Conversions based on the Transactions after Plenigo V3 Update...
		$plenigoUpdateDate = date("2021-02-23");
		if ($this->pubDate > $plenigoUpdateDate) {
			$data['conversions'] = count($this->transactions);
		}

		$data['cancelled'] = count($this->cancelled_orders());
		$data['retention_days'] = $this->average_retention_days();
		$data['refresh'] = date('Y-m-d H:i:s');
		$article->update($data, $this->articleID);
	}


	public function group_by($index) {
		$data = array_column($this->transactions,$index);
		$data = @array_count_values($data); // @ Surpresses Warnings with Null Values
		arsort($data);
		return $data;
	}

	public function group_cancelled_orders_by($index) {
		$data = array_column($this->cancelled_orders(),$index);
		$data = @array_count_values($data); // @ Surpresses Warnings with Null Values
		arsort($data);
		return $data;
	}

	public function group_by_combined($index) {

		$combined = [];
		$active = $this->group_by($index);

		$cancelled = $this->group_cancelled_orders_by($index);

		foreach ($active as $key => $value) {

			$combined[$key]['active'] = $value;

			if (isset($cancelled[$key])) {
				$combined[$key]['cancelled'] = $cancelled[$key];
			}
			else {
				$combined[$key]['cancelled'] = 0;
			}

		}
		return $combined;
	}


	public function cancelled_orders() {
		return array_filter($this->transactions, function($transaction) {
			if ($transaction['cancelled']) {return $transaction;}
		});
	}

	public function average_retention_days() {

		$cancelledOrders = $this->cancelled_orders();
		if (count($cancelledOrders) == 0) {return null;}

		$retentionDays = array_column($cancelledOrders, 'retention');
		$average = array_sum($retentionDays) / count($cancelledOrders);
		return round($average,2);

	}

	public function list() {

		$this->transactions = $this->combined_with_article_data();
		return $this->transactions;

	}

	private function load_from_db() {

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `conversions`
			 WHERE `article_id` = :id
			 ORDER BY `order_date` DESC"
		);

		$SQLstatement->execute([':id' => $this->articleID]);
		return $SQLstatement->fetchall();


	}

	private function save_to_db() {

		foreach ($this->transactions as $transaction) {

			$id = $transaction['order_id'];
			$item = $this->get($id,['article_id']);

			//dd($transaction);

			if (empty($item)) { $this->create($transaction);}
			else {$this->update($transaction, $id);}

		}
	}


	private function combined_with_article_data() {

		if (Session::get('from')) {$this->from = Session::get('from');}
		if (Session::get('to')) {$this->to = Session::get('to');}

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);

		$SQLstatement = $this->db->connection->prepare(
			"SELECT `conversions`.*,

			 Articles.pubdate as article_pubdate,
			 Articles.title as article_title,
			 Articles.kicker as article_kicker,
			 Articles.ressort as article_ressort,
			 Articles.type as article_type,
			 Articles.tag as article_tag,
			 Articles.audience as article_audience,
			 Articles.author as article_author

			 FROM `conversions`

			 LEFT JOIN `articles` AS Articles
	 		 ON `conversions`.article_id = Articles.id

			 WHERE DATE(`order_date`) BETWEEN :startDate AND :endDate

			 ORDER BY `order_date` DESC"
		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$output = $SQLstatement->fetchall();
		return $output;

	}

	private function collect_analytics_data() {

		$analyticsData = $this->analytics->conversions_by_article_id($this->articleID, $this->pubDate);

		$analyticsData = array_column($analyticsData, null, 'Transactionid'); // OrderIDs are called TransactionIDs in Google
		$this->analyticsData = $analyticsData;

	}

	private function merge_archived_transactions() {

		if (count($this->analyticsData) > 0) {

			$analyticsOrderIDs = array_column($this->analyticsData, 'Transactionid'); // OrderIDs are called TransactionIDs in Google
			$analyticsOrderIDs = array_fill_keys($analyticsOrderIDs, []);

			$previouslyStoredOrderIDs = $this->archived_order_ids($this->articleID);

			// Adds IDs from Analytics, to the stored ones and fills up with the Orders from the Plenigo API
			$this->orderIDs = array_replace($previouslyStoredOrderIDs, $analyticsOrderIDs);
		}

		else {
			$this->orderIDs = $this->archived_order_ids($this->articleID);
		}

	}

	private function archived_order_ids($articleID) {

		$SQLstatement = $this->db->connection->prepare(
			"SELECT order_id
			 FROM `conversions`
			 WHERE `article_id` = :id"
		);

		$SQLstatement->execute([':id' => $articleID]);
		return $SQLstatement->fetchall(\PDO::FETCH_UNIQUE);

	}

	private function is_already_cancelled($transactionID) {
		$cancelled = $this->get($transactionID,['cancelled'])['cancelled'];
		if ($cancelled == 1) {return true;}
		return false;
	}

	private function enrich_transactions() {

		foreach ($this->orderIDs as $transactionID => $voidValue) {

			// New Plenigo IDs should only be numbers
			// We are ignoring old GA transaction IDs
			if (!is_numeric($transactionID)) {continue;}

			$data['order_id'] = $transactionID;
			$data['article_id'] = $this->articleID;
			$data['cancelled'] = 0;
			$data['retention'] = null;

			if (!empty($this->analyticsData[$transactionID])) {

				// The Empty Check prevents setting GA Data to null if Google API
				// does not return anything for an already saved conversion

				$analytics = $this->analyticsData[$transactionID];

				$data['ga_city']		= $analytics['City'];
				$data['ga_source']		= $analytics['Source'];
				$data['ga_sessions']	= $analytics['Sessioncount'];
			}

			// Cancelled orders should not Change right?
			if ($this->is_already_cancelled($transactionID) == 1) {
				$data = $this->get($transactionID);
				array_push($this->transactions, $data);
				continue;
			}

			$plenigo = $this->plenigo->order_with_details($transactionID);

			if (!empty($plenigo)) {
				$data = array_merge($data, $plenigo);
			}

			array_push($this->transactions, $data);

		}

	}

}
