<?php

$output = fopen('php://output', 'w');

$header = [
	'order_id',
	'customer_id',
	'external_customer_id',

	'article_id',
	'article_pubdate',
	'article_kicker',
	'article_title',
	'article_ressort',
	'article_type',
	'article_tag',
	'article_author',

	'subscription_start_date',
	'subscription_cancellation_date',
	'subscription_end_date',

	'cancelled',
	'retention_days',

	'order_payment_method',
	'customer_gender',
	'ga_source',
	'ga_city',
	'customer_city',
	'ga_sessions',

	'subscription_product_id',
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
		$conversion['order_id'],
		$conversion['customer_id'],
		$conversion['external_customer_id'],

		$conversion['article_id'],
		$conversion['article_pubdate'],
		$conversion['article_kicker'],
		$conversion['article_title'],
		$conversion['article_ressort'],
		$conversion['article_type'],
		$conversion['article_tag'],
		$conversion['article_author'],

		$conversion['subscription_start_date'],
		$conversion['subscription_cancellation_date'],
		$conversion['subscription_end_date'],

		$conversion['cancelled'],
		$conversion['retention'],

		$conversion['order_payment_method'],
		$conversion['customer_gender'],
		$conversion['ga_source'],
		$conversion['ga_city'],
		$conversion['customer_city'],
		$conversion['ga_sessions'],

		$conversion['subscription_product_id'],
		$conversion['order_title'],
		$conversion['order_price'],

		$conversion['subscription_title'],
		$conversion['subscription_price'],

		$conversion['subscription_count'],

	], ';');
}

?>
