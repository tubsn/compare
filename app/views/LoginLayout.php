<?php

namespace app\views;
use \flundr\mvc\views\htmlView;

class LoginLayout extends htmlView {

	// Page Header Information is available in the Templates
	// as a $page Array. It can be accessed via $page['title']

	public $title = 'Login Bereich';
	public $description = '';
	public $css = ['/styles/css/defaults.css', '/styles/css/login.css'];
	public $fonts = 'https://fonts.googleapis.com/css?family=Fira+Sans:400,400i,600|Fira+Sans+Condensed:400,600';
	public $js = '/styles/js/main.js';
	public $framework = '';
	public $meta = [
		'author' => 'flundr',
		'robots' => 'noindex, nofollow',
		'favicon' => '/styles/img/flundr/flundr-logo.svg',
	];

	public $templates = [
		'tinyhead' => 'layout/html-doc-header',
		'header' => null,
		'main' => null,
		'footer' => null,
		'tinyfoot' => 'layout/html-doc-footer',
	];

}
