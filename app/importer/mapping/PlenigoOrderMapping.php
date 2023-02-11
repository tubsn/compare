<?php

namespace app\importer\mapping;

class PlenigoOrderMapping
{

	public function map_order($org) {

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

	public function map_running_subscription($org) {


		dd($org);


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




	public function map_subscription_data($org) {

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


	public function map_customer($org) {

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

	public function map_customer_with_invoice_data($org) {

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
}
