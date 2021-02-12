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
		$this->view->title = 'Neuste Artikel';
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
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->title = 'Klick-Highlights: ' . $count;
		$this->view->info = 'Auflistung von Artikeln mit mehr als 2500 klicks';
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
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->title = 'Autorenseite - '. $author . ' - Artikel: ' . $count;
		$this->view->info = null;
		$this->view->render('pages/list', $viewData);
	}

	public function ressort($ressort = 'cottbus') {
		Session::set('referer', '/ressort/'.$ressort);
		$ressort = $this->decode_url($ressort);

		$viewData['articles'] = $this->Articles->list_by($ressort, 'ressort');
		$viewData['chart'] = $this->Stats->get_grouped_chart_data($ressort, 'ressort');

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

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
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->navigation = 'navigation/type-menu';
		$this->view->title = $type . ' - Artikel: ' . $count;
		$this->view->render('pages/list', $viewData);
	}

	private function decode_url($urlString) {
		$urlString = urldecode($urlString);
		$urlString = strip_tags($urlString);
		$urlString = str_replace("-slash-", "/", $urlString);
		return $urlString;
	}

}
