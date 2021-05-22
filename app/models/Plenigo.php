<?php

namespace app\models;

use app\importer\PlenigoImport;

class Plenigo
{

	private $plenigo;

	function __construct() {
		$this->plenigo = new PlenigoImport();
	}


	public function order_with_details($orderID) {


		if (!$orderID) {return null;}

		$order = $this->plenigo->order($orderID);

		if (isset($order['errorCode'])) {return null;}

		$subscription = $this->active_subscription($order);
		$subscription = $this->map_subscription_data($subscription);

		$customer = $this->map_customer($order);
		$order = $this->map_order($order);

		$data = array_merge($order, $subscription, $customer);

		return $data;

	}


	private function map_order($org) {

		$product = $org['items'][0];

		//$new['order_id'] = $org['orderId'];
		$new['order_product_id'] = $product['productId'];
		$new['order_date'] = formatDate($org['orderDate'], 'Y-m-d H:i:s');
		$new['order_title'] = $product['title'];
		$new['order_price'] = $product['price'];
		$new['order_status'] = $org['status'];

		return $new;

	}


	private function map_customer($org) {


		if (empty($org['invoiceAddress'])) {
			$address = $org['items'][0]['deliveryAddress'];
		}

		else {$address = $org['invoiceAddress'];}

		$new['customer_id'] = $org['invoiceCustomerId'];
		$new['customer_city'] = $address['city'];

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


	private function map_subscription_data($org) {

		$new['cancelled'] = 0;
		$new['retention'] = null;

		$new['subscription_title'] = $org['items'][0]['title'];
		$new['subscription_price'] = $org['items'][0]['price'];
		$new['subscription_payment_method'] = $org['paymentMethod'];
		$new['subscription_start_date'] = formatDate($org['startDate'], 'Y-m-d H:i:s');
		$new['subscription_cancellation_date'] = formatDate($org['cancellationDate'], 'Y-m-d H:i:s');
		$new['subscription_end_date'] = formatDate($org['endDate'], 'Y-m-d H:i:s');

		if ($new['subscription_cancellation_date']) {
			$new['cancelled'] = 1;

			$start = new \DateTime(formatDate($org['startDate'], 'Y-m-d'));
			$end = new \DateTime(formatDate($org['cancellationDate'], 'Y-m-d'));
			$interval = $start->diff($end);
			$new['retention'] = $interval->format('%r%a');
		}

		return $new;

	}


	private function active_subscription($order) {

		if (!isset($order['items'][0])) {
			return null;
		}

		$subscriptionID = $order['items'][0]['subscriptionItemId'];
		$subscription = $this->plenigo->subscription($subscriptionID);

		if (empty($subscription['cancellationDate']) && $subscription['status'] == 'INACTIVE') {

			$subscription = $this->plenigo->chain($subscription['chainId']);
			$subscription = $subscription['items'][0];
		}

		return $subscription;

	}


	private function map_fields($data) {


		$data['cancelled'] = 0;
		$data['retention'] = null;

		$data['customer_id']			= $plenigo['customerID'];
		$data['external_customer_id']	= $plenigo['externalCustomerID'];

		$data['order_product_id']		= $plenigo['productID'];
		$data['order_date']				= $plenigo['orderDate'];
		$data['order_title']			= $plenigo['productTitle'];
		$data['order_price']			= $plenigo['productPrice'];
		$data['order_status']			= $plenigo['orderStatus'];

		$data['subscription_title']				= $plenigo['subscription']['title'];
		$data['subscription_price']				= $plenigo['subscription']['price'];
		$data['subscription_payment_method']	= $plenigo['subscription']['paymentMethod'];
		$data['subscription_start_date']		= $plenigo['subscription']['startDate'];
		$data['subscription_cancellation_date']	= $plenigo['subscription']['cancellationDate'];
		$data['subscription_end_date']			= $plenigo['subscription']['endDate'];
		$data['subscription_count']				= $plenigo['subscription_count'];

		if ($plenigo['subscription']['cancellationDate']) {
			$data['cancelled'] = 1;

			$start = new \DateTime(formatDate($plenigo['subscription']['startDate'], 'Y-m-d'));
			$end = new \DateTime(formatDate($plenigo['subscription']['cancellationDate'], 'Y-m-d'));
			$interval = $start->diff($end);
			$data['retention'] = $interval->format('%r%a');
		}

		$data['customer_consent'] = $plenigo['user']['agreementState'];
		$data['customer_status'] = $plenigo['user']['userState'];
		$data['customer_gender'] = $plenigo['user']['gender'];
		$data['customer_city'] = $plenigo['user']['city'];


	}

}
