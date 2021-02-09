<?php

namespace app\views;

class CardLayout extends DefaultLayout {

	// Page Header Information is available in the Templates
	// as a $page Array. It can be accessed via $page['title']

	public $title = 'LR Content DB';
	public $description = 'Datenbank für publizierte LR Artikel';

	public $templates = [
		'tinyhead' => 'layout/html-doc-header',
		'header' => null,
		'main' => null,
		'footer' => null,
		'tinyfoot' => 'layout/html-doc-footer',
	];

}
