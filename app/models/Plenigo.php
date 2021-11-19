<?php

namespace app\models;
use \flundr\utility\Session;
use app\importer\PlenigoAPI;

class Plenigo
{

	public $api;

	function __construct() {
		$this->api = new PlenigoAPI();
		$this->api->client(Session::get('client') ?? 'LR');
	}

	public function orders($start, $end, $items, $showAll = false) {

		$orders = $this->api->orders($start, $end, $items);
		if (empty($orders)) {return [];}

		if (!$showAll) {$orders = array_filter($orders, [$this, 'remove_free_products']);}
		$orders = array_map([$this, 'map_order'], $orders);
		return $orders;
	}



	public function order_with_details($orderID) {

		if (!$orderID) {return null;}

		$order = $this->api->order($orderID);
		if (isset($order['errorCode'])) {return null;}

		$subscription = $this->active_subscription($order);
		$subscription['orderDate'] = $order['orderDate']; // Keep original Orderdate

		$subscription = $this->map_subscription_data($subscription);

		$customer = $this->map_customer($order);
		$order = $this->map_order($order);

		// Failsafe if there is no OrderID in Plenigo, which seems to happen
		if (empty($order['order_id'])) {$order['order_id'] = $orderID;}

		$data = array_merge($order, $subscription, $customer);

		return $data;

	}

	public function order($id) {
		$order = $this->api->order($id);
		dd($order);
	}

		private function map_order($org) {

		$new['order_id'] = $org['orderId'];
		$new['order_date'] = date("Y-m-d H:i:s", strtotime($org['orderDate']));

		$new['order_title'] = $org['items'][0]['title'];
		$new['order_price'] = $org['items'][0]['price'];
		$new['order_payment_method'] = $org['paymentMethod'];

		if (isset($org['data']['sourceUrl'])) {
			$new['order_source'] = $org['data']['sourceUrl'];
			$new['article_id'] = $this->extract_id($org['data']['sourceUrl']);
			$new['article_ressort'] = $this->extract_ressort($org['data']['sourceUrl']);
		}

		return $new;

	}

	private function active_subscription($order) {

		if (!isset($order['items'][0])) {return null;}
		$subscriptionID = $order['items'][0]['subscriptionItemId'] ?? null;

		if (!$subscriptionID) {return null;}
		$subscription = $this->api->subscription($subscriptionID);

		if (empty($subscription['cancellationDate']) && $subscription['status'] == 'INACTIVE') {
			$subscription = $this->api->chain($subscription['chainId']);
			$subscription = $subscription['items'][0];
		}

		return $subscription;

	}

	private function map_subscription_data($org) {

		if (empty($org)) {return [];}

		$new['subscription_id'] = $org['items'][0]['subscriptionItemId'];
		$new['subscription_title'] = $org['items'][0]['title'];
		$new['subscription_internal_title'] = $org['items'][0]['internalTitle'];
		$new['subscription_product_id'] = $org['items'][0]['productId'];
		$new['subscription_price'] = $org['items'][0]['price'];

		$new['subscription_start_date'] = null;
		$new['subscription_cancellation_date'] = null;
		$new['subscription_end_date'] = null;

		/* Date has to be Converted cause Plenigo saves UTC Dates */
		if ($org['startDate']) {
			$new['subscription_start_date'] = date("Y-m-d H:i:s", strtotime($org['startDate']));
		}

		if ($org['cancellationDate']) {
			$new['subscription_cancellation_date'] = date("Y-m-d H:i:s", strtotime($org['cancellationDate']));
		}
		if ($org['endDate']) {
			$new['subscription_end_date'] = date("Y-m-d H:i:s", strtotime($org['endDate']));
		}

		$new['cancelled'] = 0;
		$new['retention'] = null;

		if ($new['subscription_cancellation_date']) {
			$new['cancelled'] = 1;

			$start = new \DateTime(formatDate($org['orderDate'], 'Y-m-d'));
			$end = new \DateTime(formatDate($org['cancellationDate'], 'Y-m-d'));
			$interval = $start->diff($end);
			$new['retention'] = $interval->format('%r%a');
		}

		return $new;

	}

	private function map_customer($org) {

		if (empty($org['invoiceAddress'])) {
			$address = $org['items'][0]['deliveryAddress'];
		}

		else {$address = $org['invoiceAddress'];}

		$new['customer_id'] = $org['invoiceCustomerId'];
		if (empty($address['city'])) {$new['customer_city'] = null;} else {$new['customer_city'] = $address['city'];}
		if (empty($address['postcode'])) {$new['customer_postcode'] = null;} else {$new['customer_postcode'] = $address['postcode'];}
		$new['customer_country'] = $address['country'];

		switch ($address['salutation']) {
			case 'MRS':
				$new['customer_gender'] = 'female';
				break;
			case 'MR':
				$new['customer_gender'] = 'male';
				break;
			case 'FIRM':
				$new['customer_gender'] = 'company';
				break;
			default:
				$new['customer_gender'] = null;
				break;
		}

		return $new;

	}


	private function extract_id($url) {
		// Regex search for the ID = -8Digits.html
		$searchPattern = "/-(\d{8}).html/";
		preg_match($searchPattern, $url, $matches);
		if (isset($matches[1])) {
			return $matches[1]; // First Match should be the ID
		}
		return null;
	}

	private function extract_ressort($url) {

		$path = parse_url($url, PHP_URL_PATH);
		$path = trim ($path, '/');
		$paths = explode('/',$path);

		$paths = array_filter($paths, function($path) {
			return strpos($path,'.html') == false ;
		});

		if (!isset($paths[0])) {return null;}


		switch (PORTAL) {

			case 'LR':
				if ($paths[0] == 'lausitz') {return $paths[1];}
				if ($paths[0] == 'ratgeber' || $paths[0] == 'blaulicht') {return 'nachrichten';}
				if (isset($paths[1]) && $paths[1] == 'sport') {return $paths[1];}
			break;

			case 'MOZ':
				if ($paths[0] == 'lokales') {return $paths[1];}
				if ($paths[0] == 'nachrichten') {return $paths[1];}
				if (isset($paths[1]) && $paths[1] == 'sport') {return $paths[1];}
			break;

			case 'SWP':
				if ($paths[0] == 'suedwesten') {return $paths[2];}
				if ($paths[0] == 'lokales') {return $paths[1];}
				if ($paths[0] == 'sport') {return $paths[0];}				
			break;

		}

		return $paths[0] ?? 'unbekannt';

	}

	private function remove_free_products($order) {
		if ($order['paymentMethod'] == 'ZERO') {return null;}
		return $order;
	}

}
