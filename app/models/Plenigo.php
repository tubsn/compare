<?php

namespace app\models;
use \flundr\utility\Session;
use \flundr\cache\RequestCache;
use app\importer\PlenigoAPI;
use app\importer\mapping\PlenigoAppStoreMapping;
use app\importer\mapping\PlenigoOrderMapping;

class Plenigo
{

	public $api;

	function __construct() {
		$this->api = new PlenigoAPI();
	}

	public function subscriptions($start, $end, $showAll = false) {

		$orders = $this->api->changed_subscriptions($start, $end);


		$orders = array_filter($orders, function($order) {

			// do not Include Orders			
			if (isset($order['type']) && $order['type'] == 'ORDER') {return false;}

			// Cancelations only
			if (!empty($order['cancellationDate'])) {
				return $order;
			}

		});

		return $orders;
		
		$mapper = new PlenigoOrderMapping();
		$orderData = array_map([$mapper, 'map_running_subscription'], $orders);
	






		/*
		$subscriptionData = array_map([$mapper, 'map_subscription_data'], $orders);
		$customerData = array_map([$mapper, 'map_customer'], $orders);

		$output = [];
		foreach ($orders as $key => $order) {

			$output[$key] = array_merge($orderData[$key], $subscriptionData[$key], $customerData[$key]);

		}
		*/
		return $output;

	}

	public function orders($start, $end, $showAll = false) {

		$orders = $this->api->orders($start, $end);

		if (empty($orders)) {return [];}

		if (!$showAll) {$orders = array_filter($orders, [$this, 'remove_free_products']);}

		$mapper = new PlenigoOrderMapping();
		$orderData = array_map([$mapper, 'map_order'], $orders);
		$subscriptionData = array_map([$mapper, 'map_subscription_data'], $orders);
		$customerData = array_map([$mapper, 'map_customer'], $orders);

		$output = [];
		foreach ($orders as $key => $order) {

			$output[$key] = array_merge($orderData[$key], $subscriptionData[$key], $customerData[$key]);

		}
		return $output;
	}



	public function currently_active_subscriptions() {
		return $this->api->currently_active_subscriptions();
	}

	public function appstore_orders($start = '2022-10-01', $end = 'today') {

		$mapper = new PlenigoAppStoreMapping();

		$appStoreOrders = $this->api->app_store_apple($start,$end);
		$appStoreOrders = array_map([$mapper, 'map_appstore_order'], $appStoreOrders);

		$playStoreOrders = $this->api->app_store_google($start,$end);
		$playStoreOrders = array_map([$mapper, 'map_playstore_order'], $playStoreOrders);

		$appOrders = $playStoreOrders + $appStoreOrders;
		$appOrders = array_filter($appOrders, [$this, 'valid_app_products_and_orders']);

		usort($appOrders, function($a, $b) {
			return $a['order_date'] < $b['order_date'];
		});

		foreach ($appOrders as $key => $order) {
			if (isset($order['mapped_order_id']) && $order['mapped_order_id'] != 0) {
				$appOrders[$key]['customer_id'] = $this->get_mapped_app_store_customer_id($order['mapped_order_id']);
			}
		}

		return array_values($appOrders);

	}

	private function get_mapped_app_store_customer_id($appStoreOrderId) {
		//$mapper = new PlenigoAppStoreMapping();
		$data = $this->api->app_store_mapped_order($appStoreOrderId);
		//dd($mapper->map_appstore_order($data));
		return $data['customerId'];

	}

	private function valid_app_products_and_orders($order) {
		if ($order['is_valid_product'] && $order['valid'] && $order['order_type'] == 'Production') {return $order;}
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

		$mapper = new PlenigoOrderMapping();
		$subscription = $mapper->map_subscription_data($subscription);
		$customer = $mapper->map_customer($order);
		$order = $mapper->map_order($order);

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

		$mapper = new PlenigoOrderMapping();
		$invoices = $this->api->invoices($start, $end);
		$invoices = array_map([$mapper, 'map_customer_with_invoice_data'], $invoices);

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

	public function get_chain($id) {
		return $this->api->chain($id);
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

	private function remove_free_products($order) {
		if ($order['paymentMethod'] == 'ZERO') {return null;}
		return $order;
	}

}
