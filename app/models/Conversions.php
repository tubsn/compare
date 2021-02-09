<?php

namespace app\models;
use \flundr\database\SQLdb;
use \flundr\mvc\Model;
use \flundr\utility\Session;
use flundr\cache\RequestCache;

class Conversions extends Model
{

	public $articleID = null;
	public $pubDate = '30daysAgo';

	private $transactions = [];
	private $transactionIDs = [];
	private $analyticsData = [];

	function __construct() {
		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'conversions';
		$this->db->primaryIndex = 'transaction_id';
		$this->analytics = new Analytics();
		$this->plenigo = new Plenigo();
	}


	public function collect() {

		$this->transactions = $this->load_from_db();

		if (count($this->transactions) <= 0) {
			$this->refresh();
		}

		return $this->transactions;

	}

	public function refresh() {

		$this->collect_analytics_data();
		$this->merge_archived_transactions();
		$this->enrich_transactions();

		$this->save_to_db();
		$this->update_article();

	}


	public function update_article() {
		$article = new Articles();
		$data['cancelled'] = count($this->cancelled_orders());
		$data['retention_days'] = $this->average_retention_days();
		$article->update($data, $this->articleID);
	}

	public function group_by($index) {
		$data = array_column($this->transactions,$index);
		$data = array_count_values($data);
		arsort($data);
		return $data;
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


	private function load_from_db() {

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			 FROM `conversions`
			 WHERE `article_id` = :id"
		);

		$SQLstatement->execute([':id' => $this->articleID]);
		return $SQLstatement->fetchall();


	}

	private function save_to_db() {

		foreach ($this->transactions as $transaction) {

			$id = $transaction['transaction_id'];
			$item = $this->get($id,['article_id']);

			if (empty($item)) { $this->create($transaction);}
			else {$this->update($transaction, $id);}

		}
	}


	private function collect_analytics_data() {

		$analyticsData = $this->analytics->conversions_by_article_id($this->articleID, $this->pubDate);
		$analyticsData = array_column($analyticsData, null, 'Transactionid'); // Sets TransactionID as Key
		$this->analyticsData = $analyticsData;

	}

	private function merge_archived_transactions() {

		$analyticsTransactionIDs = array_column($this->analyticsData, 'Transactionid');
		$analyticsTransactionIDs = array_fill_keys($analyticsTransactionIDs, []);
		$this->transactionIDs = array_merge($this->archived_transaction_ids($this->articleID), $analyticsTransactionIDs);

	}

	private function archived_transaction_ids($articleID) {

		$SQLstatement = $this->db->connection->prepare(
			"SELECT transaction_id
			 FROM `conversions`
			 WHERE `article_id` = :id"
		);

		$SQLstatement->execute([':id' => $articleID]);
		return $SQLstatement->fetchall(\PDO::FETCH_UNIQUE);

	}


	private function enrich_transactions() {

		foreach ($this->transactionIDs as $transactionID => $voidValue) {

			$data['transaction_id'] = $transactionID;
			$data['article_id'] = $this->articleID;

			if (!empty($this->analyticsData[$transactionID])) {

				// The Empty Check prevents setting GA Data to null if Google API
				// does not return anything for an already saved conversion

				$analytics = $this->analyticsData[$transactionID];

				$data['ga_city']		= $analytics['City'];
				$data['ga_source']		= $analytics['Source'];
				$data['ga_sessions']	= $analytics['Sessioncount'];
			}

			$plenigo = $this->plenigo->order_with_details($transactionID);

			if (!empty($plenigo)) {

				$data['cancelled'] = false;
				$data['retention'] = null;

				$data['customer_id']			= $plenigo['customerID'];
				$data['external_customer_id']	= $plenigo['externalCustomerID'];

				$data['order_product_id']		= $plenigo['productID'];
				$data['order_date']				= $plenigo['orderDate'];
				$data['order_title']			= $plenigo['productTitle'];
				$data['order_price']			= $plenigo['productPrice'];
				$data['order_status']			= $plenigo['orderStatus'];

				if ($plenigo['subscription']) {
					$data['subscription_title']				= $plenigo['subscription']['title'];
					$data['subscription_price']				= $plenigo['subscription']['price'];
					$data['subscription_paymentMethod']		= $plenigo['subscription']['paymentMethod'];
					$data['subscription_start_date']		= $plenigo['subscription']['startDate'];
					$data['subscription_cancellation_date']	= $plenigo['subscription']['cancellationDate'];
					$data['subscription_end_date']			= $plenigo['subscription']['endDate'];
					$data['subscription_count']				= $plenigo['subscription_count'];

					if ($plenigo['subscription']['cancellationDate']) {
						$data['cancelled'] = true;

						$start = new \DateTime(formatDate($plenigo['subscription']['startDate'], 'Y-m-d'));
						$end = new \DateTime(formatDate($plenigo['subscription']['cancellationDate'], 'Y-m-d'));
						$interval = $start->diff($end);
						$data['retention'] = $interval->format('%r%a');

					}
				}

				if ($plenigo['user']) {
					$data['customer_consent'] = $plenigo['user']['agreementState'];
					$data['customer_status'] = $plenigo['user']['userState'];
					$data['customer_gender'] = $plenigo['user']['gender'];
					$data['customer_city'] = $plenigo['user']['city'];
				}
			}

			array_push($this->transactions, $data);

		}

	}





}
