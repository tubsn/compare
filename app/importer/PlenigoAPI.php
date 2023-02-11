<?php

namespace app\importer;

use \flundr\cache\RequestCache;

class PlenigoAPI
{

	const API_BASE_URL = 'https://api.plenigo.com/api/v3.0';
	private $plenigoToken = PLENIGO_TOKEN;
	private $client = PORTAL;
	private $maxDaysPerRequest = 150;

	public function __construct() {}

	public function orders($start, $end, $lastID = null) {

		$startingAfter = null;
		if ($lastID) {$startingAfter = '&startingAfter=' . $lastID;}

		$apiQuery = '/orders/?startTime=' . $start . 'T00:00:00Z&endTime=' . $end . 'T23:59:59Z&size=100' . $startingAfter;
		$data = $this->curl($apiQuery);

		$orders = $data['items'];
		if (empty($orders)) {return;}

		if (count($orders) >= 100) {
			$lastID = end($orders)['orderId'];
			$newOrders = $this->orders($start,$end,$lastID);
			if ($newOrders) {$orders = array_merge($orders, $newOrders);}
		}

		return $orders;

	}

	public function changed_subscriptions($start, $end, $lastID = null) {

		$startingAfter = null;
		if ($lastID) {$startingAfter = '&startingAfter=' . $lastID;}

		$apiQuery = '/subscriptions/?startTime=' . $start . 'T00:00:00Z&endTime=' . $end . 'T23:59:59Z&size=100' . $startingAfter;
		$data = $this->curl($apiQuery);

		$orders = $data['items'];
		if (empty($orders)) {return;}

		if (count($orders) >= 100) {
			$lastID = end($orders)['subscriptionId'];
			$newOrders = $this->orders($start,$end,$lastID);
			if ($newOrders) {$orders = array_merge($orders, $newOrders);}
		}

		return $orders;

	}


	public function currently_active_subscriptions() {
		$end = date("Y-m-d", strtotime('today'));
		$apiQuery = '/analytics/subscriptions/active?calculationDate=' . $end . 'T23:59:59Z&interval=MONTH&size=12';
		return $this->curl($apiQuery);
	}


	public function app_store_apple($start, $end, $lastID = null) {

		$start = date("Y-m-d", strtotime($start));
		$end = date("Y-m-d", strtotime($end));

		if ($this->range_too_big($start, $end)) {
			throw new \Exception("Datespan is too long", 404);
		}

		$startingAfter = null;
		if ($lastID) {$startingAfter = '&startingAfter=' . $lastID;}

		$apiQuery = '/appStores/appleAppStore/?startTime=' . $start . 'T00:00:00Z&endTime=' . $end . 'T23:59:59Z&size=100' . $startingAfter;
		$data = $this->curl($apiQuery);

		$orders = $data['items'];
		if (empty($orders)) {return;}

		$endOfArrayID = end($orders)['appleAppStorePurchaseId'];
		if (count($orders) >= 100 && !empty($endOfArrayID)) {
			$newOrders = call_user_func([$this, __FUNCTION__], $start, $end, $endOfArrayID);
			if ($newOrders) {$orders = array_merge($orders, $newOrders);}
		}

		return $orders;

	}

	public function app_store_google($start, $end, $lastID = null) {

		$start = date("Y-m-d", strtotime($start));
		$end = date("Y-m-d", strtotime($end));

		if ($this->range_too_big($start, $end)) {
			throw new \Exception("Datespan is too long", 404);
		}

		$startingAfter = null;
		if ($lastID) {$startingAfter = '&startingAfter=' . $lastID;}

		$apiQuery = '/appStores/googlePlayStore/?startTime=' . $start . 'T00:00:00Z&endTime=' . $end . 'T23:59:59Z&size=100' . $startingAfter;
		$data = $this->curl($apiQuery);

		$orders = $data['items'];
		if (empty($orders)) {return;}

		$endOfArrayID = end($orders)['googlePlayStorePurchaseId'];
		if (count($orders) >= 100 && !empty($endOfArrayID)) {
			$newOrders = call_user_func([$this, __FUNCTION__], $start, $end, $endOfArrayID);
			if ($newOrders) {$orders = array_merge($orders, $newOrders);}
		}

		return $orders;

	}

	public function app_store_mapped_order($id) {
		return $this->curl('/appStores/orders/' . $id);
	}

	public function app_store_mapped_orders($start, $end, $lastID = null) {

		$start = date("Y-m-d", strtotime($start));
		$end = date("Y-m-d", strtotime($end));

		$startingAfter = null;
		if ($lastID) {$startingAfter = '&startingAfter=' . $lastID;}

		$apiQuery = '/appStores/orders/?startTime=' . $start . 'T00:00:00Z&endTime=' . $end . 'T23:59:59Z&size=100' . $startingAfter;
		$data = $this->curl($apiQuery);

		$orders = $data['items'];
		if (empty($orders)) {return;}

		$endOfArrayID = end($orders)['appStoreOrderId'];
		if (count($orders) >= 100 && !empty($endOfArrayID)) {
			$newOrders = call_user_func([$this, __FUNCTION__], $start, $end, $endOfArrayID);
			if ($newOrders) {$orders = array_merge($orders, $newOrders);}
		}

		return $orders;

		/*
		$start = '2022-10-04';

		$params = '?size=100';
		$params .= '&startTime=' . $start . 'T00:00:00Z';

		//https://api.plenigo.com/api/v3.0/appStores/googlePlayStore
		//https://api.plenigo.com/api/v3.0/appStores/appleAppStore
		//$apiQuery = '/appStores/appleAppStore';
		$apiQuery = '/appStores/orders';
		//$apiQuery = '/appStores/googlePlayStore';
		//$apiQuery = '/appStores/subscriptions';
		$data = $this->curl($apiQuery.$params);
		return $data;
		*/

	}

	public function invoices($start = 'today', $end = 'today', $lastID = null) {

		$start = date("Y-m-d", strtotime($start));
		$end = date("Y-m-d", strtotime($end));

		if ($this->range_too_big($start, $end)) {
			throw new \Exception("Too many items requestet - Period should not be longer then a month", 404);
		}

		$cache = new RequestCache('invoices' . PORTAL . $start . $end, 60*60);

		$invoices = $cache->get();
		if (!empty($invoices)) {return $invoices;}

		$startingAfter = null;
		if ($lastID) {$startingAfter = '&startingAfter=' . $lastID;}

		$apiQuery = '/invoices/?startTime=' . $start . 'T00:00:00Z&endTime=' . $end . 'T23:59:59Z&size=100' . $startingAfter;

		$data = $this->curl($apiQuery);

		$invoices = $data['items'];
		if (empty($invoices)) {return;}

		if (count($invoices) >= 100) {
			$lastID = end($invoices)['invoiceId'];
			$newInvoices = $this->invoices($start,$end,$lastID);
			if ($newInvoices) {$invoices = array_merge($invoices, $newInvoices);}
		}

		$cache->save($invoices);
		return $invoices;

	}

	private function number_of_days($start, $end) {
  		$interval = date_diff(date_create($start), date_create($end));
  		return $interval->format('%a');
  	}

  	private function range_too_big($start, $end) {
		if ($this->number_of_days($start, $end) > $this->maxDaysPerRequest) {
			return true;
		}
  	}

	public function transactions($start = 'today', $end = 'today', $filter = null, $lastID = null) {

		$start = date("Y-m-d", strtotime($start));
		$end = date("Y-m-d", strtotime($end));

		if ($this->range_too_big($start, $end)) {
			throw new \Exception("Too many items requestet - Period should not be longer then a month", 404);
		}

		$cache = new RequestCache('transactions' . PORTAL . $start . $end . $filter, 24*60*60);
		$cache->cacheDirectory = ROOT . 'cache' . DIRECTORY_SEPARATOR . 'transactions';

		$transactions = $cache->get();
		if (!empty($transactions)) {return $transactions;}

		$startingAfter = null;
		if ($lastID) {$startingAfter = '&startingAfter=' . $lastID;}

		$searchFilter = null;
		if ($filter) {$searchFilter = '&plenigoTransactionId=' . $filter;}

		$apiQuery = '/transactions/?startTime=' . $start . 'T00:00:00Z&endTime=' . $end . 'T23:59:59Z&size=100' . $startingAfter . $searchFilter;

		$data = $this->curl($apiQuery);
		$transactions = $data['items'];
		if (empty($transactions)) {return;}

		if (count($transactions) >= 100) {
			$lastID = end($transactions)['transactionId'];
			$newTransactions = $this->transactions($start,$end,null,$lastID);
			if ($newTransactions) {$transactions = array_merge($transactions, $newTransactions);}
		}

		$cache->save($transactions);
		return $transactions;

	}

	public function order($id) {
		$data = $this->curl('/orders/' . $id);
		$additionalData = $this->curl('/orders/' . $id . '/additionalData');
		$data = array_merge($data, $additionalData);
		return $data;
	}

	public function subscription($id) {
		$data = $this->curl('/subscriptions/' . $id);
		return $data;
	}

	public function chain($id) {
		$data = $this->curl('/subscriptions/chain/' . $id);
		return $data;
	}


	public function subscriptions($id) {
		$data = $this->curl('/customers/' . $id . '/subscriptions');
		return $data;
	}

	public function customer($id) {
		$data = $this->curl('/customers/' . $id);
		return $data;
	}

	public function customer_additional_data($id) {
		$data = $this->curl('/customers/' . $id . '/additionalData');
		return $data;
	}

	public function customer_app_orders($id ) {
		$data = $this->curl('/customers/' . $id . '/appStoreOrders');
		return $data;
	}


	public function test() {
		$id = 801001045594 	;
		//$data = $this->curl('/customers/' . $id . '/orders');

		$get = '?interval=MONTH&size=12';

		$data = $this->curl('/analytics/transactions' . $get);

		return $data;
	}


	private function curl($path, $data = null) {

		$url = PlenigoAPI::API_BASE_URL . $path;

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    'X-plenigo-token: ' . $this->plenigoToken,
		    'Content-Type: application/json'
		));

		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

		$recievedData = curl_exec($ch);
		if ($recievedData === false) {
			dd(curl_error($ch));
		}

		curl_close ($ch);

		return json_decode($recievedData, true);

	}

}
