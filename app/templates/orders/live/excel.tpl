<?php 

$output = fopen('php://output', 'w');

// Header Fields
// Warning Fieldname 'ID' doesn´t work as First field!!!!!!! Strange Bug
fputcsv($output, [
	'OrderID',
	'OrderDate',
	'Produkt',
	'Preis',
	'Bezahlart',
	'SourceURL',
	'ArtikelID',
	'Ressort',
	'Gekündigt',
	'Haltedauer',
	'Kündigungsdatum',
	'UserID',
	'Stadt',
	'Geschlecht',
], ';');

foreach ($orders as $order) {
	fputcsv($output, [
		$order['order_id'],
		$order['order_date'],
		$order['order_title'],
		$order['order_price'],
		$order['order_payment_method'],
		$order['order_source'] ?? '',
		$order['article_id'] ?? '',
		$order['article_ressort'] ?? '',
		$order['cancelled'],
		$order['retention'],
		$order['subscription_cancellation_date'],
		$order['customer_id'],
		$order['customer_city'],
		$order['customer_gender'],

	], ';');
}
