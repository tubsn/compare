<?php

namespace app\importer;

class RetrescoAdapter
{

	function __construct() {

	}

	public function extract_article_data($rawJson) {

		$data = json_decode($rawJson, true);

		$info = [];
		$info['persons'] = $data['rtr_persons'] ?? null;
		$info['locations'] = $data['rtr_locations'] ?? null;
		$info['keywords'] = $data['rtr_keywords'] ?? null;
		$info['organisations'] = $data['rtr_organisations'] ?? null;
		$info['products'] = $data['rtr_products'] ?? null;
		$info['events'] = $data['rtr_events'] ?? null;

		return $info;

	}

	public function extract_topic_pages($rawJson) {

		$data = json_decode($rawJson, true);
		$topics = $data['docs'];
		return ['dossiers' => $topics];

	}


}
