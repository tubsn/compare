<?php

$typen = file_get_contents(CONFIGPATH . '/artikel_typen.txt');
$typen = explode_and_trim("\n", $typen);

$tags = file_get_contents(CONFIGPATH . '/artikel_tags.txt');
$tags = explode_and_trim("\n", $tags);

define('ARTICLE_TYPES', $typen);
define('ARTICLE_TAGS', $tags);

define('PORTAL_URL', 'https://www.lr-online.de');
define('PORTAL_NAME', 'LR');

define('MAX_IMPORT_RANGE', 365);

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

define('TIMEFRAMES', [
	'heute',
	'gestern',
	'letzte 7 Tage',
	'aktuelle Woche',
	'letzte Woche',
	'vorletzte Woche',
	'aktueller Monat',
	'letzter Monat',
	'vorletzter Monat',
	'letzte 3 Monate',
	'alle Daten',
]);
