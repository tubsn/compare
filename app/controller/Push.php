<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;

class Push extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
		$this->view('DefaultLayout');
		$this->models('Cleverpush,Charts,Pushes');
	}


	public function stats_by($column = 'topic') {

		$availableColumns = ['topic', 'audience', 'tag'];
		if (!in_array($column, $availableColumns)) {
			throw new \Exception("Column not Available for grouping", 404);
		}

		$notifications = $this->Pushes->clickrate_grouped_by($column);
		$notificationsGrouped = $this->Pushes->clickrate_and_time($column);

		$this->view->generalStats = $this->Pushes->stats();
		$this->view->timeStats = $this->Charts->convert($this->Pushes->pushes_per_day(),1);

		$this->view->stats = $this->Charts->convert($notifications);
		$this->view->statsGrouped = $this->Charts->convert($notificationsGrouped,1);
		$this->view->charts = $this->Charts;

		//dd($this->view->statsGrouped);

		$this->view->title = 'Pushstatistiken';
		$this->view->info = 'In die Statistiken fließen App sowie Web Pushes ein. Klicks und Ausspielungen sind abhängig davon welche Kanäle bespielt werden (mehr Kanäle = mehr potentielle Klicks)';
		$this->view->render('articles/push/stats-grouped');

	}

	public function time_stats() {

		dd($this->Pushes->pushes_per_day());

	}


	public function development() {

		$topics = $this->Pushes->distinct_topics();
		$this->view->topics = $topics;

		$selected = $_GET['topic'] ?? $topics[0] ?? null;

		if ($selected) {
			if (!in_array($selected, $topics)) {
				throw new \Exception("Topic invalid", 404);
			}
		}

		$stats = $this->Pushes->clickrate_development($selected);

		$this->view->title = 'Push-Klickraten: Entwicklung nach Themengebiet';
		$this->view->selected = $selected;
		$this->view->charts = $this->Charts;
		$this->view->stats = $this->Charts->convert($stats);
		$this->view->render('articles/push/experiments');

	}


	public function today() {
		$notifications = $this->Cleverpush->today();
		$this->view->today = true;
		$this->view->notifications = $notifications;
		$this->view->stats = $this->Cleverpush->stats();
		$this->view->title = 'Heutige Push-Meldungen über Cleverpush';
		$this->view->info = '<b>Hinweis:</b> Pageviews und Conversions beziehen sich auf Gesamtdaten des gepushten Artikels! Es werden maximal 1.000 Einträge angezeigt.<br>Die <b>OptOuts sind nur ein Richtwert</b>, die Datenbasis ist ungenau und bezieht sich auf Austragungen von Nutzern bis zu dieser Pushmeldung.';
		$this->view->render('articles/push/list');
	}

	public function today_app() {
		$this->view->app = true;		
		$this->Cleverpush->switch_to_app();
		$this->today();
	}

	public function list() {
		$notifications = $this->Cleverpush->list(1000);
		$this->view->today = false;
		$this->view->notifications = $notifications;
		$this->view->title = 'Push-Meldungen über Cleverpush';
		$this->view->info = '<b>Hinweis:</b> Pageviews und Conversions beziehen sich auf Gesamtdaten des gepushten Artikels! Es werden maximal 1.000 Einträge angezeigt.<br>Die <b>OptOuts sind nur ein Richtwert</b>, die Datenbasis ist ungenau und bezieht sich auf Austragungen von Nutzern bis zu dieser Pushmeldung.';
		$this->view->render('articles/push/list');
	}

	public function list_app() {
		$this->view->app = true;
		$this->Cleverpush->switch_to_app();
		$this->list();
	}

	public function detail($id) {
		$notification = $this->Cleverpush->get($id);

		$this->view->charts = $this->Charts;
		$this->view->hourlyStats = $this->Charts->convert($notification['hourly_stats']);
		$this->view->notification = $notification;
		$this->view->title = $notification['title'];
		$this->view->render('articles/push/detail');
	}

	public function import() {
		$from = $_POST['from'];
		$to = $_POST['to'];
		$channel = $_POST['channel'];

		$imported = $this->Pushes->import($from, $to, $channel);
		table_dump($imported);
	}

	public function stats() {
		$this->view->hours = $this->Charts->convert($this->Cleverpush->click_stats());
		$this->view->charts = $this->Charts;
		$this->view->title = 'Klickraten nach Uhrzeit';
		$this->view->info = 'Klicks und Ausspielungen sind abhängig davon welche Kanäle bespielt werden (mehr Kanäle = mehr potentielle Klicks)';
		$this->view->render('articles/push/stats');
	}


}
