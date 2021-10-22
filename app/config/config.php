<?php

switch (PORTAL) {

	case 'LR':

		define('PORTAL_URL', 'https://www.lr-online.de');
		define('PORTAL_NAME', 'LR');

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
			'https://www.swp.de/?_XML=RSS',
			'https://www.swp.de/suedwesten/staedte/crailsheim/?_XML=RSS',
			'https://www.swp.de/suedwesten/staedte/ehingen/?_XML=RSS',
			'https://www.swp.de/suedwesten/staedte/gaildorf/?_XML=RSS',
			'https://www.swp.de/suedwesten/staedte/geislingen/?_XML=RSS',
			'https://www.swp.de/suedwesten/staedte/goeppingen/?_XML=RSS',
			'https://www.swp.de/suedwesten/staedte/hechingen/?_XML=RSS',
			'https://www.swp.de/suedwesten/staedte/heidenheim/?_XML=RSS',
			'https://www.swp.de/suedwesten/staedte/metzingen/?_XML=RSS',
			'https://www.swp.de/suedwesten/staedte/muensingen/?_XML=RSS',
			'https://www.swp.de/suedwesten/staedte/neu-ulm/?_XML=RSS',
			'https://www.swp.de/suedwesten/staedte/reutlingen/?_XML=RSS',
			'https://www.swp.de/suedwesten/staedte/schwaebisch-hall/?_XML=RSS',
			'https://www.swp.de/suedwesten/staedte/stuttgart/?_XML=RSS',
			'https://www.swp.de/suedwesten/staedte/ulm/?_XML=RSS',
			'https://www.swp.de/suedwesten/landkreise/alb-donau/?_XML=RSS',
			'https://www.swp.de/suedwesten/landkreise/lk-goeppingen/?_XML=RSS',
			'https://www.swp.de/suedwesten/landkreise/lk-heidenheim/?_XML=RSS',
			'https://www.swp.de/suedwesten/landkreise/kreis-neu-ulm-bayern/?_XML=RSS',
			'https://www.swp.de/suedwesten/landkreise/lk-ludwigsburg/?_XML=RSS',
			'https://www.swp.de/suedwesten/landkreise/lk-reutlingen/?_XML=RSS',
			'https://www.swp.de/suedwesten/landkreise/lk-schwaebisch-hall/?_XML=RSS',
			'https://www.swp.de/suedwesten/landkreise/zollernalb/?_XML=RSS',
			'https://www.swp.de/blaulicht/crailsheim/?_XML=RSS',
			'https://www.swp.de/blaulicht/ehingen/?_XML=RSS',
			'https://www.swp.de/blaulicht/gaildorf/?_XML=RSS',
			'https://www.swp.de/blaulicht/goeppingen-geislingen/?_XML=RSS',
			'https://www.swp.de/blaulicht/hechingen/?_XML=RSS',
			'https://www.swp.de/blaulicht/heidenheim/?_XML=RSS',
			'https://www.swp.de/blaulicht/metzingen/?_XML=RSS',
			'https://www.swp.de/blaulicht/muensingen/?_XML=RSS',
			'https://www.swp.de/blaulicht/reutlingen/?_XML=RSS',
			'https://www.swp.de/blaulicht/schwaebisch-hall/?_XML=RSS',
			'https://www.swp.de/blaulicht/ulm-neu-ulm/?_XML=RSS',
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
