<?php

namespace app\models;
use \flundr\mvc\Model;
use \flundr\database\SQLdb;
use \flundr\utility\Session;
use \flundr\cache\RequestCache;

class Campaigns extends Model
{

	public $from = '0000-00-00';
	public $to = '3000-01-01';

	function __construct() {

		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'campaigns';
		$this->db->primaryIndex = 'order_id';
		$this->db->orderby = 'order_id';

		$this->from = date('Y-m-d', strtotime('yesterday -6days'));
		$this->to = date('Y-m-d', strtotime('yesterday'));

		if (Session::get('from')) {$this->from = Session::get('from');}
		if (Session::get('to')) {$this->to = Session::get('to');}

	}

	public function list() {

		$from = strip_tags($this->from);
		$to = strip_tags($this->to);
		$limit = 10000;

		$SQLstatement = $this->db->connection->prepare(

			"SELECT 
			 campaigns.order_id,
			 conversions.customer_id as customer_id,
			 conversions.order_date as order_date,
		 	 campaigns.utm_source,
			 campaigns.utm_medium,
			 campaigns.utm_campaign,
			 campaigns.utm_term,
			 conversions.article_id as article_id,
			 conversions.article_ressort as article_ressort,
			 conversions.order_price as order_price,
			 conversions.subscription_internal_title as subscription_internal_title,
			 conversions.cancelled as cancelled,
			 conversions.retention as retention

			 FROM campaigns

			 LEFT JOIN conversions ON conversions.order_id = campaigns.order_id

			 WHERE DATE(campaigns.ga_date) BETWEEN :startDate AND :endDate
			 ORDER BY conversions.order_date DESC
			 LIMIT 0, $limit"

		);

		$SQLstatement->execute([':startDate' => $from, ':endDate' => $to]);
		$orders = $SQLstatement->fetchall();

		return $orders;

	}

	public function filter_cancelled($campaigns) {
		if (empty($campaigns)) {return [];}
		return array_filter($campaigns, function($campaigns) {
			if ($campaigns['cancelled']) {return $campaigns;}
		});

	}

}
