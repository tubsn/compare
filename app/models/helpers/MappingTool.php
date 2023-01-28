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

		// Filtering all the AMP Variants
		if(strpos($input, 'ampproject') !== false) {return 'AMP';}

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

		$combined['Discover/News'] = [
			'discover',
			'google news',
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
			'newsfeeds',
			'vereine',
			'wikis',
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
			'de-de.facebook.com',
			'lm.facebook.com',
			'l.facebook.com',
			'social media',
			'social',
			'fb_insta_ad',
		];

		$sources['Instagram'] = [
			'l.instagram.com',
			'instagram.com',
			'instagram',
			'instagram_story_red',
		];

		$sources['Google News'] = [
			'news.google.com',
			'news.url.google.com',
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
			'tagblatt.de',
			'schwaebischhall-online.de',
			't-online.de',
			'web.de',
			'spreewald-nachrichten.de',
		];

		$sources['Newsfeeds'] = [
			'e.stry.tl',
			'startpage.com',
			'cdn-af.feednews.com',
			'app.talkwalker.com',
			'app.meltwater.com',
			'm.newslocker.com',
			'dailyadvent.com',
			'break.ma',
			'news.upday.com',
			'newsbreakapp.com',
			'swipe',
			'quickaccess.internet.apps.samsung.com',
		];

		$sources['Twitter'] = [
			't.co',
			'twitter',
			'twitter.com',
		];

		$sources['Vereine'] = [
			'loewenfrankfurt-playground.de',
			'lokfalkenberg.de',
			'es-weisswasser.de',
			'transfermarkt.de',
			'ssg-strausberg.de',
			'forum.es-weisswasser.de',
			'fupa.net',
			'reitturniere.de',
			'victoria-templin.com',
			'ff-schildow.de',
		];

		$sources['Social-Other'] = [
			'linkedin.com',
			'transition.meltwater.com',
			'stepstone.de',
			'monitoring.echobot.de',
			'app.asana.com',
			'dlvr.it',
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
			'live.maerkischepresse.net',
			'umfragetool',
			'reg_Button',
			'swp.de',
			'mozde',
		];

		$sources['Intern-Werbung'] = [
			'lr',
			'lrde',
			'lr-online',
			'lronline',
			'print',
			'display',
			'cpc',
			'edgead',
			'edge',
			'edge ad',
			'edge_ad',
			'edge-ad',
			'header-ad',
			'header-bar',
			'headerline',
			'plus-werbebalken',
			'angebote_mozplus',
			'start-banner',
			'pr_teaser',
			'dossierbox',
			'halfpage-ad',
			'billboard',
			'anzeigeplus',
			'teaser_box',
			'swpstart',
			'vertriebsbox1_hp',
			'artikelboxmauerbau',
		];

		$sources['Outbrain'] = [
			'outbrain.com',
			'traffic.outbrain.com',
		];

		$sources['Intern-Tests'] = [
			'redmine.moz.de',
		];

		$sources['Aboshop'] = [
			'abo.moz.de',
			'abo.lr-online.de',
			'abo.lr-digital.de',
			'aboshop',
		];

		$sources['Plenigo'] = [
			'checkout.plenigo.com',
		];

		$sources['Push'] = [
			'browser',
			'push-notification',
			'web-push',
		];

		$sources['Search-Other'] = [
			'bing',
			'ecosia.org',
			'duckduckgo',
			'yahoo',
			'de.search.yahoo.com',
			'qwant.com',
			'fireball.de',
			'suche.aol.de',
			'suche.t-online.de',
			'search.becovi.com',
			'flipboard.com',
			'namenfinden.de',
			'cse.start.fyi',
		];

		$sources['Newsletter'] = [
			'email',
			'deref-web.de',
			'deref-gmx.net',
			'email.t-online.de',
			'm-email.t-online.de',
			'mobilemailer-bs.web.de',
			'webmail.freenet.de',
			'eclipso.de',
			'login.ok.de',
			'mail.google.com',
			'spikenow.com',
			'email04.active24.com',
			'mail.picabyte.de',
			'vodafonemail.de',
			'webto.salesforce.com',
			'webmail.uni-hannover.de',
			'mobilemailer-bap.web.de',
			'mobilemailer-bap.gmx.net',
			'mobilemailer-bs.gmx.net',
			'mobilemailer-bs.web.de',
			'lightmailer-bap.gmx.net',
			'lightmailer-bap.web.de',
			'lightmailer-bs.web.de',
			'lightmailer-bs.gmx.net',
			'my.mail.de',
			'guerrillamail.com',
			'deref-1und1.de',
			'byom.de',
			'newsletter',
			'10minutemail.net',
			'outlook.live.com',
		];

		return $sources;
	}

}
