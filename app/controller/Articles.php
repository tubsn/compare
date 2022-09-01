<?php

namespace app\controller;
use app\importer\ArticleImport;
use flundr\mvc\Controller;
use flundr\auth\Auth;
use flundr\utility\Session;
use flundr\cache\RequestCache;

class Articles extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
		$this->view('DefaultLayout');
		$this->view->navigation = 'navigation/article-menu';
		$this->models('Articles,Analytics,ArticleKPIs,Conversions,Linkpulse,Kilkaya,Plenigo,ArticleMeta');
	}


	public function detail($id) {
		$viewData['article'] = $this->Articles->get($id);

		if (empty($viewData['article'])) {
			$this->quick_import($id);
			$viewData['article'] = $this->Articles->get($id);
		}

		if (empty($viewData['article'])) {
			throw new \Exception('Artikel nicht gefunden', 404);
		}

		if ($viewData['article']['pageviews'] == null) {
			$pubDate = formatDate($viewData['article']['pubdate'], 'Y-m-d');
			$this->collect_data($id, $pubDate);
			$viewData['article'] = $this->Articles->get($id); // Reload fresh Article Data
		}

		// Returns Stats aggregated by Day for this ID
		$viewData['stats'] = $this->ArticleKPIs->by_id($id);
		$viewData['chart'] = $this->ArticleKPIs->detail_chart_data($viewData['stats']);

		// Ressort Starts
		$ressortStats = $this->Articles->stats_grouped_by('ressort')[$viewData['article']['ressort']] ?? null;

		//dump($ressortStats);

		if ($ressortStats['pageviews'] ?? 0 > 0) {$ressortPageviewsAverage = $ressortStats['pageviews'] / $ressortStats['artikel'];}
		else {$ressortPageviewsAverage = 1;}

		if ($ressortStats['subscriberviews'] ?? 0 > 0) {$ressortSubsAverage = $ressortStats['subscriberviews'] / $ressortStats['artikel'];}
		else {$ressortSubsAverage = 1;}

		$pageViewsToRessort = ($viewData['article']['pageviews'] / $ressortPageviewsAverage * 100);

		$viewData['ressortAverage'] = round($ressortPageviewsAverage);
		$viewData['ressortRank'] = round($pageViewsToRessort);
		$viewData['ressortAvgergeSubs'] = round($ressortSubsAverage);

		// Detailled Conversion / Transaction Stats
		$this->Conversions->articleID = $id;
		$this->Conversions->pubDate = formatDate($viewData['article']['pubdate'],'Y-m-d');

		$viewData['conversions'] = $this->Conversions->collect();
		$viewData['sources'] = $this->Conversions->group_by_combined('ga_source');
		$viewData['cities'] = $this->Conversions->group_by_combined('ga_city');
		$viewData['gender'] = $this->Conversions->group_by_combined('customer_gender');
		$viewData['payments'] = $this->Conversions->group_by_combined('order_payment_method');
		$viewData['cancellation_reasons'] = $this->Conversions->group_by('cancellation_reason');
		$viewData['cancelled'] = $this->Conversions->cancelled_orders();

		$viewData['emotions'] = $this->ArticleMeta->emotions($id);

		// Rendering
		$this->view->title = htmlentities($viewData['article']['title']);
		$this->view->render('pages/detail', $viewData);
	}

	public function edit($id) {

		$viewData['article'] = $this->Articles->get($id);
		$this->view->backlink = '/artikel/' . $id;
		$this->view->render('pages/edit', $viewData);

	}

	public function save($id) {

		if (!auth_rights('type')) {throw new \Exception("Sie haben keine Berechtitung um diesen Inhalt zu Bearbeiten", 403);}

		if (isset($_POST['pubdate'])) {
			$this->Articles->update(['pubdate' => $_POST['pubdate']], $id);
		}

		if (isset($_POST['kicker'])) {
			$this->Articles->update(['kicker' => $_POST['kicker']], $id);
		}

		if (isset($_POST['image'])) {
			$this->Articles->update(['image' => $_POST['image']], $id);
		}

		if (isset($_POST['ressort'])) {
			$this->Articles->update(['ressort' => $_POST['ressort']], $id);
		}

		$this->view->redirect('/artikel/' . $id);

	}

	public function medium($id) {

		$viewData['article'] = $this->Articles->get($id);
		$pubDate = $viewData['article']['pubdate'];

		$mediumCache = new RequestCache('ArticleMedium' . $id, 10 * 60);
		$mediumStats = $mediumCache->get();

		if (!$mediumStats) {
			$mediumStats = $this->Analytics->sources_by_article_id($id, formatDate($pubDate, 'Y-m-d'), 'today');
			$mediumCache->save($mediumStats);
		}

		$viewData['medium'] = $mediumStats;

		$this->view->backlink = '/artikel/' . $id;
		$this->view->render('pages/medium', $viewData);

	}

	public function retresco($id) {
		// Test Stuff
		$import = new \app\importer\RetrescoImport();
		$output = $import->collect($id);
		dd($output);
	}


	public function refresh($id) {
		$pubDate = $this->Articles->get($id,['pubdate'])['pubdate'];
		$pubDate = formatDate($pubDate, 'Y-m-d');
		$this->collect_data($id, $pubDate);
		$this->view->redirect('/artikel/' . $id);
	}


	private function days_since($date) {
		$interval = date_diff(date_create($date), new \DateTime());
		return $interval->days;
	}

	private function collect_data($id, $pubDate = '30daysAgo') {

		// Don't refresh Stuff older then a Year
		if ($this->days_since($pubDate) > MAX_IMPORT_RANGE) {return;}

		// show Realtime Linkpulse/Kilkaya Data for Todays articles
		if ($pubDate == date('Y-m-d')) {
			$stats = $this->Kilkaya->stats_today($id);

			if (isset($stats['date'])) {
				unset($stats['date']);
			}

			$stats['refresh'] = date('Y-m-d H:i:s');
			$this->Articles->update($stats,$id);
			return;
		}

		// GA Pageviews and Sessions
		$gaData = $this->Analytics->by_article_id($id, $pubDate);

		$dailyStats = $gaData['details'];
		$lifeTimeTotals = $gaData['totals'];

		// Buying intent
		$lifeTimeTotals['buyintent'] = $this->Analytics->buy_intention_by_article_id($id, $pubDate);

		// Subscribed Readers
		$subscriberviews = $this->Kilkaya->subscribers($id, $pubDate);
		$lifeTimeTotals['subscriberviews'] = $subscriberviews;

		$this->Articles->add_stats($lifeTimeTotals,$id);
		$this->ArticleKPIs->add($dailyStats,$id);

		if ($lifeTimeTotals['Itemquantity'] < 0) { return; }

		// Transaction Stats
		$this->Conversions->articleID = $id;
		$this->Conversions->pubDate = $pubDate;
		$this->Conversions->refresh();
	}

	public function quick_import($articleID) {
		$import = new ArticleImport();
		$newArticle = $import->detail_rss($articleID);

		if (empty($newArticle)) {
			throw new \Exception('Artikel konnte nicht importiert werden', 404);
		}

		if ($this->days_since($newArticle['pubdate']) >= MAX_IMPORT_RANGE) {
			throw new \Exception('Artikel zu alt zum Importieren', 404);
		}


		$articles = [$newArticle]; // this has to be an Array of articles
		return $this->Articles->add_to_database($articles);
	}

	public function delete($id) {
		if (!auth_rights('type')) {$this->view->redirect('/login');}
		$this->Articles->delete($id);
		$this->ArticleKPIs->delete($id);
		$this->view->redirect('/');
	}

	public function set_type($id) {
		if (!auth_rights('type')) {return;}

		if (isset($_POST['type']) && $_POST['type'] != '') {
			$type = strip_tags($_POST['type']);
			if ($type == '0') {$type = null;}
			$this->Articles->set(['type' => $type], $id);
		}

		if (isset($_POST['tag']) && $_POST['tag'] != '') {
			$tag = strip_tags($_POST['tag']);
			if ($tag == '0') {$tag = null;}
			$this->Articles->set(['tag' => $tag], $id);
		}

		if (isset($_POST['audience']) && $_POST['audience'] != '') {
			$audience = strip_tags($_POST['audience']);
			if ($audience == '0') {$audience = null;}
			$this->Articles->set(['audience' => $audience], $id);
		}

	}

	public function switch_portal() {

		$page = strip_tags($_GET['page'] ?? '/');
		$from = strip_tags($_GET['from'] ?? null);
		$to = strip_tags($_GET['to'] ?? null);
		$getParameters = strip_tags($_GET['get'] ?? null);

		Session::set('timeframe', 'Zeitraum');
		if ($from) {Session::set('from', $from);}
		if ($to) {Session::set('to', $to);}

		$this->view->redirect($page . $getParameters);

	}

	public function set_timeframe() {

		if (isset($_POST['timeframe'])) {

			if ($_POST['timeframe'] == 'heute') {
				$this->view->redirect('/live');
			}

			$this->dates_from_timeframe($_POST['timeframe']);
		}

		if (isset($_POST['from']) && isset($_POST['to'])) {
			Session::set('timeframe', 'Zeitraum'); // for the Select Box

			Session::set('from', strip_tags($_POST['from']));
			Session::set('to', strip_tags($_POST['to']));
		}

		$cache = new RequestCache('temp');
		$cache->flush();


		if (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH) == '/live') {
			$this->view->back();
		}

		$this->view->redirect($_SERVER['HTTP_REFERER']);

	}

	private function dates_from_timeframe($timeframe) {

		$timeframe = strip_tags($timeframe);

		Session::set('timeframe', $timeframe); // for the Select Box

		switch ($timeframe) {
			case 'heute':
				Session::set('from', date('Y-m-d', strtotime('today')));
				Session::set('to', date('Y-m-d', strtotime('today')));
			break;
			case 'gestern':
				Session::set('from', date('Y-m-d', strtotime('yesterday')));
				Session::set('to', date('Y-m-d', strtotime('yesterday')));
			break;
			case 'letzte 7 Tage':
				Session::set('from', date('Y-m-d', strtotime('yesterday -6days')));
				Session::set('to', date('Y-m-d', strtotime('yesterday')));
			break;
			case 'letzte 30 Tage':
				Session::set('from', date('Y-m-d', strtotime('yesterday -30days')));
				Session::set('to', date('Y-m-d', strtotime('yesterday')));
			break;
			case 'letzte 365 Tage':
				Session::set('from', date('Y-m-d', strtotime('yesterday -365days')));
				Session::set('to', date('Y-m-d', strtotime('yesterday')));
			break;
			case 'aktuelle Woche':
				Session::set('from', date('Y-m-d', strtotime('monday this week')));
				Session::set('to', date('Y-m-d', strtotime('sunday this week')));
			break;
			case 'letzte Woche':
				Session::set('from', date('Y-m-d', strtotime('monday this week -1week')));
				Session::set('to', date('Y-m-d', strtotime('sunday this week -1week')));
			break;
			case 'vorletzte Woche':
				Session::set('from', date('Y-m-d', strtotime('monday this week -2week')));
				Session::set('to', date('Y-m-d', strtotime('sunday this week -2week')));
			break;
			case 'aktueller Monat':
				Session::set('from', date('Y-m-d', strtotime('first day of this month')));
				Session::set('to', date('Y-m-d', strtotime('last day of this month')));
			break;
			case 'letzter Monat':
				Session::set('from', date('Y-m-d', strtotime('first day of this month -1month')));
				Session::set('to', date('Y-m-d', strtotime('last day of this month -1month')));
			break;
			case 'vorletzter Monat':
				Session::set('from', date('Y-m-d', strtotime('first day of this month -2month')));
				Session::set('to', date('Y-m-d', strtotime('last day of this month -2month')));
			break;
			case 'letzte 3 Monate':
				Session::set('from', date('Y-m-d', strtotime('first day of this month -3month')));
				Session::set('to', date('Y-m-d', strtotime('last day of this month -1month')));
			break;
			case 'aktuelles Jahr':
				Session::set('from', date('Y-m-d', strtotime('first day of january')));
				Session::set('to', date('Y-m-d', strtotime('last day of december')));
			break;
			case 'letztes Jahr':
				Session::set('from', date('Y-m-d', strtotime('first day of january last year')));
				Session::set('to', date('Y-m-d', strtotime('last day of december last year')));
			break;
			case 'alle Daten':
				Session::set('from', '2000-01-01');
				Session::set('to', '2050-01-01');
			break;

			default:
				Session::set('from', null);
				Session::set('to', null);
			break;
		}

	}


}
