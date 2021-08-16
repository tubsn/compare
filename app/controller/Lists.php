<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;

class Lists extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Articles,Stats');
	}

	public function index() {
		Session::set('referer', '/');
		$viewData['articles'] = $this->Articles->list();
		$this->view->title = 'Artikel-Übersicht';
		$this->view->info = '(Es werden maximal 2000 Artikel dargestellt)' ;
		$this->view->render('pages/list', $viewData);
	}


	public function cards() {

		$this->view('CardLayout');

		$viewData['articles'] = $this->Articles->conversions_only();

		$this->view->title = 'Krams';
		$this->view->render('pages/cards', $viewData);

	}

	public function unset_only() {
		Session::set('referer', '/unset');
		$viewData['articles'] = $this->Articles->list_unset();

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}
		$this->view->title = 'Nicht Zugeordnet: ' . $count;
		$this->view->info = 'Liste aller nicht zugeordneten Artikel für diesen Zeitraum <b>(um alle zu listen oben rechts "alle Daten" einstellen)</b> ';
		$this->view->render('pages/list', $viewData);
	}


	public function plus() {
		Session::set('referer', '/plus');
		$viewData['articles'] = $this->Articles->plus_only();
		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscribers'] = $this->Articles->sum_up($viewData['articles'],'subscribers');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->title = 'Plusartikel: ' . $count;
		$this->view->info = null;
		$this->view->render('pages/list', $viewData);
	}

	public function conversions() {
		Session::set('referer', '/conversions');
		$viewData['articles'] = $this->Articles->conversions_only();
		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscribers'] = $this->Articles->sum_up($viewData['articles'],'subscribers');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->title = 'Artikel mit Conversions: ' . $count;
		$this->view->info = null;
		$this->view->render('pages/list', $viewData);
	}

	public function pageviews() {
		Session::set('referer', '/pageviews');
		$viewData['articles'] = $this->Articles->pageviews_only($minimum = 2500);
		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscribers'] = $this->Articles->sum_up($viewData['articles'],'subscribers');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->title = 'Klick-Highlights: ' . $count;
		$this->view->info = 'Auflistung von Artikeln mit mehr als 2500 klicks';
		$this->view->render('pages/list', $viewData);
	}

	public function subscribers() {
		Session::set('referer', '/subscribers');
		$viewData['articles'] = $this->Articles->subscriber_only($minimum = 2000);

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['subscribers'] = $this->Articles->sum_up($viewData['articles'],'subscribers');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->title = 'Artikel mit Subscriberviews: ' . $count;
		$this->view->info = 'Artikel die von Pluslesern gelesen wurden. <b>Bitte beachten:</b> wir haben nur ca. 2500 aktive Abonnenten die einen Plusartikel lesen können!';
		$this->view->render('pages/list', $viewData);
	}

	public function author($author) {

		if (!Auth::has_right('author')) {
			throw new \Exception("Keine Berechtigung", 403);
		}

		Session::set('referer', '/author/'.$author);
		$author = $this->decode_url($author);
		$viewData['articles'] = $this->Articles->list_by($author, 'author');
		$viewData['chart'] = $this->Stats->get_grouped_chart_data($author, 'author');

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscribers'] = $this->Articles->sum_up($viewData['articles'],'subscribers');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->title = 'Autorenseite - '. $author . ' - Artikel: ' . $count;
		$this->view->info = null;
		$this->view->render('pages/list', $viewData);
	}

	public function author_fuzzy($author) {

		if (!Auth::has_right('author')) {
			throw new \Exception("Keine Berechtigung", 403);
		}

		Session::set('referer', '/author/'.$author);
		$author = $this->decode_url($author);
		$viewData['articles'] = $this->Articles->list_by_fuzzy($author, 'author');
		$viewData['chart'] = $this->Stats->get_grouped_chart_data($author, 'author');

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscribers'] = $this->Articles->sum_up($viewData['articles'],'subscribers');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->title = 'Autorenseite - '. $author . ' - Artikel: ' . $count;
		$this->view->info = null;
		$this->view->render('pages/list', $viewData);
	}



	public function ressort($ressort = null) {
		Session::set('referer', '/ressort/'.$ressort);

		$ressortList = $this->Articles->list_distinct('ressort');
		if ($ressort == null) {$ressort = $ressortList[0];}

		$ressort = $this->decode_url($ressort);

		$viewData['articles'] = $this->Articles->list_by($ressort, 'ressort');
		$viewData['chart'] = $this->Stats->get_grouped_chart_data($ressort, 'ressort');

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscribers'] = $this->Articles->sum_up($viewData['articles'],'subscribers');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;
		$viewData['ressorts'] = $ressortList;

		$this->view->navigation = 'navigation/ressort-menu';
		$this->view->title = 'Artikel aus ' . ucwords($ressort) . ': ' . $count;
		$this->view->info = null;
		$this->view->render('pages/list', $viewData);
	}

	public function type($type = ARTICLE_TYPES[0]) {
		Session::set('referer', '/type/'.$type);
		$type = $this->decode_url($type);
		$viewData['articles'] = $this->Articles->list_by($type, 'type');
		$viewData['chart'] = $this->Stats->get_grouped_chart_data($type, 'type');

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscribers'] = $this->Articles->sum_up($viewData['articles'],'subscribers');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->navigation = 'navigation/type-menu';
		$this->view->title = $type . ' - Artikel: ' . $count;
		$this->view->render('pages/list', $viewData);
	}

	public function tag($tag = ARTICLE_TAGS[0]) {
		Session::set('referer', '/tag/'.$tag);
		$tag = $this->decode_url($tag);

		$viewData['articles'] = $this->Articles->list_by($tag, 'tag');
		$viewData['chart'] = $this->Stats->get_grouped_chart_data($tag, 'tag');

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscribers'] = $this->Articles->sum_up($viewData['articles'],'subscribers');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->navigation = 'navigation/tag-menu';
		$this->view->title = '#Tag - ' . ucwords($tag) . ': ' . $count;
		$this->view->info = null;
		$this->view->render('pages/list', $viewData);
	}


	public function top5() {
		Session::set('referer', '/top5/');
		$viewData['list']['conversions'] = $this->Articles->conversions_only(5);
		$viewData['list']['subscribers'] = $this->Articles->subscriber_only(5);
		$viewData['list']['pageviews'] = $this->Articles->pageviews_only($minimum = 5, $limit=5);

		$ids = [];
		foreach ($viewData['list'] as $articles) {
			$ids = array_merge($ids,array_column($articles,'id'));
		}

		$countedIDs = array_count_values($ids);
		$multipleIDs = array_filter($countedIDs, function($id) {return $id > 1;});

		foreach ($viewData['list'] as $type => $list) {
			$viewData['list'][$type] = array_map(function($article) use ($multipleIDs) {

				$article['multiple'] = null;

				if (in_array($article['id'], array_keys($multipleIDs))) {
					$article['multiple'] = $multipleIDs[$article['id']];
					return $article;
				}
				else return $article;

			}, $viewData['list'][$type]);
		}

		$this->view->info = 'Gelb = Artikel nur in einer Topliste, Grün = Artikel in mehreren Toplisten';
		$this->view->title = 'Top5 - Artikel';
		$this->view->render('pages/top5list', $viewData);
	}



	private function decode_url($urlString) {
		$urlString = urldecode($urlString);
		$urlString = strip_tags($urlString);
		$urlString = str_replace("-slash-", "/", $urlString);
		return $urlString;
	}

}
