<?php

namespace app\models;

use \plenigo\PlenigoManager;
use \plenigo\services\CompanyService;
use \plenigo\services\UserService;


class Plenigo
{

	function __construct($jsonFileName = null) {
		PlenigoManager::configure(PLENIGO_SECRET, PLENIGO_CUSTOMER_ID);
	}

	public function user($plenigoID) {

		if (!$plenigoID) {return null;}

		$user = CompanyService::getUserByIds($plenigoID);
		$user = $user->getElements();
		$user = $this->map_user_data($user[0]);
		return $user;

	}

	public function order_with_details($orderID) {

		if (!$orderID) {return null;}

		$order = $this->order($orderID);

		$subscriptions = $this->subscriptions($order['customerID']);

		$order['subscription_count'] = count($subscriptions);

		// Only interested in the latest Subscription
		$order['subscription'] = $subscriptions[0];

		// No use for Customer Products right now
		// $order['products'] = $this->products($order['customerID']);

		$user = $this->user($order['customerID']);
		$order['user'] = $user;

		return $order;

	}

	public function order($orderID) {

		try {
			$order = CompanyService::getOrder($orderID);
			$order = $this->map_order_data($order);
		} catch (\Exception $e) {
			return null;
		}

		return $order;

	}

	public function products($plenigoID) {

		if (!$plenigoID) {return null;}

		$plenigoProducts = UserService::getProductsBought($plenigoID);
		if (empty($plenigoProducts)) {return null;}

		$products = [];

		foreach ($plenigoProducts['subscriptions'] as $key => $product) {
			$products[$key] = $this->map_product_data($product);
		}

		return $products;

	}

	public function subscriptions($plenigoID) {

		if (!$plenigoID) {return null;}

		$plenigoSubscriptions = UserService::getSubscriptions($plenigoID);
		$plenigoSubscriptions = $plenigoSubscriptions->getElements();

		if (empty($plenigoSubscriptions)) {return null;}

		$subscriptions = [];

		foreach ($plenigoSubscriptions as $key => $subscription) {
			$subscriptions[$key] = $this->map_subscription_data($subscription);
		}

		return $subscriptions;

	}

	private function map_product_data($plenigoProduct) {

		$product['id'] = $plenigoProduct->productId;
		$product['title'] = $plenigoProduct->title;
		$product['paymentMethod'] = $plenigoProduct->paymentMethod;
		$product['price'] = $plenigoProduct->price;
		$product['currency'] = $plenigoProduct->currency;
		$product['productType'] = $plenigoProduct->productType;

		$product['startDate'] = null;
		if ($plenigoProduct->startDate) {
			$product['startDate'] = date('Y-m-d H:i:s', strtotime($plenigoProduct->startDate));
		}

		$product['endDate'] = null;
		if ($plenigoProduct->endDate) {
			$product['endDate'] = date('Y-m-d H:i:s', strtotime($plenigoProduct->endDate));
		}

		return $product;

	}

	private function map_subscription_data($plenigoSubscription) {

		$subscription['title'] = $plenigoSubscription->getTitle();
		$subscription['productId'] = $plenigoSubscription->getProductId();
		$subscription['price'] = $plenigoSubscription->getPrice();
		$subscription['currency'] = $plenigoSubscription->getCurrency();
		$subscription['paymentMethod'] = $plenigoSubscription->getPaymentMethod();

		$subscription['startDate'] = null;
		if ($plenigoSubscription->getStartDate()) {
			$subscription['startDate'] = date('Y-m-d H:i:s', strtotime($plenigoSubscription->getStartDate()));
		}

		$subscription['cancellationDate'] = null;
		if ($plenigoSubscription->getCancellationDate()) {
			$subscription['cancellationDate'] = date('Y-m-d H:i:s', strtotime($plenigoSubscription->getCancellationDate()));
		}

		$subscription['endDate'] = null;
		if ($plenigoSubscription->getEndDate()) {
			$subscription['endDate'] = date('Y-m-d H:i:s', strtotime($plenigoSubscription->getEndDate()));
		}

		$subscription['active'] = $plenigoSubscription->getActive(); // not working?
		$subscription['term'] = $plenigoSubscription->getTerm();
		$subscription['orderId'] = $plenigoSubscription->getOrderId();
		$subscription['subscriptionId'] = $plenigoSubscription->getSubscriptionId();

		return $subscription;

	}

	private function map_order_data($plenigoOrder) {

		$order['orderID'] = $plenigoOrder->getOrderId();
		$order['customerID'] = $plenigoOrder->getCustomerId();
		$order['externalCustomerID'] = $plenigoOrder->getExternalCustomerId();

		$order['orderDate'] = null;
		if ($plenigoOrder->getOrderDate()) {
			$order['orderDate'] = date('Y-m-d H:i:s', strtotime($plenigoOrder->getOrderDate()));
		}

		// Right now we only have Orders with one position
		$productInfo = $plenigoOrder->getOrderItems()[0];

		$order['productTitle'] = $productInfo->getTitle();
		$order['productID'] = $productInfo->getProductId();
		$order['productPrice'] = $productInfo->getPrice();
		$order['orderStatus'] = $productInfo->getStatus();

		return $order;

	}

	private function map_user_data($plenigoUser) {

		$user['customerId'] = $plenigoUser->getCustomerId();
		$user['externalCustomerId'] = $plenigoUser->getExternalCustomerId();
		$user['agreementState'] = $plenigoUser->getAgreementState();
		$user['userState'] = $plenigoUser->getUserState();

		$user['firstname'] = $plenigoUser->getFirstName();
		$user['lastname'] = $plenigoUser->getName();
		$user['email'] = $plenigoUser->getEmail();
		$user['gender'] = $plenigoUser->getGender();
		$user['city'] = $plenigoUser->getCity();

		return $user;

	}


}
