<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\auth\Auth;
use flundr\utility\Session;

class Campaigns extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
		$this->view('DefaultLayout');
		$this->models('AnalyticsShop,Charts,Orders');
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

		$campaignName = '3Monate-fuer3';

		if (PORTAL == 'MOZ') {
			$campaignName = '3Monate-kostenlos';
		}

		$campaignData = $this->AnalyticsShop->shop_pageviews_filterred_by_utm_campaign($campaignName);
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

		$this->view->orders = $this->Orders->list("order_date >= '2021-12-01' AND order_origin = 'Aboshop' AND (order_source like '%100556%' OR order_source like '%101782%')");

		$this->view->cancelled = $this->Orders->list("order_date >= '2021-12-01' AND order_origin = 'Aboshop' AND conversions.cancelled = 1 AND (order_source like '%100556%' OR order_source like '%101782%')");

		$this->view->charts = $this->Charts;
		$this->view->clickData = $clickData;
		$this->view->campaignData = $campaignData;

		$this->view->title = 'UTM - Auswertung zur Facebook Accelerator Kampagne';
		$this->view->info = '<p>Klickevents auf Bestellbutton gefiltert nach eventLabel: <b>3 Monate für 3 Euro</b><br>
		Landingpageklicks gefiltert nach Campaign: <b>3Monate-fuer3</b><br/>
		Transaktionen werden noch nicht per UTM getrackt!</p>';
		$this->view->render('campaigns/facebook');


	}

}
