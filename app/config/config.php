<?php

switch (PORTAL) {

	case 'LR':

		define('PORTAL_URL', 'https://www.lr-online.de'); // used for Article Imports
		define('PORTAL_URL_SHORT', 'www.lr.de');
		define('PORTAL_EPAPER_URL', 'https://epaper.lr-online.de');
		define('PORTAL_NAME', 'LR');

		define('APP_PRODUCTS', [
			'ir.lronline.product.year',
			'ir.lrepaper.product.year',
			'ir.lrepaper.product.month',
			'ir.lrepaper.product.month',
			'ir.lrepaper.product.year',
			'ir.lronline.product.year',
		]);

		define('RESSORT_MAPPING', [
			'cottbus' => 'cos',
			'elsterwerda' => 'els',
			'energie-cottbus' => 'fce',
			'finsterwalde' => 'fin',
			'forst' => 'for',
			'guben' => 'gub',
			'herzberg' => 'her',
			'hoyerswerda' => 'hoy',
			'luckau' => 'luc',
			'luebben' => 'lue',
			'luebbenau' => 'cal',
			'nachrichten' => 'nac',
			'senftenberg' => 'sfb',
			'sport' => 'spo',
			'spremberg' => 'spr',
			'weisswasser' => 'wwr',
			'kultur' => 'kul',
		]);

		define('RESSORT_AUDIENCE_REQUIREMENTS', [
		/*	'cottbus' => 3,
			'elsterwerda' => 3,
			'energie-cottbus' => 3,
			'finsterwalde' => 3,
			'forst' => 3,
			'guben' => 3,
			'herzberg' => 3,
			'hoyerswerda' => 3,
			'luckau' => 3,
			'luebben' => 3,
			'luebbenau' => 3,
			'nachrichten' => 3,
			'senftenberg' => 3,
			'sport' => 3,
			'spremberg' => 3,
			'weisswasser' => 3,
			'kultur' => 3,
		*/
		]);		

		define('IMPORT_FEEDS', [
			'https://www.lr-online.de/?_XML=RSS',
			'https://www.lr-online.de/energie-cottbus/?_XML=RSS',
			'https://www.lr-online.de/nachrichten/sport/?_XML=RSS',
			'https://www.lr-online.de/lausitz/cottbus/?_XML=RSS',
			'https://www.lr-online.de/lausitz/senftenberg/?_XML=RSS',
			'https://www.lr-online.de/lausitz/hoyerswerda/?_XML=RSS',
			'https://www.lr-online.de/lausitz/weisswasser/?_XML=RSS',
			'https://www.lr-online.de/lausitz/spremberg/?_XML=RSS',
			'https://www.lr-online.de/lausitz/forst/?_XML=RSS',
			'https://www.lr-online.de/lausitz/guben/?_XML=RSS',
			'https://www.lr-online.de/lausitz/elsterwerda/?_XML=RSS',
			'https://www.lr-online.de/lausitz/finsterwalde/?_XML=RSS',
			'https://www.lr-online.de/lausitz/herzberg/?_XML=RSS',
			'https://www.lr-online.de/lausitz/luebben/?_XML=RSS',
			'https://www.lr-online.de/lausitz/luebbenau/?_XML=RSS',
			'https://www.lr-online.de/lausitz/luckau/?_XML=RSS',
			'https://www.lr-online.de/nachrichten/brandenburg/?_XML=RSS',
			'https://www.lr-online.de/nachrichten/polen/?_XML=RSS',
			'https://www.lr-online.de/nachrichten/wirtschaft/?_XML=RSS',
			'https://www.lr-online.de/nachrichten/sachsen/?_XML=RSS',
			'https://www.lr-online.de/nachrichten/kultur/?_XML=RSS',
		]);
	break;

	case 'MOZ':

		define('PORTAL_URL', 'https://www.moz.de');
		define('PORTAL_URL_SHORT', 'www.moz.de');
		define('PORTAL_EPAPER_URL', 'https://epaper.moz.de');
		define('PORTAL_NAME', 'MOZ');

		define('RESSORT_MAPPING', [
			'angermuende' => 'ang',
			'bad-belzig' => 'bel',
			'bad-freienwalde' => 'bfw',
			'beeskow' => 'bee',
			'berlin' => 'bln',
			'bernau' => 'ber',
			'bilder' => 'bil',
			'brandenburg' => 'bra',
			'brandenburg-havel' => 'bhv',
			'eberswalde' => 'ebw',
			'eisenhuettenstadt' => 'ehs',
			'erkner' => 'erk',
			'falkensee' => 'fal',
			'frankfurt-oder' => 'ffo',
			'fuerstenwalde' => 'fue',
			'gransee' => 'gra',
			'hennigsdorf' => 'hen',
			'kultur' => 'kul',
			'neuruppin' => 'neu',
			'oranienburg' => 'ora',
			'panorama' => 'pan',
			'politik' => 'pol',
			'rathenow' => 'rat',
			'schwedt' => 'scw',
			'seelow' => 'see',
			'sport' => 'spo',
			'strausberg' => 'str',
			'wirtschaft' => 'wir',
			'seelow+bad-freienwalde' => 'see/frw',
			'brandenburg-kombiniert' => 'brb',
		]);

		define('RESSORT_AUDIENCE_REQUIREMENTS', [
			'angermuende' => [
				'Deutsch-Polen' => 1,
				'Familien' => 1,
				'Häuslebauer' => 1,
				'Industriebeschäftigte UM' => 1,
				'Pendler' => 1,
				'Default' => null,
			],
			'beeskow' => [
				'Familien' => 2,
				'Foodies' => 1,
				'Häuslebauer' => 1,
				'Pendler' => 1,
				'Rettungsengel' => 1,
				'Default' => null,
			],
			'bernau' => [
				'Familien' => 2,
				'Foodies' => 1,
				'Häuslebauer' => 1,
				'Pendler' => 1,
				'Rettungsengel' => 1,
				'Tierfreunde' => 1,
				'Default' => null,
			],
			'brandenburg' => [
				'Crime-Fans' => 2,
				'Deutsch-Polen' => 3,
				'Familien' => 2,
				'Pendler' => 1,
				'Teslanians' => 3,
				'Default' => null,
			],
			'eberswalde' => [
				'Crime-Fans' => 1,
				'Familien' => 2,
				'Häuslebauer' => 2,
				'Pendler' => 1,
				'Rettungsengel' => 1,
				'Tierfreunde' => 1,
				'Default' => null,
			],
			'eisenhuettenstadt' => [
				'Deutsch-Polen' => 1,
				'Ekoianer' => 3,
				'Familien' => 3,
				'Foodies' => 1,
				'Pendler' => 2,
				'Tierfreunde' => 2,
				'Default' => null,
			],
			'erkner' => [
				'Familien' => 1,
				'Foodies' => 1,
				'Häuslebauer' => 1,
				'Pendler' => 1,
				'Teslanians' => 1,
				'Default' => null,
			],
			'frankfurt-oder' => [
				'Crime-Fans' => 1,
				'Deutsch-Polen' => 2,
				'Familien' => 2,
				'Foodies' => 1,
				'Häuslebauer' => 1,
				'Pendler' => 1,
				'Pflegende_Angehörige' => 1,
				'Rettungsengel' => 1,
				'Default' => null,
			],
			'fuerstenwalde' => [
				'Crime-Fans' => 1,
				'Familien' => 2,
				'Foodies' => 1,
				'Häuslebauer' => 1,
				'Pendler' => 1,
				'Rettungsengel' => 1,
				'Tierfreunde' => 1,
				'Default' => null,
			],
			'gransee' => [
				'Familien' => 2,
				'Foodies' => 1,
				'Häuslebauer' => 1,
				'Pendler' => 1,
				'Rettungsengel' => 1,
				'Tierfreunde' => 1,
				'Default' => null,
			],
			'hennigsdorf' => [
				'Familien' => 1,
				'Foodies' => 1,
				'Häuslebauer' => 1,
				'Pendler' => 1,
				'Rettungsengel' => 1,
				'Tierfreunde' => 1,
				'Default' => null,
			],
			'kultur' => [
				'Crime-Fans' => 1,
				'Familien' => 2,
				'Foodies' => 1,
				'Default' => null,
			],
			'neuruppin' => [
				'Crime-Fans' => 1,
				'Familien' => 2,
				'Foodies' => 1,
				'Häuslebauer' => 1,
				'Pendler' => 1,
				'Rettungsengel' => 1,
				'Default' => null,
			],
			'oranienburg' => [
				'Crime-Fans' => 1,
				'Familien' => 2,
				'Foodies' => 1,
				'Häuslebauer' => 1,
				'Pendler' => 1,
				'Rettungsengel' => 1,
				'Tierfreunde' => 1,
				'Default' => null,
			],
			'schwedt' => [
				'Familien' => 1,
				'Häuslebauer' => 1,
				'Industriebeschäftigte UM' => 2,
				'Pendler' => 1,
				'Rettungsengel' => 1,
				'Tierfreunde' => 1,
				'Default' => null,
			],
			'sport' => [
				'Amateurfußball' => 6,
				'Unioner' => 4,
				'Default' => null,
			],
			'strausberg' => [
				'Familien' => 2,
				'Foodies' => 1,
				'Häuslebauer' => 1,
				'Pendler' => 1,
				'Rettungsengel' => 1,
				'Tierfreunde' => 1,
				'Default' => null,
			],
			'seelow+bad-freienwalde' => [
				'Deutsch-Polen' => 1,
				'Familien' => 2,
				'Foodies' => 2,
				'Häuslebauer' => 1,
				'Pendler' => 2,
				'Rettungsengel' => 1,
				'Tierfreunde' => 1,
				'Default' => null,
			],
			'brandenburg-kombiniert' => [
				'Crime-Fans' => 2,
				'Deutsch-Polen' => 3,
				'Familien' => 2,
				'Pendler' => 1,
				'Teslanians' => 3,
				'Default' => null,
			],
		]);


		define('IMPORT_FEEDS', [
			'https://www.moz.de/?_XML=RSS',
			'https://www.moz.de/nachrichten/?_XML=RSS',
		    'https://www.moz.de/nachrichten/politik/?_XML=RSS',
		    'https://www.moz.de/nachrichten/berlin/?_XML=RSS',
		    'https://www.moz.de/nachrichten/brandenburg/?_XML=RSS',
		    'https://www.moz.de/nachrichten/wirtschaft/?_XML=RSS',
		    'https://www.moz.de/nachrichten/kultur/?_XML=RSS',
		    'https://www.moz.de/nachrichten/panorama/?_XML=RSS',
		    'https://www.moz.de/nachrichten/sport/?_XML=RSS',
		    'https://www.moz.de/lokales/angermuende/?_XML=RSS',
		    'https://www.moz.de/lokales/bad-belzig/?_XML=RSS',
		    'https://www.moz.de/lokales/bad-freienwalde/?_XML=RSS',
		    'https://www.moz.de/lokales/beeskow/?_XML=RSS',
		    'https://www.moz.de/lokales/bernau/?_XML=RSS',
		    'https://www.moz.de/lokales/brandenburg-havel/?_XML=RSS',
		    'https://www.moz.de/lokales/eberswalde/?_XML=RSS',
		    'https://www.moz.de/lokales/eisenhuettenstadt/?_XML=RSS',
		    'https://www.moz.de/lokales/erkner/?_XML=RSS',
		    'https://www.moz.de/lokales/falkensee/?_XML=RSS',
		    'https://www.moz.de/lokales/frankfurt-oder/?_XML=RSS',
		    'https://www.moz.de/lokales/fuerstenwalde/?_XML=RSS',
		    'https://www.moz.de/lokales/gransee/?_XML=RSS',
		    'https://www.moz.de/lokales/hennigsdorf/?_XML=RSS',
		    'https://www.moz.de/lokales/neuruppin/?_XML=RSS',
		    'https://www.moz.de/lokales/oranienburg/?_XML=RSS',
		    'https://www.moz.de/lokales/rathenow/?_XML=RSS',
		    'https://www.moz.de/lokales/schwedt/?_XML=RSS',
		    'https://www.moz.de/lokales/seelow/?_XML=RSS',
		    'https://www.moz.de/lokales/strausberg/?_XML=RSS',
		]);
	break;

	case 'SWP':

		define('PORTAL_URL', 'https://www.swp.de');
		define('PORTAL_URL_SHORT', 'www.swp.de');
		define('PORTAL_EPAPER_URL', 'https://epaper.swp.de');
		define('PORTAL_NAME', 'SWP');

		define('IMPORT_FEEDS', [
			//'https://www.swp.de/?_XML=RSS',
			'https://www.swp.de/lokales/?_XML=RSS',
			'https://www.swp.de/lokales/ulm/?_XML=RSS',
			'https://www.swp.de/lokales/neu-ulm/?_XML=RSS',
			'https://www.swp.de/lokales/goeppingen/?_XML=RSS',
			'https://www.swp.de/lokales/geislingen/?_XML=RSS',
			'https://www.swp.de/lokales/schwaebisch-hall/?_XML=RSS',
			'https://www.swp.de/lokales/crailsheim/?_XML=RSS',
			'https://www.swp.de/lokales/thema/gaildorf?_XML=RSS',
			'https://www.swp.de/lokales/ehingen/?_XML=RSS',
			'https://www.swp.de/lokales/metzingen/?_XML=RSS',
			'https://www.swp.de/lokales/reutlingen/?_XML=RSS',
			'https://www.swp.de/lokales/muensingen/?_XML=RSS',
			'https://www.swp.de/lokales/hechingen/?_XML=RSS',
			'https://www.swp.de/sport/?_XML=RSS',
			'https://www.swp.de/sport/fussball-lokal/?_XML=RSS',
			'https://www.swp.de/sport/mehr-lokalsport/?_XML=RSS',
			'https://www.swp.de/politik/?_XML=RSS',
			'https://www.swp.de/wirtschaft/?_XML=RSS',
		]);
	break;
}

$typen = file_get_contents(CONFIGPATH . PORTAL .DIRECTORY_SEPARATOR . 'artikel_typen.txt');
$typen = explode_and_trim("\n", $typen);
define('ARTICLE_TYPES', $typen);

$tags = file_get_contents(CONFIGPATH . PORTAL .DIRECTORY_SEPARATOR . 'artikel_tags.txt');
$tags = explode_and_trim("\n", $tags);
define('ARTICLE_TAGS', $tags);

$audiences = file_get_contents(CONFIGPATH . PORTAL .DIRECTORY_SEPARATOR . 'artikel_audiences.txt');
$audiences = explode_and_trim("\n", $audiences);
define('ARTICLE_AUDIENCES', $audiences);

define('MAX_IMPORT_RANGE', 365);

define('TAGESNAMEN', ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag']);

define('TIMEFRAMES', [
	'heute',
	'gestern',
	'letzte 7 Tage',
	'letzte 30 Tage',
	'letzte 365 Tage',	
	'aktueller Monat',
	'letzter Monat',
	'vorletzter Monat',
	'letzte 3 Monate',
	'aktuelles Jahr',
	'letztes Jahr',
	'alle Daten',
]);

define('DEFAULT_FROM', 'yesterday -29 days');
define('DEFAULT_TO', 'yesterday');

define('CANCELLATION_REASON', [
	0 => null,
	1 => 'zu teuer',
	2 => 'keine interessanten Inhalte',
	3 => 'technisches Problem',
	4 => 'keine Angabe',
	5 => 'besseres Angebot gefunden',
	6 => 'fehlende Funktion',
	7 => 'Kündigung mit Widerrufsrecht',
	8 => 'interne Korrektur',
]);
