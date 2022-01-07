<?php

switch (PORTAL) {

	case 'LR':

		define('PORTAL_URL', 'https://www.lr-online.de');
		define('PORTAL_NAME', 'LR');

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
			'eisenhuettenstadt' => 'eis',
			'erkner' => 'erk',
			'falkensee' => 'fal',
			'frankfurt-oder' => 'fra',
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
	'aktuelle Woche',
	'letzte Woche',
	'vorletzte Woche',
	'aktueller Monat',
	'letzter Monat',
	'vorletzter Monat',
	'letzte 3 Monate',
	'alle Daten',
]);
