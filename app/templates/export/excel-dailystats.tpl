<?php

$output = fopen('php://output', 'w');

$header = [
	'ArtikelID',
	'Datum',
	'Pageviews',
	'Sessions',
	'Conversions',
	'Ressort',
	'InhaltsTyp',
	'Autor',
];

// First Line of xls
fputcsv($output, $header, ';');

foreach ($articles as $article) {

	fputcsv($output,[
		$article['id'],
		$article['date'],
		$article['pageviews'],
		$article['sessions'],
		$article['conversions'],
		ucwords($article['ressort']),
		$article['type'],
		$article['author'],
	], ';');
}

?>