<?php

namespace app\models;
use \flundr\utility\Session;
use \flundr\cache\RequestCache;
use app\importer\PlenigoAPI;
use app\importer\mapping\PlenigoAppStoreMapping;

class Plenigo
{

	public $api;

	function __construct() {
		$this->api = new PlenigoAPI();
	}

	public function orders($start, $end, $items, $showAll = false) {

		$orders = $this->api->orders($start, $end, $items);
		
		if (empty($orders)) {return [];}

		if (!$showAll) {$orders = array_filter($orders, [$this, 'remove_free_products']);}
		
		$orderData = array_map([$this, 'map_order'], $orders);
		$subscriptionData = array_map([$this, 'map_subscription_data'], $orders);
		$customerData = array_map([$this, 'map_customer'], $orders);

		$output = [];
		foreach ($orders as $key => $order) {
			
			$output[$key] = array_merge($orderData[$key], $subscriptionData[$key], $customerData[$key]);

		}
		return $output;
	}



	public function appstore_orders($start = 'first day of this month', $end = 'today') {

		$mapper = new PlenigoAppStoreMapping();

		$appStoreOrders = $this->api->app_store_apple($start,$end);
		$appStoreOrders = array_map([$mapper, 'map_appstore_order'], $appStoreOrders);
		
		$playStoreOrders = $this->api->app_store_google($start,$end);
		$playStoreOrders = array_map([$mapper, 'map_playstore_order'], $playStoreOrders);

		$appOrders = $playStoreOrders + $appStoreOrders;

		$appOrders = array_filter($appOrders, function($order) {
			if ($order['is_valid_product'] && $order['valid'] && $order['order_type'] == 'Production') {return $order;}
		});

		usort($appOrders, function($a, $b) {
			return $a['order_date'] < $b['order_date'];
		});

		return array_values($appOrders);

	}

	public function appstores_mapped_to_customers($start = 'yesterday', $end = 'yesterday') {

		$appOrders = $this->api->app_store_mapped_orders($start,$end);
		$mapper = new PlenigoAppStoreMapping();
		$appOrders = array_map([$mapper, 'map_appstore_order'], $appOrders);

		return $appOrders;

	}


	public function customer($id) {
		return $this->api->customer($id);
	}


	public function customer_additional_data($id) {
		$id = strip_tags($id);
		return $this->api->customer_additional_data($id);
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


	public function invoices($start = 'today', $end = 'today') {

		$invoices = $this->api->invoices($start, $end);
		$invoices = array_map([$this, 'map_customer_with_invoice_data'], $invoices);

		return $invoices;
	}


	public function transactions($start, $end) {
		
		$transactions = $this->api->transactions($start, $end);

		// Exclude the Mandate Creations
		$transactions = array_filter($transactions, function($transaction) {
			if ($transaction['paymentAction'] != 'SEPA_MANDATE_CREATION') {return $transaction;}
		});

		return $transactions;

	}

	public function transactions_clustered($start, $end) {

		$transactions = $this->api->transactions($start, $end);

		if (empty($transactions)) {
			return [];
		}

		// Debug for Paypal Only
		if (isset($_GET['paypal'])) {
			$transactions = array_filter($transactions, function($transaction) {
				if ($transaction['paymentProvider'] == 'PAYPAL') {return $transaction;}
			});			
		}

		// Debug for Stripe Only
		if (isset($_GET['stripe'])) {
			$transactions = array_filter($transactions, function($transaction) {
				if ($transaction['paymentProvider'] == 'STRIPE') {return $transaction;}
			});			
		}

		// Separating the Mandate-Creations from the rest
		$mandates = $this->array_filter('paymentAction', 'SEPA_MANDATE_CREATION', $transactions);
		$transactionsWithoutMandates = array_filter($transactions, function($entry) {
			if ($entry['paymentAction'] != 'SEPA_MANDATE_CREATION') {return $entry;}
		});

		$success = $this->filter_succes($transactionsWithoutMandates);
		$chargebacks = $this->filter_chargebacks($transactionsWithoutMandates);
		$failures = $this->filter_failed($transactionsWithoutMandates);

		return [
			'Transaktionen' => $transactionsWithoutMandates,
			'Erfolgreiche Transaktionen' => $success,
			'Fehlgeschlagene Transaktionen' => $failures,
			'Rückzahlungen' => $chargebacks,
			'SEPA Mandate' => $mandates,
		];

	}

	private function filter_failed(array $array) {
		return array_filter($array, function($entry) {
			if (
				$entry['paymentStatus'] == 'FAILURE' 
				|| $entry['paymentAction'] == 'PAYPAL_REFUND'
				|| $entry['paymentAction'] == 'CREDIT_CARD_REFUND'
				|| $entry['paymentAction'] == 'SEPA_VOID'
			) {return $entry;}
		});
	}

	private function filter_succes(array $array) {
		return array_filter($array, function($entry) {
			if (
				$entry['paymentStatus'] == 'SUCCESS' 
				&& $entry['paymentAction'] != 'PAYPAL_REFUND'
				&& $entry['paymentAction'] != 'CREDIT_CARD_REFUND'
				&& $entry['paymentAction'] != 'SEPA_VOID'
			) {return $entry;}
		});
	}

	private function filter_chargebacks(array $array) {
		return array_filter($array, function($entry) {
			if (
				$entry['paymentAction'] == 'SEPA_DEBIT_RETURN' 
				|| $entry['paymentAction'] == 'PAYPAL_REFUND'
				|| $entry['paymentAction'] == 'CREDIT_CARD_REFUND'
				|| $entry['paymentAction'] == 'SEPA_VOID'
			) {return $entry;}
		});
	}

	private function array_filter($column, $value, array $array) {
		return array_filter($array, function($entry) use ($column, $value) {
			if ($entry[$column] == $value) {return $entry;}
		});
	}


	// Costs see agreements
	// Paypal 2,99% + 35 Cent
	// Stripe 1.5% + 13 Cent
	// Stripe Visa 2,4% + 20 Cent

	private function calculate_paypal_costs($amount) {return round(0.39 + (0.0299 * $amount),2);}
	private function calculate_stripe_costs($amount) {return round(0.13 + (0.015 * $amount),2);}
	private function calculate_stripe_creditcard_costs($amount) {return round(0.20 + (0.024 * $amount),2);}

	public function calculate_transaction_costs($transactions) {

		$total = 0;
		foreach ($transactions as $transaction) {
			$amount = 0;
			if ($transaction['paymentMethod'] == 'PAYPAL') {$amount = $this->calculate_paypal_costs($transaction['amount']);}
			if ($transaction['paymentMethod'] == 'BANK_ACCOUNT') {$amount = $this->calculate_stripe_costs($transaction['amount']);}
			if ($transaction['paymentMethod'] == 'CREDIT_CARD') {$amount = $this->calculate_stripe_creditcard_costs($transaction['amount']);}
			$total += $amount;
		}

		return $total;
	}

	public function calculate_chargeback_costs($transactions) {

		// Paypal 0 + Transaktionskosten 
		// Stripe 3,5€

		$total = 0;
		foreach ($transactions as $transaction) {
			$amount = 0;
			if ($transaction['paymentMethod'] == 'PAYPAL') {$amount = $this->calculate_paypal_costs($transaction['amount']);}
			if ($transaction['paymentMethod'] == 'BANK_ACCOUNT') {$amount = 3.5;}
			if ($transaction['paymentMethod'] == 'CREDIT_CARD') {$amount = 3.5;}
			$total += $amount;
		}
		
		return $total;

	}


	private function map_order($org) {

		$new['order_id'] = $org['orderId'];
		$new['order_date'] = date("Y-m-d H:i:s", strtotime($org['orderDate']));
		$new['order_title'] = $org['items'][0]['title'];
		$new['order_price'] = $org['items'][0]['price'];
		$new['order_payment_method'] = $org['paymentMethod'];

		if (isset($org['data']['sourceUrl'])) {

			$url = $org['data']['sourceUrl'];

			$new['order_source'] = $url;
			$new['order_origin'] = $this->extract_order_origin($url);

			if ($new['order_origin'] == 'Artikel') {
				$new['article_id'] = $this->extract_id($url);
				$new['article_ressort'] = $this->extract_ressort($url);
			}

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
			//dd($subscription);
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

		$new['subscription_status'] = $org['status'];
		$new['subscription_start_date'] = null;
		$new['subscription_cancellation_date'] = null;
		$new['subscription_end_date'] = null;

		/* Date has to be Converted cause Plenigo saves UTC Dates */
		if (isset($org['startDate'])) {
			$new['subscription_start_date'] = date("Y-m-d H:i:s", strtotime($org['startDate']));
		}

		if (isset($org['cancellationDate'])) {
			$new['subscription_cancellation_date'] = date("Y-m-d H:i:s", strtotime($org['cancellationDate']));
		}
		if (isset($org['endDate'])) {
			$new['subscription_end_date'] = date("Y-m-d H:i:s", strtotime($org['endDate']));
		}

		$new['cancelled'] = 0;
		$new['cancellation_reason'] = null;
		$new['retention'] = null;

		if ($new['subscription_cancellation_date']) {
			$new['cancelled'] = 1;

			if (!empty($org['cancellationReasonUniqueId'])) {
				$new['cancellation_reason'] = $org['cancellationReasonUniqueId'];
			}

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

		switch ($address['salutation'] ?? null) {
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

	private function map_customer_with_invoice_data($org) {

		if (empty($org['invoiceAddress'])) {
			$address = $org['items'][0]['deliveryAddress'];
		}

		else {$address = $org['invoiceAddress'];}

		$new['invoice_id'] = $org['invoiceId'];
		$new['invoice_date'] = $org['invoiceDate'];
		$new['invoice_type'] = $org['type'];
		$new['invoice_status'] = $org['status'];
		$new['invoice_price'] = $org['accumulatedPrice'] ?? null;
		$new['invoice_payment_method'] = $org['paymentMethod']  ?? null;
		$new['invoice_transaction_id'] = $org['transactionId'] ?? null;
		$new['customer_id'] = $org['invoiceCustomerId'];
		$new['customer_email'] = $org['customerEmail'];
		$new['customer_firstname'] = $address['firstName'] ?? null;
		$new['customer_lastname'] = $address['lastName'] ?? null;
		$new['customer_company'] = $address['companyName'] ?? null;


		$new['customer_id'] = $org['invoiceCustomerId'];
		if (empty($address['city'])) {$new['customer_city'] = null;} else {$new['customer_city'] = $address['city'];}
		if (empty($address['postcode'])) {$new['customer_postcode'] = null;} else {$new['customer_postcode'] = $address['postcode'];}
		$new['customer_country'] = $address['country'];

		switch ($address['salutation'] ?? null) {
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


	private function extract_order_origin($url) {

		if (empty($url)) {return 'Extern';}

		$host = parse_url($url, PHP_URL_HOST);
		$path = parse_url($url, PHP_URL_PATH);
		$path = trim ($path, '/');
		$paths = explode('/',$path);

		$testUrls = ['lro-int.swp.de', 'int.swp.de', 'moz-int.swp.de',
					 'lro.int.red.swp.de', 'int.red.swp.de', 'moz.int.red.swp.de'];
		if (in_array($host, $testUrls)) {return 'Test';}

		$shopUrls = ['abo.lr-online.de', 'abo.moz.de', 'abo.swp.de'];
		if (in_array($host, $shopUrls)) {return 'Aboshop';}

		if (strTolower($paths[2] ?? null) == 'kuendigung') {return 'Umwandlung';}
		if (strTolower($paths[0] ?? null) == 'plus') {return 'Plusseite';}


		$pageUrls = ['www.lr-online.de', 'www.moz.de', 'www.swp.de'];
		if (in_array($host, $pageUrls)) {return 'Artikel';}

		return 'Extern';

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
				if (isset($paths[1]) && $paths[1] == 'kultur') {return $paths[1];}
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
