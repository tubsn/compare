<?php

$output = fopen('php://output', 'w');

$header = [
	'customer_id',
	'external_customer_id',


	'subscription_start_date',
	'subscription_cancellation_date',
	'subscription_end_date',

	'cancelled',
	'retention_days',

	'subscription_payment_method',
	'customer_gender',
	'customer_city',

	'order_product_id',
	'order_title',
	'order_price',
	'subscription_title',
	'subscription_price',
	'subscription_count',
];

// First Line of xls
fputcsv($output, $header, ';');

foreach ($conversions as $conversion) {

	fputcsv($output,[
		$conversion['customer_id'],
		$conversion['external_customer_id'],

		$conversion['subscription_start_date'],
		$conversion['subscription_cancellation_date'],
		$conversion['subscription_end_date'],

		$conversion['cancelled'],
		$conversion['retention'],

		$conversion['subscription_payment_method'],
		$conversion['customer_gender'],
		$conversion['customer_city'],

		$conversion['order_product_id'],
		$conversion['order_title'],
		$conversion['order_price'],

		$conversion['subscription_title'],
		$conversion['subscription_price'],

		$conversion['subscription_count'],

	], ';');
}

?>
