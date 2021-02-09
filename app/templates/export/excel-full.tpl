<?php

$output = fopen('php://output', 'w');

$header = [
	'ArtikelID',
	'Publikatonsdatum',
	'Plus',
	'Dachzeile',
	'Titel',
	'Ressort',
	'InhaltsTyp',
	'Autor',
	'Pageviews',
	'Sessions',
	'Conversions',
	'Kündiger',
	'Haltedauer'
];

// First Line of xls
fputcsv($output, $header, ';');

foreach ($articles as $article) {

	fputcsv($output,[
		$article['id'],
		$article['pubdate'],
		$article['plus'],
		$article['kicker'],
		$article['title'],
		ucwords($article['ressort']),
		$article['type'],
		$article['author'],
		$article['pageviews'],
		$article['sessions'],
		$article['conversions'],
		$article['cancelled'],
		$article['retention_days'],
	], ';');
}

?>