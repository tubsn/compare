<?php

namespace app\importer\mapping;

class PlenigoAppStoreMapping
{

	public $validProducts = APP_PRODUCTS;
	private $defaultFields = [
		'order_id' => null,
		'customer_id' => null,
		'order_status' => null,
		'order_title' => null,
		'order_origin' => 'UNKNOWN',
		'order_type' => null, // Stage or Live
		'order_date' => null,
		'subscription_start_date' => null,
		'subscription_end_date' => null,
		'subscription_cancellation_date' => null,
		'order_plenigo_refresh_date' => null,
		'order_plenigo_added_date' => null,
		'sub_orders' => null,
		'valid' => null,
		'is_valid_product' => null,
	];


	public function map_playstore_order($org) {

		$new = $this->defaultFields;
		$new['order_id'] = $org['googlePlayStorePurchaseId'];
		$new['order_title'] = $org['productId'];
		$new['order_origin'] = 'PLAYSTORE';
		$new['valid'] = $org['valid'];

		$new['order_plenigo_refresh_date'] = date("Y-m-d H:i:s", strtotime($org['changedDate']));
		$new['order_plenigo_added_date'] = date("Y-m-d H:i:s", strtotime($org['purchaseDate']));

		if (!empty($org['productPurchase'])) {
			$new['order_date'] = date("Y-m-d H:i:s", ($org['productPurchase']['purchaseTimeMillis'] / 1000));
		}

		if (!empty($org['subscriptionPurchase'])) {
			$new['subscription_start_date'] = date("Y-m-d H:i:s", ($org['subscriptionPurchase']['startTimeMillis'] / 1000));
			$new['subscription_end_date'] = date("Y-m-d H:i:s", ($org['subscriptionPurchase']['expiryTimeMillis'] / 1000));
		}

		if (empty($new['order_date'])) {
			$new['order_date'] = $new['subscription_start_date'] ?? null;
		}

		if (in_array($org['productId'], $this->validProducts)) {$new['is_valid_product'];}

		return $new;

	}



	public function map_appstore_order($org) {

		$new['order_id'] = $org['appleAppStorePurchaseId'] ?? $org['googlePlayStorePurchaseId'] ?? $org['appStoreOrderId'] ?? null;
		$new['customer_id'] = $org['customerId'] ?? null;
		$new['order_status'] = $org['status'] ?? $org['receipt']['status'] ?? null;
		$new['order_title'] = null;
		
		$new['order_origin'] = 'UNKNOWN';
		if (isset($org['appleAppStorePurchaseId'])) {$new['order_origin'] = 'APPSTORE';}
		if (isset($org['googlePlayStorePurchaseId'])) {$new['order_origin'] = 'PLAYSTORE';}
		if (isset($org['storeType'])) {$new['order_origin'] = $org['storeType'];}

		$new['order_type'] = null;
		if (isset($org['receipt'])) {
			$new['order_type'] = $org['receipt']['receiptType'] ?? null;
		}

		$new['order_date'] = null;		
		if (isset($org['orderDate'])) {
			$new['order_date'] = date("Y-m-d H:i:s", strtotime($org['orderDate']));
		}

		$new['subscription_start_date'] = null;
		$new['subscription_end_date'] = null;
		$new['subscription_cancellation_date'] = null;

		$new['order_plenigo_refresh_date'] = null;		
		if (isset($org['changedDate'])) {
			$new['order_plenigo_refresh_date'] = date("Y-m-d H:i:s", strtotime($org['changedDate']));
		}

		$new['order_plenigo_added_date'] = null;		
		if (isset($org['purchaseDate'])) {
			$new['order_plenigo_added_date'] = date("Y-m-d H:i:s", strtotime($org['purchaseDate']));
		}

		if (isset($org['items'])) {
			$new['sub_orders'] = array_map([$this, 'map_appstore_suborder'], $org['items']);
		}

		if (isset($org['receipt']['items'])) {
			$new['sub_orders'] = array_map([$this, 'map_appstore_suborder'], $org['receipt']['items']);
		}

		$new['valid'] = $org['valid'] ?? null;
		$new['is_valid_product'] = null;
		foreach ($new['sub_orders'] as $order) {
			if (in_array($order['product_id'], $this->validProducts)) {
				$new['is_valid_product'] = true;
				$new['order_title'] = $order['product_id'];
				$new['order_date'] = date("Y-m-d H:i:s", strtotime($order['original_purchase_date']));
				$new['subscription_start_date'] = date("Y-m-d H:i:s", strtotime($order['purchase_date']));
				$new['subscription_end_date'] = date("Y-m-d H:i:s", strtotime($order['expires_date']));

				if ($order['cancellation_date']) {
					$new['subscription_cancellation_date'] = date("Y-m-d H:i:s", strtotime($order['cancellation_date']));
				}

			}
		}

		return $new;

	}

	private function map_appstore_suborder($org) {

		$new['access_right'] = $org['accessRightUniqueId'] ?? null;
		$new['product_id'] = $org['productId'] ?? null;
		
		$new['expires_date'] = $org['expiresDate'] ?? null;
		$new['purchase_date'] = $org['purchaseDate'] ?? null;
		$new['original_purchase_date'] = $org['originalPurchaseDate'] ?? null;
		$new['cancellation_date'] = $org['cancellationDate'] ?? null;
		$new['cancellation_reason'] = $org['cancellationReason'] ?? null;

		if (isset($org['additionalStoreItemData'])) {
			$new['cancellation_date'] = $org['additionalStoreItemData']['cancellationDate'] ?? null;
			$new['cancellation_reason'] = $org['additionalStoreItemData']['cancellationReason'] ?? null;
			$new['expires_date'] = $org['additionalStoreItemData']['expiresDate'] ?? null;
			$new['original_purchase_date'] = $org['additionalStoreItemData']['originalPurchaseDate'] ?? null;
			$new['purchase_date'] = $org['additionalStoreItemData']['purchaseDate'] ?? null;
			
		}

		return $new;
	}

}
