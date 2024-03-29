<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;

class Lists extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Articles,ArticleMeta,ArticleKPIs,Orders,Charts,Discover');
	}

	public function index() {
		$viewData['articles'] = $this->Articles->list();
		$this->view->title = 'Artikel-Übersicht';
		$this->view->info = '(Es werden maximal 2000 Artikel dargestellt)';
		$this->view->referer('/');
		$this->view->render('articles/list', $viewData);
	}


	public function cards() {

		$this->view('CardLayout');

		$viewData['articles'] = $this->Articles->conversions_only();

		$this->view->title = 'Krams';
		$this->view->render('articles/cards', $viewData);

	}

	public function unset_only() {
		$viewData['articles'] = $this->Articles->list_unset();
		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}
		$this->view->title = 'Artikel ohne Themenzuweisung: ' . $count;
		$this->view->info = 'Liste aller nicht zugeordneten Artikel für diesen Zeitraum <b>(um alle zu listen oben rechts "alle Daten" einstellen)</b> | <a href="/admin/topics">Artikel automatisch zuordnen (Beta)</a>';
		$this->view->referer('/unclassified/types');
		$this->view->render('articles/list', $viewData);
	}

	public function unset_audience_only() {
		$viewData['articles'] = $this->Articles->list_no_audience();
		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}
		$this->view->title = 'Artikel ohne Audience: ' . $count;
		$this->view->info = 'Liste aller Artikel ohne Audience für diesen Zeitraum <b>(um alle zu listen oben rechts "alle Daten" einstellen)</b>';
		$this->view->referer('/unclassified/audiences');
		$this->view->render('articles/list', $viewData);
	}

	public function conversions() {
		$viewData['articles'] = $this->Articles->conversions_only();
		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['primaryChart'] = $this->Charts->get('conversions_by_date');

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscriberviews'] = $this->Articles->sum_up($viewData['articles'],'subscriberviews');
		$viewData['avgmediatime'] = $this->Articles->average_up($viewData['articles'],'avgmediatime');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->title = 'Anzahl von Artikeln mit Conversions: ' . $count;
		if ($count>=1000) {
			$this->view->title = 'Anzahl von Artikeln mit Conversions: >' . $count;
		}
		$this->view->info = '<b>Hinweis:</b> Auf dieser Seite wird nach dem Publikationsdatum des Artikels gefiltert! Die <b>Gesamtzahl der Conversions</b> ergibt sich aus der <b>Summe, der in den Artikeln erreichten Conversions</b>, die an diesen Tagen produziert wurden. <br/><b>Achtung:</b> Im Diagramm werden taggenau <b>alle Conversions in diesem Zeitraum</b> gezeigt (auch Plusseite).';
		$this->view->referer('/conversions');
		$this->view->render('articles/list', $viewData);
	}

	public function pageviews() {
		$viewData['articles'] = $this->Articles->pageviews_only($minimum = 2500);
		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['primaryChart'] = $this->ArticleKPIs->combined_kpis_filtered_chart();

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscriberviews'] = $this->Articles->sum_up($viewData['articles'],'subscriberviews');
		$viewData['avgmediatime'] = $this->Articles->average_up($viewData['articles'],'avgmediatime');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->title = 'Klick-Highlights: ' . $count;
		$this->view->info = 'Auflistung von Artikeln mit <b>mehr als 2500</b> klicks, die im eingestellten Zeitraum publiziert wurden.';
		$this->view->referer('/pageviews');
		$this->view->render('articles/list', $viewData);
	}


	public function scores() {

		$minScore = 85;

		if (PORTAL == 'SWP') {$minScore = 125;}

		$viewData['articles'] = $this->Articles->score_articles($minScore);

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscriberviews'] = $this->Articles->sum_up($viewData['articles'],'subscriberviews');
		$viewData['avgmediatime'] = $this->Articles->average_up($viewData['articles'],'avgmediatime');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->title = 'Artikel mit mehr als ' . $minScore . ' Score Punkten';
		$this->view->info = 'Score-Formel: (conversions * 20) + (pageviews / 1000 * 5) + ((avgmediatime / 10) * 2) + (subscriberviews / 100 * 3)';
		$this->view->referer('/score');
		$this->view->render('articles/list', $viewData);

	}


	public function mediatime() {
		$viewData['articles'] = $this->Articles->mediatime_only($minimum = 150);
		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['primaryChart'] = $this->Charts->get('mediatime_by', 'ressort');

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscriberviews'] = $this->Articles->sum_up($viewData['articles'],'subscriberviews');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['avgmediatime'] = $this->Articles->average_up($viewData['articles'],'avgmediatime');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->title = 'Mediatime-Highlights: ' . $count;
		$this->view->info = 'Auflistung von Artikeln mit <b>mehr als 150s</b> durchschnittlicher Mediatime, die im eingestellten Zeitraum publiziert wurden.';
		$this->view->referer('/mediatime');
		$this->view->render('articles/list', $viewData);
	}

	public function subscribers() {
		$viewData['articles'] = $this->Articles->subscriber_only($minimum = 2000);

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['secondaryChart'] = $this->Charts->get('subscriberviews_by_date');

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['subscriberviews'] = $this->Articles->sum_up($viewData['articles'],'subscriberviews');
		$viewData['avgmediatime'] = $this->Articles->average_up($viewData['articles'],'avgmediatime');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$viewData['showSubscribersInTable'] = true;

		$this->view->title = 'Von Abonnenten gelesene Artikel';
		$this->view->info = '<b>Bitte beachten:</b> wir haben verhältnismäßig wenige aktive Abonnenten die einen Plusartikel auch tatsächlich lesen können!';
		$this->view->referer('/subscribers');
		$this->view->render('articles/list', $viewData);
	}

	public function author($author) {

		if (!Auth::has_right('author')) {
			throw new \Exception("Keine Berechtigung", 403);
		}

		$author = $this->decode_url($author);
		$viewData['articles'] = $this->Articles->list_by($author, 'author');
		$viewData['primaryChart'] = $this->ArticleKPIs->combined_kpis_filtered_chart($author, 'author');

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscriberviews'] = $this->Articles->sum_up($viewData['articles'],'subscriberviews');
		$viewData['avgmediatime'] = $this->Articles->average_up($viewData['articles'],'avgmediatime');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->title = 'Autorenseite - '. $author . ' - Artikel: ' . $count;
		$this->view->info = null;
		$this->view->referer('/author/' . $author);
		$this->view->render('articles/list', $viewData);
	}

	public function author_fuzzy($author) {

		if (!Auth::has_right('author')) {
			throw new \Exception("Keine Berechtigung", 403);
		}

		$author = $this->decode_url($author);
		$viewData['articles'] = $this->Articles->list_by_fuzzy($author, 'author');
		$viewData['primaryChart'] = $this->ArticleKPIs->combined_kpis_filtered_chart($author, 'author');

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscriberviews'] = $this->Articles->sum_up($viewData['articles'],'subscriberviews');
		$viewData['avgmediatime'] = $this->Articles->average_up($viewData['articles'],'avgmediatime');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->title = 'Autorenseite - '. $author . ' - Artikel: ' . $count;
		$this->view->info = null;
		$this->view->referer('/author/' . $author);
		$this->view->render('articles/list', $viewData);
	}


	public function ressort($ressort = null) {
		Session::set('referer', '/ressort/'.$ressort);

		$ressortList = $this->Articles->list_distinct('ressort');

		if (isset($ressortList[0]) && $ressortList[0] == 'bilder') {
			$temp = array_shift($ressortList);
			array_push($ressortList, $temp);
		}

		if (isset($ressortList[0]) && $ressortList[0] == 'blaulicht') {
			$temp = array_shift($ressortList);
			array_push($ressortList, $temp);
		}


		if ($ressort == null && isset($ressortList[0])) {$ressort = $ressortList[0];}

		$ressort = $this->decode_url($ressort);

		$viewData['articles'] = $this->Articles->list_by($ressort, 'ressort');
		$viewData['primaryChart'] = $this->ArticleKPIs->combined_kpis_filtered_chart($ressort, 'ressort');

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscriberviews'] = $this->Articles->sum_up($viewData['articles'],'subscriberviews');
		$viewData['avgmediatime'] = $this->Articles->average_up($viewData['articles'],'avgmediatime');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;
		$viewData['ressorts'] = $ressortList;

		$this->view->navigation = 'navigation/ressort-menu';
		$this->view->title = 'Artikel aus ' . ucwords($ressort) . ': ' . $count;
		$this->view->info = null;
		$this->view->render('articles/list', $viewData);
	}

	public function type($type = null) {
		Session::set('referer', '/type/'.$type);

		$viewData['typeList'] = $this->Articles->list_distinct('type');
		if (is_null($type)) {$type = $viewData['typeList'][0] ?? '';}

		$type = $this->decode_url($type);
		$viewData['articles'] = $this->Articles->list_by($type, 'type');
		$viewData['primaryChart'] = $this->ArticleKPIs->combined_kpis_filtered_chart($type, 'type');

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscriberviews'] = $this->Articles->sum_up($viewData['articles'],'subscriberviews');
		$viewData['avgmediatime'] = $this->Articles->average_up($viewData['articles'],'avgmediatime');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->navigation = 'navigation/type-menu';
		$this->view->title = $type . ' - Artikel: ' . $count;
		$this->view->render('articles/list', $viewData);
	}

	public function audience($audience = null) {
		Session::set('referer', '/audience/'.$audience);

		$viewData['audienceList'] = $this->Articles->list_distinct('audience');
		array_push($viewData['audienceList'],'Alle');
		if (is_null($audience)) {$audience = $viewData['audienceList'][0] ?? '';}

		$audience = $this->decode_url($audience);

		if ($audience == 'Alle') {
			$viewData['articles'] = $this->Articles->list_all_audiences();
		}
		else {$viewData['articles'] = $this->Articles->list_by($audience, 'audience');}


		$viewData['primaryChart'] = $this->ArticleKPIs->combined_kpis_filtered_chart($audience, 'audience');

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscriberviews'] = $this->Articles->sum_up($viewData['articles'],'subscriberviews');
		$viewData['avgmediatime'] = $this->Articles->average_up($viewData['articles'],'avgmediatime');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->navigation = 'navigation/audience-menu';
		$this->view->title = 'Audience: ' . $audience . ' - Artikel: ' . $count;
		$this->view->render('articles/list', $viewData);
	}

	public function tag($tag = null) {
		Session::set('referer', '/tag/'.$tag);

		$viewData['tagList'] = $this->Articles->list_distinct('tag');
		if (is_null($tag)) {$tag = $viewData['tagList'][0] ?? '';}

		$tag = $this->decode_url($tag);

		$viewData['articles'] = $this->Articles->list_by($tag, 'tag');

		$viewData['primaryChart'] = $this->ArticleKPIs->combined_kpis_filtered_chart($tag, 'tag');

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscriberviews'] = $this->Articles->sum_up($viewData['articles'],'subscriberviews');
		$viewData['avgmediatime'] = $this->Articles->average_up($viewData['articles'],'avgmediatime');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		if ($viewData['articles']) {
		//	$viewData['emotions'] = $this->ArticleMeta->list_emotions( array_column($viewData['articles'],'id') );
		}

		$this->view->navigation = 'navigation/tag-menu';
		$this->view->title = '#Tag - ' . ucwords($tag) . ': ' . $count;
		$this->view->info = null;
		$this->view->render('articles/list', $viewData);

	}


	public function userneed($userneed = null) {
		Session::set('referer', '/userneed/'.$userneed);

		$viewData['userneedList'] = $this->Articles->list_distinct('userneed');
		if (is_null($userneed)) {$userneed = $viewData['userneedList'][0] ?? '';}

		$userneed = $this->decode_url($userneed);
		$viewData['articles'] = $this->Articles->list_by($userneed, 'userneed');
		$viewData['primaryChart'] = $this->ArticleKPIs->combined_kpis_filtered_chart($userneed, 'userneed');

		$count = 0;
		if (is_array($viewData['articles'])) {$count = count($viewData['articles']);}

		$viewData['pageviews'] = $this->Articles->sum_up($viewData['articles'],'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['subscriberviews'] = $this->Articles->sum_up($viewData['articles'],'subscriberviews');
		$viewData['avgmediatime'] = $this->Articles->average_up($viewData['articles'],'avgmediatime');
		$viewData['conversions'] = $this->Articles->sum_up($viewData['articles'],'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = $count;

		$this->view->navigation = 'navigation/userneed-menu';
		$this->view->title = $userneed . ' - Artikel: ' . $count;
		$this->view->render('articles/list', $viewData);
	}


	public function top5() {
		Session::set('referer', '/top5');
		$this->view->info = 'Gelb = Artikel nur in einer Topliste, Grün = Artikel in mehreren Toplisten';
		$this->view->title = 'Top5 - Artikel';

		$viewData['list']['conversions'] = $this->Articles->conversions_only(5);
		$viewData['list']['subscriberviews'] = $this->Articles->subscriber_only(5);
		$viewData['list']['mediatime'] = $this->Articles->mediatime_only($minimum = 150, $limit=5);
		$viewData['list']['pageviews'] = $this->Articles->pageviews_only($minimum = 5, $limit=5);

		$ids = [];
		foreach ($viewData['list'] as $articles) {
			if (is_null($articles)) {break;}
			$ids = array_merge($ids,array_column($articles,'id'));
		}

		$countedIDs = array_count_values($ids);
		$multipleIDs = array_filter($countedIDs, function($id) {return $id > 1;});

		foreach ($viewData['list'] as $type => $list) {
			if (is_null($viewData['list'][$type])) {break;}
			$viewData['list'][$type] = array_map(function($article) use ($multipleIDs) {
				$article['multiple'] = null;

				if (in_array($article['id'], array_keys($multipleIDs))) {
					$article['multiple'] = $multipleIDs[$article['id']];
					return $article;
				}
				else return $article;

			}, $viewData['list'][$type]);
		}

		$this->view->render('pages/top5list', $viewData);
	}


	public function valueables($type = 'geister') {

		Session::set('referer', '/valueables');
		$viewData['articles'] = $this->Articles->valueables_by_group($type);

		switch ($type) {
			case 'geister': $this->view->info = 'Artikel ohne Conversions und mit weniger als 100 Subscriber Views (Sie sorgen weder für Neukunden noch bieten sie relevante Informationen für Bestandskunden)'; break;
			case 'abwehr': $this->view->info = 'Artikel mit mindestens 100 Subscriber Views (Sie halten unsere Abonnenten/Subscriber im Abo, weil Sie relevante Inhalte bieten)'; break;
			case 'stuermer': $this->view->info = 'Artikel mit mindestens 1 Conversion (Sie sorgen für Neukunden)'; break;
			case 'spielmacher': $this->view->info = 'Artikel mit mindestens 100 Subscriber Views und mindestens 1 Conversion (Sie halten unsere Abonnenten/Subscriber im Abo, weil Sie relevante Inhalte bieten und generieren Neukunden)'; break;
			default: $this->view->info = 'Es werden maximal 2000 Artikel angezeigt'; break;
		}

		$this->view->title = ucfirst($type) . ' - Artikel: ' . count($viewData['articles'] ?? []);
		$this->view->render('articles/list', $viewData);
	}

	public function discover() {

		$this->view->chart = $this->Charts->discover_articles_by();
		$this->view->chart2 = $this->Charts->discover_articles_by_date();

		$this->view->title = 'Artikel, die bei Google Discover gelistet waren';
		$this->view->info = 'Hier werden Artikel gelistet, die bei Google Discover gelistet wurden. D-Klicks und Impressions werden jeweils aus den importierten Daten aus der Search Console gespeist. (Achtung: die Discover Daten werden nur bei Bedarf aktualisiert)';
		$this->view->articles = $this->Discover->list();
		$this->view->render('pages/discover');

	}

	private function decode_url($urlString) {
		if (is_null($urlString)) {return $urlString;}
		$urlString = urldecode($urlString);
		$urlString = strip_tags($urlString);
		$urlString = str_replace("-slash-", "/", $urlString);
		return $urlString;
	}

}
