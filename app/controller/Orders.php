<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;
use flundr\cache\RequestCache;

class Orders extends Controller {

	public function __construct() {

		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Articles,Orders,Plenigo,Campaigns,Longterm,Charts,Maps');
	}


	public function list() {

		Session::set('referer', '/orders/list');
		$this->view->title = 'Eingehende Bestellungen';

		$viewData['orders'] = $this->Orders->list_plain();

		if (empty($viewData['orders'])) {
			$this->view->render('orders/list-no-data');	die;
		}

		$viewData['numberOfOrders'] = count($viewData['orders'] ?? []);
		$viewData['numberOfCancelled'] = count($this->Orders->filter_cancelled($viewData['orders']));
		$viewData['plusOnly'] = count($this->Orders->filter_plus_only($viewData['orders']));
		$viewData['pushOnly'] = count($this->Orders->filter_push_only($viewData['orders']));
		$viewData['aboshopOnly'] = count($this->Orders->filter_aboshop($viewData['orders']));
		$viewData['yearlyOnly'] = count($this->Orders->filter_yearly($viewData['orders']));
		$viewData['externalOnly'] = count($this->Orders->filter_external($viewData['orders']));
		$viewData['umwandlungOnly'] = count($this->Orders->filter_umwandlung($viewData['orders']));
		$viewData['averageRetention'] = $this->Orders->average($this->Orders->filter_cancelled($viewData['orders']),'retention');
		$viewData['mediatimeMax'] = max(array_column($viewData['orders'], 'customer_cancel_mediatime'));

		$this->view->render('orders/list', $viewData);

	}

	public function list_by_day() {

		Session::set('referer', '/orders/list-daily');
		$this->view->title = 'Conversion-Eingang nach Tagen';

		$viewData['orders'] = $this->Orders->list_by_day();
		$viewData['orders_by_day'] = array_group_by('day', $viewData['orders']);

		$this->view->render('orders/list-daily', $viewData);

	}

	public function list_cancellations() {

		Session::set('referer', '/orders/list-cancellactions');
		$this->view->title = 'Kündigungsdaten - Eingang';

		$viewData['cancellations'] = $this->Orders->cancellations_plain();

		if (empty($viewData['cancellations'])) {
			$this->view->render('orders/list-no-data'); die;
		}

		$viewData['championOnly'] = count($this->Orders->filter_cancel_segment($viewData['cancellations'],'champion'));
		$viewData['loyalOnly'] = count($this->Orders->filter_cancel_segment($viewData['cancellations'],'loyal'));

		$viewData['averageRetention'] = $this->Orders->average($this->Orders->filter_cancelled($viewData['cancellations']),'retention');

		$mtMAX = max(array_column($viewData['cancellations'], 'customer_cancel_mediatime'));
		$mtSUM = array_sum(array_column($viewData['cancellations'], 'customer_cancel_mediatime'));
		$mtAVG = $mtSUM / count($viewData['cancellations']);

		$viewData['mediatimeMax'] = 5000;

		$this->view->render('orders/cancellations-list', $viewData);

	}

	public function list_app_orders() {
		$this->view->orders = $this->Plenigo->appstore_orders();
		$this->view->render('orders/app/list');
	}


	public function yearly_converters() {
		dump($this->Orders->count_yearly_into_default_subscription());
	}

	public function utm($field = 'campaign', $campaign = null) {

		Session::set('referer', '/orders/utm');

		if ($campaign == null) {
			$viewData['campaigns'] = $this->Campaigns->list();
			$this->view->title = 'UTM Kampagnen - Übersicht';
		}

		else {
			switch ($field) {
				case 'campaign': $field = 'utm_campaign';break;
				case 'medium': $field = 'utm_medium';break;
				case 'source': $field = 'utm_source';break;
				default: $field = 'utm_campaign'; break;
			}

			$this->Campaigns->from = '2000-01-01';
			$this->Campaigns->to = '2050-01-01';
			$viewData['campaigns'] = $this->Campaigns->filter_campaign_field($campaign, $field);
			$this->view->title = 'Gesamtzeitraum: ' . $field . ' - ' . $campaign;
		}

		$viewData['numberOfCampaigns'] = count($viewData['campaigns'] ?? []);
		$viewData['numberOfCancelled'] = count($this->Campaigns->filter_cancelled($viewData['campaigns']));
		$viewData['averageRetention'] = $this->Orders->average($this->Campaigns->filter_cancelled($viewData['campaigns']),'retention');

		$this->view->info = 'Daten aus Google Analytics (ohne Sampling) - Nutzer ohne Tracking-Consent sind ausgeschlossen.';
		$this->view->render('orders/list-campaigns', $viewData);

	}

	public function clustered() {

		Session::set('referer', '/orders');

		$this->view->title = 'Bestell- und Kündiger Statistiken';
		$this->view->info = null;

		$viewData['orders'] = $this->Orders->list();
		$viewData['numberOfOrders'] = count($viewData['orders'] ?? []);
		$viewData['numberOfCancelled'] = count($this->Orders->filter_cancelled($viewData['orders']));
		$viewData['churnSameDay'] = $this->Orders->sum_up($this->Orders->cancelled_by_retention_days('retention = 0'),'cancelled_orders');

		if ($viewData['orders']) {
			$viewData['cancelQuote'] = round(($viewData['numberOfCancelled'] / $viewData['numberOfOrders']) * 100, 1);
		} else {$viewData['cancelQuote'] = null;}

		$viewData['charts'] = $this->Charts;

		$viewData['plusOnly'] = count($this->Orders->filter_plus_only($viewData['orders']));
		$viewData['aboshopOnly'] = count($this->Orders->filter_aboshop($viewData['orders']));
		$viewData['externalOnly'] = count($this->Orders->filter_external($viewData['orders']));
		$viewData['umwandlungOnly'] = count($this->Orders->filter_umwandlung($viewData['orders']));
		$viewData['averageRetention'] = $this->Orders->average($this->Orders->filter_cancelled($viewData['orders']),'retention');

		$viewData['type'] = $this->Orders->group_by_combined('article_type');
		$viewData['tag'] = $this->Orders->group_by_combined('article_tag');
		$viewData['audience'] = $this->Orders->group_by_combined('article_audience');
		$viewData['ressorts'] = $this->Orders->group_by_combined('article_ressort');
		$viewData['origin'] = $this->Orders->group_by_combined('order_origin');
		$viewData['utm_source'] = $this->Orders->group_by_combined('utm_source');
		$viewData['utm_medium'] = $this->Orders->group_by_combined('utm_medium');
		$viewData['utm_campaign'] = $this->Orders->group_by_combined('utm_campaign');
		$viewData['source'] = $this->Orders->group_by_combined('ga_source');
		$viewData['city'] = $this->Orders->group_by_combined('customer_city');
		$viewData['plz'] = $this->Orders->group_by_combined('customer_postcode');
		$viewData['gender'] = $this->Orders->group_by_combined('customer_gender');
		$viewData['product'] = $this->Orders->group_by_combined('order_title');
		$viewData['internalTitle'] = $this->Orders->group_by_combined('subscription_internal_title');
		$viewData['price'] = $this->Orders->group_by_combined('order_price');
		$viewData['payment'] = $this->Orders->group_by_combined('order_payment_method');
		$viewData['gender'] = $this->Orders->group_by_combined('customer_gender');
		$this->view->render('orders/stats', $viewData);

	}


	public function customer_behavior() {

		Session::set('referer', '/orders/behavior');

		$this->view->title = 'Kundenverhalten und Kündigerentwicklung';
		$this->view->info = null;

		$viewData['orders'] = $this->Orders->list();

		$viewData['numberOfOrders'] = count($viewData['orders'] ?? []);
		$viewData['numberOfCancelled'] = count($this->Orders->filter_cancelled($viewData['orders']));
		$viewData['firstDayChurners'] = $this->Orders->group_by('article_ressort', 'retention = 0');

		$reasonData = $this->Orders->group_by('cancellation_reason');
		if (!empty($reasonData)) {
			$reasons = [];
			foreach ($reasonData as $key => $value) {
				if (empty($key)) {continue;}
				$reasons[CANCELLATION_REASON[$key]] = $value;
			}
			$reasonChart = $this->Charts->convert($reasons);
			$reasonChart['metrics'] = implode(',' ,$reasons);
			$viewData['reasons_chart'] = $reasonChart;

			$noAwnserReasons = $reasonData[0] ?? null;
			$viewData['reasons'] = array_sum($reasonData) - $noAwnserReasons;
		}
		else {
			$viewData['reasons_chart'] = null;
			$viewData['reasons'] = 0;
		}

		if ($viewData['orders']) {
			$viewData['cancelQuote'] = round(($viewData['numberOfCancelled'] / $viewData['numberOfOrders']) * 100, 1);
		} else {$viewData['cancelQuote'] = null;}

		$viewData['plusOnly'] = count($this->Orders->filter_plus_only($viewData['orders']));
		$viewData['aboshopOnly'] = count($this->Orders->filter_aboshop($viewData['orders']));
		$viewData['externalOnly'] = count($this->Orders->filter_external($viewData['orders']));
		$viewData['averageRetention'] = $this->Orders->average($this->Orders->filter_cancelled($viewData['orders']),'retention');

		$viewData['monthlyActiveTimespan'] = $this->Charts->convert($this->Orders->customers_timespan('month'));

		$yearlyCustomers = $this->Orders->customers_timespan('year',false);
		$viewData['activeAfterOneYear'] = $yearlyCustomers[1]['orders'] ?? 0;
		$viewData['yearlyActiveTimespan'] = $this->Charts->convert_as_integer($yearlyCustomers);

		$viewData['churnSameDay'] = $this->Orders->sum_up($this->Orders->cancelled_by_retention_days('retention = 0'),'cancelled_orders');
		$viewData['churn30'] = $this->Orders->sum_up($this->Orders->cancelled_by_retention_days('retention < 31'),'cancelled_orders');
		$viewData['churn90'] = $this->Orders->sum_up($this->Orders->cancelled_by_retention_days('retention < 90'),'cancelled_orders');
		$viewData['churnAfter90'] = $this->Orders->sum_up($this->Orders->cancelled_by_retention_days('retention > 90'),'cancelled_orders');

		$this->view->charts = $this->Charts;
		$this->view->longterm = $this->Longterm->chartdata('orders');

		$this->view->render('orders/cancellations', $viewData);

	}


}
