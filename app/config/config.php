<?php

$typen = file_get_contents(CONFIGPATH . '/artikel_typen.txt');
$typen = explode_and_trim("\n", $typen);

define('ARTICLE_TYPES', $typen);

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
]);

define('TIMEFRAMES', [
	'alle Daten',
	'aktuelle Woche',
	'letzte Woche',
	'vorletzte Woche',
	'aktueller Monat',
	'letzter Monat',
	'vorletzter Monat',
]);

define('PLENIGO_CUSTOMER_ID', '***REMOVED***');
define('PLENIGO_SECRET', '***REMOVED***');
