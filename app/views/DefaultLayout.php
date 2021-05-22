<?php

namespace app\views;
use \flundr\mvc\views\htmlView;

class DefaultLayout extends htmlView {

	// Page Header Information is available in the Templates
	// as a $page Array. It can be accessed via $page['title']

	public $title = 'Content DB';
	public $description = 'Datenbank für publizierte Artikel';
	public $css = ['/styles/css/defaults.css', '/styles/css/main.css'];
	public $fonts = 'https://fonts.googleapis.com/css?family=Fira+Sans:400,400i,600|Fira+Sans+Condensed:400,600';
	public $js = '/styles/js/main.js';
	public $framework = ['https://cdn.jsdelivr.net/npm/apexcharts', '/styles/js/fl-dialog.js'];
	public $meta = [
		'author' => 'flundr',
		'robots' => 'index, follow',
		'favicon' => '/styles/img/datenkrake.svg',
	];

	public $templateVars = ['info' => null];

	public $templates = [
		'tinyhead' => 'layout/html-doc-header',
		'header' => 'navigation/main-menu',
		'main' => null,
		'footer' => null,
		'tinyfoot' => 'layout/html-doc-footer',
	];

}
