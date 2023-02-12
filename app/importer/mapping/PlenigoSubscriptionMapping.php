<?php

namespace app\importer\mapping;
use app\models\Subscriptions;
use app\models\Orders;

class PlenigoSubscriptionMapping
{

	function __construct() {
		$this->Subscriptions = new Subscriptions();
		$this->Orders = new Orders();
	}

	public function map_multiple_subscriptions($org) {

		$order = $this->Subscriptions->get($org['subscriptionId']);
		if (empty($order)) {$order = $this->Subscriptions->get($org['precursorId']);}
		if (empty($order)) {$order = $this->Orders->get($org['subscriptionId']);}
		if (empty($order)) {$order = $this->Orders->get($org['precursorId']);}
		$new['order_id'] = $order['order_id'] ?? null;

		$new['subscription_id'] = $org['subscriptionId'];
		$new['subscription_start_date'] = date("Y-m-d H:i:s", strtotime($org['startDate']));

		$new['subscription_cancellation_date'] = null;
		if ($org['cancellationDate']) {
			$new['subscription_cancellation_date'] = date("Y-m-d H:i:s", strtotime($org['cancellationDate']));
		}

		$new['subscription_end_date'] = null;
		if ($org['endDate']) {
			$new['subscription_end_date'] = date("Y-m-d H:i:s", strtotime($org['endDate']));
		}

		$new['subscription_status'] = $org['status'];
		$new['cancelled'] = $new['subscription_cancellation_date'] ? 1 : null;
		$new['cancellation_reason'] = intval($org['cancellationReasonUniqueId'] ?? null);
		
		$new['subscription_title'] = $org['items'][0]['title'];
		$new['subscription_internal_title'] = $org['items'][0]['internalTitle'];
		$new['subscription_product_id'] = $org['items'][0]['plenigoProductId'];
		$new['subscription_price'] = $org['items'][0]['price'];
		
		return $new;

	}


}
