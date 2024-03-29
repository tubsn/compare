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
		$this->models('Articles,Orders,DailyKPIs');
	}

	public function chefredaktion() {

		$this->Orders->from = date('Y-m-d', strtotime('yesterday'));
		$this->Orders->to = date('Y-m-d', strtotime('today'));

		$viewData['pageviews'] = $this->Articles->top_pageviews_days_ago(1);
		$viewData['conversions'] = $this->Orders->latest_grouped();
		$viewData['conversionCount'] = $this->Orders->count();

		$this->view->render('newsletter/chefredaktion/top-articles', $viewData);

	}

	public function sport_newsletter($send = false) {

		$viewData['articles'] = $this->Articles->top_articles_by_ressort_and_days_ago('sport,energie-cottbus',7);

		if (empty($viewData['articles'])) {
			throw new \Exception("Keine Sport-Artikel in diesem Zeitraum gefunden");
		}

		$infoMail = new Email();
		$infoMail->subject = 'Sport-Artikel Rückblick - ' . PORTAL;
		$infoMail->to = [
			'jan.lehmann@lr-online.de', 'frank.noack@lr-online.de', 'josephine.japke@lr-online.de',
			'cvoigt@moz.de', 'matthias.haack@ruppiner-anzeiger.de', 'uwuttke@moz.de', 'kbeiszer@moz.de', 'hroessler@moz.de', 'skretschmer@moz.de'
		];

		$infoMail->cc = ['oht@lr-online.de'];

		if ($send) {
			$infoMail->send('newsletter/sport-review', $viewData);
		}

		$this->view->render('newsletter/sport-review', $viewData);

	}


	public function nachdreh_alert($send = false) {

		$scoreLimit = 85;

		if (PORTAL == 'MOZ') {
			$scoreLimit = 100;
		}

		$this->Articles->from = date('Y-m-d', strtotime('-3days'));
		$this->Articles->to = date('Y-m-d', strtotime('-1days'));
		$viewData['articles'] = $this->Articles->score_articles($scoreLimit);

		if (empty($viewData['articles'])) {
			throw new \Exception("Keine Artikel in diesem Zeitraum gefunden");
		}

		$yesterday = date('Y-m-d', strtotime('yesterday'));

		$this->DailyKPIs->from = $yesterday;
		$this->DailyKPIs->to = $yesterday;
		$viewData['stats'] = $this->DailyKPIs->stats();

		$this->Orders->from = $yesterday;
		$this->Orders->to = $yesterday;
		$viewData['stats']['conversions'] = $this->Orders->count();

		$viewData['stats']['date'] = $yesterday;
		$viewData['stats']['weekday'] = date('w', strtotime('yesterday'));

		$infoMail = new Email();
		$infoMail->subject = PORTAL . '- Nachdreh-Alert am ' . TAGESNAMEN[date('w', strtotime('today'))];
		$infoMail->to = ['oht@lr.de', 'twinkler@moz.de', 'robert.suesse@lr.de'];
		$infoMail->cc = ['sebastian.butt@lr-online.de'];

		if ($send) {
			$infoMail->send('newsletter/nachdreh-alert', $viewData);
		}

		$this->view->render('newsletter/nachdreh-alert', $viewData);

	}

	public function nachdreh_alert_filtered($region = null, $send = false) {

		$regioDB = [
			'spreewald' => ['limit' => 30, 'filter' => "ressort = 'luckau' || ressort = 'luebben' || ressort = 'luebbenau'"],
		];

		if (!isset($regioDB[$region])) {
			throw new \Exception("Unbekannte Ressort Region", 404);

		}

		$this->Articles->from = date('Y-m-d', strtotime('-3days'));
		$this->Articles->to = date('Y-m-d', strtotime('-1days'));
		$viewData['articles'] = $this->Articles->score_articles($regioDB[$region]['limit'], $regioDB[$region]['filter']);

		if (empty($viewData['articles'])) {
			throw new \Exception("Keine Artikel in diesem Zeitraum gefunden");
		}

		$yesterday = date('Y-m-d', strtotime('yesterday'));
		$viewData['stats']['date'] = $yesterday;
		$viewData['stats']['weekday'] = date('w', strtotime('yesterday'));
		$viewData['stats']['region'] = ucfirst($region);

		$infoMail = new Email();
		$infoMail->subject = ucfirst($region) . ' - Nachdreh-Alert am ' . TAGESNAMEN[date('w', strtotime('today'))];
		$infoMail->to = ['harriet.stuermer@lr-online.de'];
		//$infoMail->cc = ['sebastian.butt@lr-online.de'];

		if ($send) {
			$infoMail->send('newsletter/nachdreh-alert-filtered', $viewData);
		}

		$this->view->render('newsletter/nachdreh-alert-filtered', $viewData);

	}



	public function nachdreh_alert_score($send = false) {

		$scoreLimit = 100;

		$start = date('Y-m-d', strtotime('-7days'));
		$end = date('Y-m-d', strtotime('-1days'));

		$this->Articles->from = date('Y-m-d', strtotime('-7days'));
		$this->Articles->to = date('Y-m-d', strtotime('-1days'));
		$viewData['articles'] = $this->Articles->score_articles($scoreLimit);

		if (empty($viewData['articles'])) {
			throw new \Exception("Keine Artikel in diesem Zeitraum gefunden");
		}

		$yesterday = date('Y-m-d', strtotime('yesterday'));

		$this->DailyKPIs->from = $start;
		$this->DailyKPIs->to = $end;
		$viewData['stats'] = $this->DailyKPIs->stats();

		$this->Orders->from = $start;
		$this->Orders->to = $end;
		$viewData['stats']['conversions'] = $this->Orders->count();

		$viewData['stats']['date'] = $yesterday;
		$viewData['stats']['weekday'] = date('w', strtotime('yesterday'));

		$infoMail = new Email();
		$infoMail->subject = PORTAL . ' -  Wochenbericht';
		$infoMail->to = ['blattkritik@moz.de', 'bbr.red.lr@lr.de', 'lr.med.red@lr.de'];
		$infoMail->cc = ['sebastian.butt@lr-online.de'];

		if ($send) {
			$infoMail->send('newsletter/nachdreh-alert-score', $viewData);
		}

		$this->view->render('newsletter/nachdreh-alert-score', $viewData);

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

		try {
			if (PORTAL == 'LR') {
				$this->nachdreh_alert_filtered('spreewald', true);
				echo 'Spreewald Newsletter erfolgreich versand';
			}
		} catch (\Exception $e) {
			echo '<b>Spreewald Newsletter gescheitert:</b> ' . $e->getMessage();
		}

		echo '<br>';

		echo '<h3>Versand abgeschlossen</h3>';

	}


	public function trigger_weekly_newsletter_sends() {

		echo '<h1>Newsletter - Manager</h1>';

		try {
			$this->nachdreh_alert_score(true);
			echo 'Weekly Score Newsletter erfolgreich versand';
		} catch (\Exception $e) {
			echo '<b>Weekly Score Newsletter gescheitert:</b> ' . $e->getMessage();
		}

		echo '<h3>Versand abgeschlossen</h3>';

	}


}
