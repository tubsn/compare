<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;

class Push extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
		$this->view('DefaultLayout');
		$this->models('Cleverpush,Charts');
	}



	public function today() {
		$notifications = $this->Cleverpush->today();
		$this->view->today = true;
		$this->view->notifications = $notifications;
		$this->view->stats = $this->Cleverpush->stats();
		$this->view->title = 'Heutige Push-Meldungen über Cleverpush';
		$this->view->info = '<b>Hinweis:</b> Pageviews und Conversions beziehen sich auf Gesamtdaten des gepushten Artikels! Es werden maximal 1.000 Einträge angezeigt.<br>Die <b>OptOuts sind nur ein Richtwert</b>, die Datenbasis ist ungenau und bezieht sich auf Austragungen von Nutzern bis zu dieser Pushmeldung. <a href="/push/stats">Statistiken (Beta - Berechnung dauert bis 3min)</a>';
		$this->view->render('articles/push/list');
	}

	public function list() {
		$notifications = $this->Cleverpush->list(1000);
		$this->view->today = false;
		$this->view->notifications = $notifications;
		$this->view->title = 'Push-Meldungen über Cleverpush';
		$this->view->info = '<b>Hinweis:</b> Pageviews und Conversions beziehen sich auf Gesamtdaten des gepushten Artikels! Es werden maximal 1.000 Einträge angezeigt.<br>Die <b>OptOuts sind nur ein Richtwert</b>, die Datenbasis ist ungenau und bezieht sich auf Austragungen von Nutzern bis zu dieser Pushmeldung.';
		$this->view->render('articles/push/list');
	}

	public function detail($id) {
		$notification = $this->Cleverpush->get($id);

		$this->view->charts = $this->Charts;
		$this->view->hourlyStats = $this->Charts->convert($notification['hourly_stats']);
		$this->view->notification = $notification;
		$this->view->title = $notification['title'];
		$this->view->render('articles/push/detail');
	}

	public function stats() {
		$this->view->hours = $this->Charts->convert($this->Cleverpush->click_stats());
		$this->view->charts = $this->Charts;
		$this->view->title = 'Klickraten nach Uhrzeit';
		$this->view->info = 'Klicks und Ausspielungen sind abhängig davon welche Kanäle bespielt werden (mehr Kanäle = mehr potentielle Klicks)';
		$this->view->render('articles/push/stats');
	}


}
