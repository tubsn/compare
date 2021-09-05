<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;

class Lists extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Articles,DailyKPIs,Orders,Charts');
	}

	public function index() {
		$viewData['articles'] = $this->Articles->list();
		$this->view->title = 'Artikel-Übersicht';
		$this->view->info = '(Es werden maximal 2000 Artikel dargestellt)';
		$this->view->referer('/');
		$this->view->render('pages/list', $viewData);
	}


	public function cards() {

		$this->view('CardLayout');

		$viewData['articles'] = $this->Articles->conversions_only();

		$this->view->title = 'Krams';
		$this->view->render('pages/cards', $viewData);

	}

	public function unset_only() {
		$viewData['articles'] = $this->Articles->list_unset();

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}
		$this->view->title = 'Nicht Zugeordnet: ' . $count;
		$this->view->info = 'Liste aller nicht zugeordneten Artikel für diesen Zeitraum <b>(um alle zu listen oben rechts "alle Daten" einstellen)</b> ';
		$this->view->referer('/unset');
		$this->view->render('pages/list', $viewData);
	}

	public function conversions() {
		$viewData['articles'] = $this->Articles->conversions_only();
		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['primaryChart'] = $this->Charts->get('conversionsByDate');

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscribers'] = $this->Articles->sum_up($viewData['articles'],'subscribers');
		$viewData['avgmediatime'] = $this->Articles->average_up($viewData['articles'],'avgmediatime');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->title = 'Anzahl von Artikeln mit Conversions: ' . $count;
		$this->view->info = '<b>Hinweis:</b> Auf dieser Seite wird nach dem Publikationsdatum des Artikels gefiltert! Die <b>Gesamtzahl der Conversions</b> ergibt sich aus der <b>Summe, der in den Artikeln erreichten Conversions</b>, die an diesen Tagen Produziert wurden. <br/><b>Achtung:</b> Im Diagramm werden taggenau <b>alle Conversions in diesem Zeitraum</b> gezeigt (auch Plusseite).';
		$this->view->referer('/conversions');
		$this->view->render('pages/list', $viewData);
	}

	public function pageviews() {
		$viewData['articles'] = $this->Articles->pageviews_only($minimum = 2500);
		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['primaryChart'] = $this->DailyKPIs->combined_kpis_filtered_chart();

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscribers'] = $this->Articles->sum_up($viewData['articles'],'subscribers');
		$viewData['avgmediatime'] = $this->Articles->average_up($viewData['articles'],'avgmediatime');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->title = 'Klick-Highlights: ' . $count;
		$this->view->info = 'Auflistung von Artikeln mit <b>mehr als 2500</b> klicks, die im eingestellten Zeitraum publiziert wurden.';
		$this->view->referer('/pageviews');
		$this->view->render('pages/list', $viewData);
	}


	public function mediatime() {
		$viewData['articles'] = $this->Articles->mediatime_only($minimum = 150);
		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['primaryChart'] = $this->Charts->get('mediatimeByRessort');

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscribers'] = $this->Articles->sum_up($viewData['articles'],'subscribers');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['avgmediatime'] = $this->Articles->average_up($viewData['articles'],'avgmediatime');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->title = 'Mediatime-Highlights: ' . $count;
		$this->view->info = 'Auflistung von Artikeln mit <b>mehr als 150s</b> durchschnittlicher Mediatime, die im eingestellten Zeitraum publiziert wurden.';
		$this->view->referer('/mediatime');
		$this->view->render('pages/list', $viewData);
	}

	public function subscribers() {
		$viewData['articles'] = $this->Articles->subscriber_only($minimum = 2000);

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}


		$viewData['primaryChart'] = $this->Charts->get('subscribersByRessort');
		$viewData['secondaryChart'] = $this->Charts->get('subscribersByDate');

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['subscribers'] = $this->Articles->sum_up($viewData['articles'],'subscribers');
		$viewData['avgmediatime'] = $this->Articles->average_up($viewData['articles'],'avgmediatime');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$viewData['showSubscribersInTable'] = true;

		$this->view->title = 'Von Abonnenten gelesene Artikel';
		$this->view->info = '<b>Bitte beachten:</b> wir haben verhältnismäßig wenige aktive Abonnenten die einen Plusartikel auch tatsächlich lesen können!';
		$this->view->referer('/subscribers');
		$this->view->render('pages/list', $viewData);
	}

	public function author($author) {

		if (!Auth::has_right('author')) {
			throw new \Exception("Keine Berechtigung", 403);
		}

		$author = $this->decode_url($author);
		$viewData['articles'] = $this->Articles->list_by($author, 'author');
		$viewData['primaryChart'] = $this->DailyKPIs->combined_kpis_filtered_chart($author, 'author');

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscribers'] = $this->Articles->sum_up($viewData['articles'],'subscribers');
		$viewData['avgmediatime'] = $this->Articles->average_up($viewData['articles'],'avgmediatime');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->title = 'Autorenseite - '. $author . ' - Artikel: ' . $count;
		$this->view->info = null;
		$this->view->referer('/author/' . $author);
		$this->view->render('pages/list', $viewData);
	}

	public function author_fuzzy($author) {

		if (!Auth::has_right('author')) {
			throw new \Exception("Keine Berechtigung", 403);
		}

		$author = $this->decode_url($author);
		$viewData['articles'] = $this->Articles->list_by_fuzzy($author, 'author');
		$viewData['primaryChart'] = $this->DailyKPIs->combined_kpis_filtered_chart($author, 'author');

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscribers'] = $this->Articles->sum_up($viewData['articles'],'subscribers');
		$viewData['avgmediatime'] = $this->Articles->average_up($viewData['articles'],'avgmediatime');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->title = 'Autorenseite - '. $author . ' - Artikel: ' . $count;
		$this->view->info = null;
		$this->view->referer('/author/' . $author);
		$this->view->render('pages/list', $viewData);
	}


	public function ressort($ressort = null) {
		Session::set('referer', '/ressort/'.$ressort);

		$ressortList = $this->Articles->list_distinct('ressort');

		if ($ressortList[0] == 'bilder') {
			$temp = array_shift($ressortList);
			array_push($ressortList, $temp);
		}

		if ($ressortList[0] == 'blaulicht') {
			$temp = array_shift($ressortList);
			array_push($ressortList, $temp);
		}


		if ($ressort == null) {$ressort = $ressortList[0];}

		$ressort = $this->decode_url($ressort);

		$viewData['articles'] = $this->Articles->list_by($ressort, 'ressort');
		$viewData['primaryChart'] = $this->DailyKPIs->combined_kpis_filtered_chart($ressort, 'ressort');

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscribers'] = $this->Articles->sum_up($viewData['articles'],'subscribers');
		$viewData['avgmediatime'] = $this->Articles->average_up($viewData['articles'],'avgmediatime');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;
		$viewData['ressorts'] = $ressortList;

		$this->view->navigation = 'navigation/ressort-menu';
		$this->view->title = 'Artikel aus ' . ucwords($ressort) . ': ' . $count;
		$this->view->info = null;
		$this->view->render('pages/list', $viewData);
	}

	public function type($type = null) {
		Session::set('referer', '/type/'.$type);

		$viewData['typeList'] = $this->Articles->list_distinct('type');
		if (is_null($type)) {$type = $viewData['typeList'][0] ?? '';}

		$type = $this->decode_url($type);
		$viewData['articles'] = $this->Articles->list_by($type, 'type');
		$viewData['primaryChart'] = $this->DailyKPIs->combined_kpis_filtered_chart($type, 'type');

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscribers'] = $this->Articles->sum_up($viewData['articles'],'subscribers');
		$viewData['avgmediatime'] = $this->Articles->average_up($viewData['articles'],'avgmediatime');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->navigation = 'navigation/type-menu';
		$this->view->title = $type . ' - Artikel: ' . $count;
		$this->view->render('pages/list', $viewData);
	}

	public function tag($tag = null) {
		Session::set('referer', '/tag/'.$tag);

		$viewData['tagList'] = $this->Articles->list_distinct('tag');
		if (is_null($tag)) {$tag = $viewData['tagList'][0] ?? '';}

		$tag = $this->decode_url($tag);

		$viewData['articles'] = $this->Articles->list_by($tag, 'tag');
		$viewData['primaryChart'] = $this->DailyKPIs->combined_kpis_filtered_chart($tag, 'tag');

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscribers'] = $this->Articles->sum_up($viewData['articles'],'subscribers');
		$viewData['avgmediatime'] = $this->Articles->average_up($viewData['articles'],'avgmediatime');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->navigation = 'navigation/tag-menu';
		$this->view->title = '#Tag - ' . ucwords($tag) . ': ' . $count;
		$this->view->info = null;
		$this->view->render('pages/list', $viewData);
	}


	public function top5() {
		Session::set('referer', '/top5');
		$viewData['list']['conversions'] = $this->Articles->conversions_only(5);
		$viewData['list']['subscribers'] = $this->Articles->subscriber_only(5);
		$viewData['list']['mediatime'] = $this->Articles->mediatime_only($minimum = 150, $limit=5);
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
