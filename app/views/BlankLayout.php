<?php

namespace app\views;
use \flundr\mvc\views\htmlView;

class BlankLayout extends DefaultLayout {


	public $templates = [
		'tinyhead' => 'layout/html-doc-header',
		'header' => null,
		'main' => null,
		'footer' => null,
		'tinyfoot' => 'layout/html-doc-footer',
	];

}
