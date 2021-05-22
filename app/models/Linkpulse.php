<?php

namespace app\models;
use	\app\importer\LinkpulseImport;

class Linkpulse
{

	function __construct() {
		$this->api = new LinkpulseImport();
	}

	public function stats_today($id) {
		return $this->api->article_today($id);
	}

	public function subscribers($id, $pubDate) {
		return $this->api->subscribers($id, $pubDate);
	}

}
