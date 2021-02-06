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

		$user = CompanyService::getUserByIds($plenigoID);
		$user = $user->getElements();
		$user = $this->map_user_data($user[0]);
		return $user;

	}

	public function products($plenigoID) {

		$plenigoProducts = UserService::getProductsBought($plenigoID);

		$products = [];

		foreach ($plenigoProducts['subscriptions'] as $key => $product) {
			$products[$key] = $this->map_product_data($product);
		}

		return $products;

	}

	public function subscriptions($plenigoID) {

		$plenigoSubscriptions = UserService::getSubscriptions($plenigoID);
		$plenigoSubscriptions = $plenigoSubscriptions->getElements();

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
		$product['startDate'] = strtotime($plenigoProduct->startDate);
		$product['endDate'] = strtotime($plenigoProduct->endDate);

		return $product;

	}

	private function map_subscription_data($plenigoSubscription) {

		$subscription['title'] = $plenigoSubscription->getTitle();
		$subscription['productId'] = $plenigoSubscription->getProductId();
		$subscription['price'] = $plenigoSubscription->getPrice();
		$subscription['currency'] = $plenigoSubscription->getCurrency();
		$subscription['paymentMethod'] = $plenigoSubscription->getPaymentMethod();
		$subscription['startDate'] = strtotime($plenigoSubscription->getStartDate());
		$subscription['cancellationDate'] = strtotime($plenigoSubscription->getCancellationDate());
		$subscription['active'] = $plenigoSubscription->getActive(); // not working?
		$subscription['term'] = $plenigoSubscription->getTerm();
		$subscription['orderId'] = $plenigoSubscription->getOrderId();
		$subscription['subscriptionId'] = $plenigoSubscription->getSubscriptionId();

		return $subscription;

	}


	private function map_user_data($plenigoUser) {

		$user['firstname'] = $plenigoUser->getFirstName();
		$user['lastname'] = $plenigoUser->getName();
		$user['email'] = $plenigoUser->getEmail();
		$user['gender'] = $plenigoUser->getGender();
		$user['city'] = $plenigoUser->getCity();

		return $user;

	}


}
