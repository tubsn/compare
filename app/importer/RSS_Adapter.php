<?php

namespace app\importer;

class RSS_Adapter
{

	function __construct() {

	}

	public function convert($data) {

		$xml = simplexml_load_string($data);

		$items = $xml->channel->xpath('item');
		$articles = array_map([$this,'map_article_fields'], $items);

		return $articles;

	}

	public function convert_news_markup($data, $url) {

		$xml = simplexml_load_string($data);

		if (empty($xml)) {
			throw new \Exception("Artikel konnte nicht Importiert werden", 404);
		}

		$components = $xml->NewsItem->NewsComponent;

		$article['id'] = $this->extract_id($url);
		$article['ressort'] = $this->extract_ressort($url);
		//$newsMLRessort = $components[0]->DescriptiveMetadata->xpath('Property[@FormalName="Department"]')[0]['Value']->__toString();

		$article['title'] = $components[0]->NewsLines->HeadLine->__toString();
		$article['kicker'] = null; // Not available in Detail RSS :/
		$article['description'] = $components[0]->NewsLines->SubHeadLine->__toString();
		$article['author'] = $components[0]->AdministrativeMetadata->xpath('Property[@FormalName="Author"]')[0]['Value']->__toString();
		$article['plus'] = $xml->NewsItem->NewsManagement->accessRights->__toString()  == 'available to subscribers only' ? true : false;

		$timestamp = $xml->NewsItem->Identification->NewsIdentifier->DateId->__toString();
		$article['pubdate'] = date('Y-m-d H:i:s', strtotime($timestamp));

		if (isset($xml->NewsItem->xpath('NewsComponent[@Duid="leadImage"]//NewsComponent')[0]->ContentItem['Href'])) {
			$article['image'] = $xml->NewsItem->xpath('NewsComponent[@Duid="leadImage"]//NewsComponent')[0]->ContentItem['Href']->__toString();
			$article['image'] = PORTAL_URL . $article['image'];
		}
		else {$article['image'] = null;}

		$article['link'] = substr($url,0,-9);;

		return $article;

	}

	private function map_article_fields($item) {

		$url = $item->link->__toString();

		$article['id'] = $this->extract_id($url);
		$article['ressort'] = $this->extract_ressort($url);
		$article['title'] = $item->title->__toString();
		$article['kicker'] = $item->kicker->__toString();

		$article['audience'] = $item->contenttype->__toString();
		if (empty($article['audience'])) {unset($article['audience']);}

		$article['description'] = $item->description->__toString();
		$article['author'] = $item->author->__toString();
		$article['plus'] = $item->freemium->__toString() == 'free' ? 0 : 1; // Converts Freemiuminfo to boolean
		$article['pubdate'] = date('Y-m-d H:i:s', strtotime($item->pubDate));
		$article['image'] = $this->get_image($item->enclosure['url']);
		$article['link'] = $url;

		return $article;

	}


	private function extract_id($url) {
		// Regex search for the ID = -8Digits.html
		$searchPattern = "/-(\d{8}).html/";
		preg_match($searchPattern, $url, $matches);
		return $matches[1] ?? NULL; // First Match should be the ID
	}

	private function extract_ressort($url) {

		$path = parse_url($url, PHP_URL_PATH);
		$path = trim ($path, '/');
		$paths = explode('/',$path);

		$paths = array_filter($paths, function($path) {
			return strpos($path,'.html') == false ;
		});

		if (!isset($paths[0])) {return 'unbekannt';}

		switch (PORTAL) {

			case 'LR':
				if ($paths[0] == 'lausitz') {return $paths[1];}
				if ($paths[0] == 'ratgeber' || $paths[0] == 'blaulicht') {return 'nachrichten';}
				if (isset($paths[1]) && $paths[1] == 'sport') {return $paths[1];}
				if (isset($paths[1]) && $paths[1] == 'kultur') {return $paths[1];}
			break;

			case 'MOZ':
				if ($paths[0] == 'lokales') {return $paths[1];}
				if ($paths[0] == 'nachrichten') {return $paths[1];}
				if (isset($paths[1]) && $paths[1] == 'sport') {return $paths[1];}
			break;

			case 'SWP':
				if ($paths[0] == 'lokales') {return $paths[1];}
				if ($paths[0] == 'sport') {return $paths[0];}
			break;

		}

		return $paths[0] ?? 'unbekannt';

	}

	private function get_image($enclosureURL) {
		// toString throws Fatal Error if cast on null
		if ($enclosureURL) {
			return $enclosureURL->__toString();
		}
		return null;
	}



}
