<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\auth\Auth;
use flundr\utility\Session;
use flundr\cache\RequestCache;

class Articles extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
		$this->view('DefaultLayout');
		$this->view->navigation = 'navigation/article-menu';
		$this->models('Articles,Analytics,Stats');
	}


	public function retresco($id) {

		// Test Stuff
		$import = new \app\importer\RetrescoImport();
		$output = $import->collect($id);
		dd($output);

	}

	public function detail($id) {
		$viewData['article'] = $this->Articles->get($id);

		if (empty($viewData['article'])) {
			throw new \Exception("Artikel nicht gefunden oder noch nicht importiert", 404);
		}

		if ($viewData['article']['pageviews'] == null) {
			$pubDate = formatDate($viewData['article']['pubdate'], 'Y-m-d');
			$this->refresh_stats($id, $pubDate);
			// Reload Article Data
			$viewData['article'] = $this->Articles->get($id);
		}

		// Returns Stats aggregated by Day for this ID
		$viewData['stats'] = $this->Stats->by_id($id);
		$viewData['chart'] = $this->Stats->convert_to_chart_data($viewData['stats']);

		$ressortStats = $this->Articles->stats_grouped_by('ressort')[$viewData['article']['ressort']];
		//$typeStats = $this->Articles->stats_grouped_by('type')[$viewData['article']['type']] ?? null;

		$ressortPageviewsAverage = $ressortStats['pageviews'] / $ressortStats['artikel'];
		$pageViewsToRessort = ($viewData['article']['pageviews'] / $ressortPageviewsAverage * 100);

		$viewData['ressortAverage'] = round($ressortPageviewsAverage);
		$viewData['ressortRank'] = round($pageViewsToRessort);

		$this->view->render('pages/detail', $viewData);
	}

	public function refresh($id) {
		$pubDate = $this->Articles->get($id,['pubdate'])['pubdate'];
		$pubDate = formatDate($pubDate, 'Y-m-d');
		$this->refresh_stats($id, $pubDate);
		$this->view->redirect('/artikel/' . $id);
	}

	public function delete($id) {
		if (!Auth::logged_in()) {$this->view->redirect('/login');}
		$this->Articles->delete($id);
		$this->Stats->delete($id);
		$this->view->redirect('/');
	}

	public function edit($id) {
		$type = strip_tags($_POST['type']);
		if ($type == '0') {$type = null;}
		$this->Articles->set(['type' => $type], $id);
	}

	public function set_timeframe() {

		if ($_POST['timeframe']) {
			$this->dates_from_timeframe($_POST['timeframe']);
		}

		if ($_POST['from'] && $_POST['to']) {
			Session::set('timeframe', 'Zeitraum'); // for the Select Box

			Session::set('from', strip_tags($_POST['from']));
			Session::set('to', strip_tags($_POST['to']));
		}

		$cache = new RequestCache('temp');
		$cache->flush();

		$this->view->redirect($_SERVER['HTTP_REFERER']);

	}


	private function dates_from_timeframe($timeframe) {

		$timeframe = strip_tags($timeframe);

		Session::set('timeframe', $timeframe); // for the Select Box

		switch ($timeframe) {
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
			default:
				Session::set('from', null);
				Session::set('to', null);
			break;
		}

	}



	private function refresh_stats($id, $pubDate = '30daysAgo') {
		$gaData = $this->Analytics->byArticleID($id, $pubDate);
		$dailyStats = $gaData['details'];
		$lifeTimeStats = $gaData['stats'];
		$this->Articles->add_stats($lifeTimeStats,$id);
		$this->Stats->add($dailyStats,$id);
	}

}
