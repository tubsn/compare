<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\auth\Auth;
use flundr\cache\RequestCache;
use app\importer\ArticleImport;

class Epaper extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
		$this->view('DefaultLayout');
		$this->models('Epaper,Charts');
		$this->view->ressortNavi = $this->Epaper->distinct_ressorts();
	}

	public function list() {

		$this->view->dailyStats = $this->Epaper->ressort_stats();
		
		$this->view->sessions = array_sum(array_column($this->view->dailyStats, 'sessions'));
		$this->view->sessionsArticle = array_sum(array_column($this->view->dailyStats, 'sessions_article'));
		$this->view->pageviews = array_sum(array_column($this->view->dailyStats, 'pageviews'));
		$this->view->pageviewsArticle = array_sum(array_column($this->view->dailyStats, 'pageviews_article'));

		$this->view->title = 'Web ePaper - Artikel kummuliert';
		$this->view->info = 'Artikel die in mehreren Ausgaben platziert sind, werden auf der <b>Übersichtsseite kummuliert nach Überschriften</b>
			dargestellt.<br/>Artikel wie "Polizei" oder "Kommentar" die <b>mehrmals Täglich im mehreren Ausgaben</b> im Blatt erscheinen sind ausgeblendet.
			Es werden nur Klicks auf das <b>Web ePaper</b> getrackt!';
		$this->view->articles = $this->Epaper->articles();
		$this->view->chart = $this->Charts->get('epaper_stats_by_date');

		$this->view->render('epaper/list');
	}


	public function ressort($ressort) {

		$this->view->dailyStats = $this->Epaper->ressort_stats($ressort);

		$this->view->sessions = array_sum(array_column($this->view->dailyStats, 'sessions'));
		$this->view->sessionsArticle = array_sum(array_column($this->view->dailyStats, 'sessions_article'));
		$this->view->pageviews = array_sum(array_column($this->view->dailyStats, 'pageviews'));
		$this->view->pageviewsArticle = array_sum(array_column($this->view->dailyStats, 'pageviews_article'));

		$this->view->chart = $this->Charts->get('epaper_stats_by_date', $ressort);

		$speakingRessort = $this->Epaper->ressort_speaking_name('/' . $ressort . '/');
		$this->view->title = 'Web ePaper - Ausgabe ' . $speakingRessort;
		$this->view->articles = $this->Epaper->by_ressort($ressort);
		$this->view->render('epaper/list');
	}


	public function stats() {

		$this->view->title = 'Web ePaper - Statistiken';
		$this->view->dailyStats = $this->Epaper->ressort_stats();

		$this->view->sessions = array_sum(array_column($this->view->dailyStats, 'sessions'));
		$this->view->sessionsArticle = array_sum(array_column($this->view->dailyStats, 'sessions_article'));
		$this->view->pageviews = array_sum(array_column($this->view->dailyStats, 'pageviews'));
		$this->view->pageviewsArticle = array_sum(array_column($this->view->dailyStats, 'pageviews_article'));

		//$this->view->epaperBtnClicksByDate = $this->Epaper->clicks_on_epaper_btn();
		//$this->view->epaperBtnClicks = array_sum($this->view->epaperBtnClicksByDate);

		$this->view->charts = $this->Charts;

		$this->view->ep = $this->Epaper;
		$this->view->render('epaper/stats');
	}


	public function detail($id) {

		$this->view->title = 'ePaper - Artikel Detail';
		$this->view->article = $this->Epaper->by_article_id($id);
		$this->view->render('epaper/detail');

	}

	public function import() {

		$this->Epaper->from = date('Y-m-d', strtotime('first day of this month'));
		$this->Epaper->to = date('Y-m-d', strtotime('yesterday'));

		//$this->Epaper->from = '2020-01-01';
		//$this->Epaper->to = '2022-01-16';

		$this->Epaper->import_ressort_stats_to_db();
		$this->Epaper->import_event_clicks_to_db();

	}


}
