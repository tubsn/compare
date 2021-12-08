<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\auth\Auth;
use flundr\utility\Session;

class Campaigns extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
		$this->view('DefaultLayout');
		$this->models('AnalyticsShop,Charts');
	}


	public function all($filter = null) {

		$campaignData = $this->AnalyticsShop->shop_pageviews_filterred_by_utm_campaign($filter);

		$this->view->charts = $this->Charts;
		$this->view->campaignData = $campaignData;

		$this->view->title = 'UTM - Kampagnen Daten (ungesampled)';
		$this->view->info = '<p>Test</p>';
		$this->view->render('campaigns/list');

	}

	public function fb_accelerator() {

		$campaignData = $this->AnalyticsShop->shop_pageviews_filterred_by_utm_campaign('3Monate-fuer3');
		$clickData = $this->AnalyticsShop->shop_events_filterred_by_label('3 Monate für 3 Euro');

		$conversions = $this->AnalyticsShop->utm_campaigns_shop();
		$conversions = array_map(function ($set) {
			if (isset($set['Transactionid'])) {
				$set['Transactionid'] = '<a href="/orders/'.$set['Transactionid'].'">'.$set['Transactionid'].'</a>';
				unset($set['Transactions']);
			} return $set; }, $conversions);

		$grouped = $this->AnalyticsShop->utm_campaigns_shop(true);

		$this->view->conversions = $conversions;
		$this->view->conversionsGrouped = $grouped;

		$this->view->charts = $this->Charts;
		$this->view->clickData = $clickData;
		$this->view->campaignData = $campaignData;

		$this->view->title = 'UTM - Auswertung zur Facebook Accelerator Kampagne';
		$this->view->info = '<p>Klickevents auf Bestellbutton gefiltert nach eventLabel: <b>3 Monate für 3 Euro</b><br>
		Landingpageklicks gefiltert nach Campaign: <b>3Monate-fuer3</b><br/>
		Transaktionen werden noch nicht getrackt!</p>';
		$this->view->render('campaigns/facebook');


	}

}
