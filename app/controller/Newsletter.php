<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\message\Email;
use flundr\auth\Auth;

class Newsletter extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
		$this->view('BlankLayout');
		$this->models('Articles,Orders,GlobalKPIs');
	}

	public function chefredaktion() {

		$this->Orders->from = date('Y-m-d', strtotime('yesterday'));
		$this->Orders->to = date('Y-m-d', strtotime('today'));

		$viewData['pageviews'] = $this->Articles->top_pageviews_days_ago(1);
		$viewData['conversions'] = $this->Orders->latest_grouped();
		$viewData['conversionCount'] =$this->Orders->count();

		$this->view->render('newsletter/chefredaktion/top-articles', $viewData);

	}

	public function sport_newsletter($send = false) {

		$viewData['articles'] = $this->Articles->top_articles_by_ressort_and_days_ago('sport,energie-cottbus',4);

		if (empty($viewData['articles'])) {
			throw new \Exception("Keine Sport-Artikel in diesem Zeitraum gefunden");
		}

		$infoMail = new Email();
		$infoMail->subject = 'Sport-Artikel Rückblick';
		$infoMail->to = ['jan.lehmann@lr-online.de', 'frank.noack@lr-online.de', 'josephine.japke@lr-online.de'];
		$infoMail->cc = ['sebastian.butt@lr-online.de', 'oht@lr-online.de', 'mariell.begemann@lr-online.de'];

		if ($send) {
			$infoMail->send('newsletter/sport-review', $viewData);
		}

		$this->view->render('newsletter/sport-review', $viewData);

	}


	public function nachdreh_alert($send = false) {

		$this->Articles->from = date('Y-m-d', strtotime('-3days'));
		$this->Articles->to = date('Y-m-d', strtotime('-1days'));
		$viewData['articles'] = $this->Articles->score_articles(100);

		if (empty($viewData['articles'])) {
			throw new \Exception("Keine Artikel in diesem Zeitraum gefunden");
		}

		$yesterday = date('Y-m-d', strtotime('yesterday'));

		$this->GlobalKPIs->from = $yesterday;
		$this->GlobalKPIs->to = $yesterday;
		$viewData['stats'] = $this->GlobalKPIs->stats();

		$this->Orders->from = $yesterday;
		$this->Orders->to = $yesterday;
		$viewData['stats']['conversions'] = $this->Orders->count();

		$this->Articles->from = $yesterday;
		$this->Articles->to = $yesterday;
		$viewData['stats']['subscribers'] = $this->Articles->sum('subscribers');

		$viewData['stats']['date'] = $yesterday;
		$viewData['stats']['weekday'] = date('w', strtotime('yesterday'));

		$infoMail = new Email();
		$infoMail->subject = 'Nachdreh-Alert am ' . TAGESNAMEN[date('w', strtotime('today'))];
		$infoMail->to = ['oht@lr-online.de', 'daniel.steiger@lr-online.de', 'susann.michalk@lr-online.de'];
		$infoMail->cc = ['sebastian.butt@lr-online.de', 'robert.suesse@lr-online.de', 'mariell.begemann@lr-online.de'];

		if ($send) {
			$infoMail->send('newsletter/nachdreh-alert', $viewData);
		}

		$this->view->render('newsletter/nachdreh-alert', $viewData);

	}


	public function trigger_newsletter_sends() {

		echo '<h1>Newsletter - Manager</h1>';

		try {
			$this->sport_newsletter(true);
			echo 'Sport Newsletter erfolgreich versand';
		} catch (\Exception $e) {
			echo '<b>Sport Newsletter gescheitert:</b> ' . $e->getMessage();
		}

		echo '<br>';

		try {
			$this->nachdreh_alert(true);
			echo 'Nachdreh-Alert erfolgreich versand';
		} catch (\Exception $e) {
			echo '<b>Nachdreh-Alert gescheitert:</b> ' . $e->getMessage();
		}

		echo '<h3>Versand abgeschlossen</h3>';

	}

}
