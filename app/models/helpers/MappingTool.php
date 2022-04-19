<?php

namespace app\models\helpers;

class MappingTool
{

	public $defaultCategory = 'Unknown';

	public function referer($input) {
		$out['referer_source'] = $this->map($input, $this->source_mapping_table());
		$out['referer_source_grouped'] = $this->map($out['referer_source'], $this->source_grouped_mapping_table());
		return $out;
	}


	public function referer_overview(array $data) {
		$sources = $this->referer_multiple($data);

		$out = [];
		foreach ($sources as $key => $value) {
			if (@!is_array($out[$value['referer_source_grouped']])) {$out[$value['referer_source_grouped']] = [];}
			array_push($out[$value['referer_source_grouped']], $key);
		}
		return $out;
	}


	public function referer_multiple(array $sources) {

		$out = [];
		foreach ($sources as $input) {
			$out[$input]['referer_source'] = $this->map($input, $this->source_mapping_table());
			$out[$input]['referer_source_grouped'] = $this->map($out[$input]['referer_source'], $this->source_grouped_mapping_table());
		}
		return $out;
	}


	private function map($input, $table) {

		$input = strTolower($input);
		foreach ($table as $category => $sources) {
			if (in_array($input, $sources)) {return $category;}
		}
		return $this->defaultCategory;
	}


	public function list_source_grouped_table() {
		return array_keys($this->source_grouped_mapping_table());
	}

	private function source_grouped_mapping_table() {

		$combined['Email'] = ['newsletter'];
		$combined['Push'] = ['push'];

		$combined['Social'] = [
			'facebook',
			'instagram',
			'twitter',
			'social-other',
		];

		$combined['Search'] = [
			'search-google',
			'search-other',
			'amp',
		];

		$combined['Direct'] = [
			'direct',
			'intern-tests',
			'plenigo',
			'outbrain',
			'aboshop',
		];

		$combined['Eigenanzeigen'] = [
			'intern-werbung',
		];

		$combined['Referral'] = [
			'unknown',
			'nachrichtenseiten',
			'discover',
			'newsfeeds',
			'sportseiten',
			'wikis',
			'google news',
		];

		return $combined;
	}

	public function list_sources_table() {
		return array_keys($this->source_mapping_table());
	}

	private function source_mapping_table() {

		$sources['Facebook'] = [
			'facebook.com',
			'facebook',
			'm.facebook.com',
			'lm.facebook.com',
			'l.facebook.com',
			'social media',
			'social',
		];

		$sources['Instagram'] = [
			'l.instagram.com',
			'instagram.com',
			'instagram',
		];

		$sources['Google News'] = [
			'news.google.com',
			'gnews',
			'newsshowcase',
			'googleapis.com',
		];

		$sources['Discover'] = [
			'discover',
		];

		$sources['Nachrichtenseiten'] = [
			't-online.de',
			'm.bild.de',
			'rbb24.de',
			'lausitzer-woche.de',
			'spreewald-nachrichten.de',
		];

		$sources['Newsfeeds'] = [
			'e.stry.tl',
			'startpage.com',
			'cdn-af.feednews.com',
			'app.talkwalker.com',
			'm.newslocker.com',
			'dailyadvent.com',
			'newsbreakapp.com',
			'swipe',
			'quickaccess.internet.apps.samsung.com',
		];

		$sources['Twitter'] = [
			't.co',
			'twitter',
			'twitter.com',
		];

		$sources['Sportseiten'] = [
			'loewenfrankfurt-playground.de',
			'lokfalkenberg.de',
			'es-weisswasser.de',
			'transfermarkt.de',
			'forum.es-weisswasser.de',
			'fupa.net',
			'reitturniere.de',
		];

		$sources['Social-Other'] = [
			'linkedin.com',
			'transition.meltwater.com',
			'stepstone.de',
			'app.asana.com',
		];

		$sources['Wikis'] = [
			'de.m.wikipedia.org',
			'de.wikipedia.org',
		];

		$sources['AMP'] = [
			'ampproject.org',
			'www-moz-de.cdn.ampproject.org',
			'amp-newslocker-com.cdn.ampproject.org',
			'www-lr--online-de.cdn.ampproject.org',
		];

		$sources['Search-Google'] = [
			'google',
			'instant',
		];

		$sources['Direct'] = [
			'(direct)',
			'(none)',
		];

		$sources['Intern-Werbung'] = [
			'lr',
			'lr-online',
			'lronline',
			'print',
			'display',
			'edgead',
			'edge',
			'edge ad',
			'start-banner',
			'dossierbox',
		];

		$sources['Outbrain'] = [
			'outbrain.com',
			'traffic.outbrain.com',
		];

		$sources['Intern-Tests'] = [
			'redmine.moz.de',
		];

		$sources['Aboshop'] = [
			'abo.lr-digital.de',
		];

		$sources['Plenigo'] = [
			'checkout.plenigo.com',
		];

		$sources['Push'] = [
			'browser',
			'push-notification',
		];

		$sources['Search-Other'] = [
			'bing',
			'ecosia.org',
			'duckduckgo',
			'yahoo',
			'de.search.yahoo.com',
			'qwant.com',
			'suche.aol.de',
			'suche.t-online.de',
			'flipboard.com',
			'namenfinden.de',
		];

		$sources['Newsletter'] = [
			'email',
			'deref-web.de',
			'deref-gmx.net',
			'email.t-online.de',
			'm-email.t-online.de',
			'mobilemailer-bs.web.de',
			'webmail.freenet.de',
			'mail.google.com',
			'vodafonemail.de',
			'mobilemailer-bap.web.de',
			'mobilemailer-bap.gmx.net',
			'mobilemailer-bs.gmx.net',
			'my.mail.de',
			'deref-1und1.de',
			'byom.de',
			'newsletter',
		];

		return $sources;
	}

}
