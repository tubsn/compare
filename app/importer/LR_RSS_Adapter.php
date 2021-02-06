<?php

namespace app\importer;

class LR_RSS_Adapter
{

	function __construct() {

	}

	public function convert($xml) {

		$items = $xml->channel->xpath('item');
		$articles = array_map([$this,'map_article_fields'], $items);

		return $articles;

	}

	private function map_article_fields($item) {

		$url = $item->link->__toString();

		$article['id'] = $this->extract_id($url);
		$article['ressort'] = $this->extract_ressort($url);
		$article['title'] = $item->title->__toString();
		$article['kicker'] = $item->kicker->__toString();
		$article['description'] = $item->description->__toString();
		$article['author'] = $item->author->__toString();
		$article['plus'] = $item->freemium->__toString() == 'free' ? false : true; //Converts Freemiuminfo to boolean
		$article['pubdate'] = date('Y-m-d H:i:s', strtotime($item->pubDate));
		$article['image'] = $this->get_image($item->enclosure['url']);
		$article['link'] = $url;

		return $article;

	}


	private function extract_id($url) {
		// Regex search for the ID = -8Digits.html
		$searchPattern = "/-(\d{8}).html/";
		preg_match($searchPattern, $url, $matches);
		return $matches[1]; // First Match should be the ID
	}

	private function extract_ressort($url) {

		$path = parse_url($url, PHP_URL_PATH);
		$path = trim ($path, '/');
		$paths = explode('/',$path);

		$paths = array_filter($paths, function($path) {
			return strpos($path,'.html') == false ;
		});

		if ($paths[0] == 'lausitz') {
			return $paths[1];
		}

		if (isset($paths[1]) && $paths[1] == 'sport') {
			return $paths[1];
		}

		return $paths[0];

	}

	private function get_image($enclosureURL) {
		// toString throws Fatal Error if cast on null
		if ($enclosureURL) {
			return $enclosureURL->__toString();
		}
		return null;
	}



}
