<?php

$output = fopen('php://output', 'w');

$header = [
	'ArtikelID',
	'Publikatonsdatum',
	'Plus',
	'Dachzeile',
	'Titel',
	'Teaser',
	'Teaserlaenge',
	'Ressort',
	'InhaltsTyp',
	'SubTyp',
	'Audience',
	'Userneed',
	'Autor',
	'Pageviews',
	'Sessions',
	'Conversions',
	'PlusLeser',
	'Mediatime',
	'AvgMediatime',
	'Impulse',
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
		$article['description'],
		strlen($article['description']),
		ucwords($article['ressort']),
		$article['type'],
		$article['tag'],
		$article['audience'],
		$article['userneed'],
		$article['author'],
		$article['pageviews'],
		$article['sessions'],
		$article['conversions'],
		$article['subscriberviews'],
		$article['mediatime'],
		$article['avgmediatime'],
		$article['buyintent'],
		$article['cancelled'],
		$article['retention_days'],
	], ';');
}

?>
