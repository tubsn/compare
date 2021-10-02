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
		$this->models('Articles,Orders,Campaigns,Charts');
	}


	public function list() {

		Session::set('referer', '/orders/list');

		$viewData['orders'] = $this->Orders->list_plain();
		$viewData['numberOfOrders'] = count($viewData['orders'] ?? []);
		$viewData['numberOfCancelled'] = count($this->Orders->filter_cancelled($viewData['orders']));
		$viewData['plusOnly'] = count($this->Orders->filter_plus_only($viewData['orders']));
		$viewData['externalOnly'] = count($this->Orders->filter_external($viewData['orders']));
		$viewData['averageRetention'] = $this->Orders->average($this->Orders->filter_cancelled($viewData['orders']),'retention');

		$this->view->title = 'Bestellübersicht';
		$this->view->render('orders/list', $viewData);

	}


	public function campaigns() {

		$viewData['campaigns'] = $this->Campaigns->list();
		$viewData['numberOfCampaigns'] = count($viewData['campaigns'] ?? []);
		$viewData['numberOfCancelled'] = count($this->Campaigns->filter_cancelled($viewData['campaigns']));
		$viewData['averageRetention'] = $this->Orders->average($this->Campaigns->filter_cancelled($viewData['campaigns']),'retention');

		$this->view->title = 'UTM Kampagnen - Übersicht';
		$this->view->info = 'Daten aus Google Analytics (ohne Sampling) - Nutzer ohne Tracking-Consent sind ausgeschlossen.';
		$this->view->render('orders/list-campaigns', $viewData);

	}

	public function stats() {

		Session::set('referer', '/orders');

		$this->view->title = 'Bestell- und Kündiger Statistiken';
		$this->view->info = null;

		$viewData['orders'] = $this->Orders->list();
		$viewData['numberOfOrders'] = count($viewData['orders'] ?? []);
		$viewData['numberOfCancelled'] = count($this->Orders->filter_cancelled($viewData['orders']));

		if ($viewData['orders']) {
			$viewData['cancelQuote'] = round(($viewData['numberOfCancelled'] / $viewData['numberOfOrders']) * 100, 1);
		} else {$viewData['cancelQuote'] = null;}

		$viewData['charts'] = $this->Charts;

		$viewData['plusOnly'] = count($this->Orders->filter_plus_only($viewData['orders']));
		$viewData['externalOnly'] = count($this->Orders->filter_external($viewData['orders']));
		$viewData['averageRetention'] = $this->Orders->average($this->Orders->filter_cancelled($viewData['orders']),'retention');

		$viewData['type'] = $this->Orders->group_by_combined('article_type');
		$viewData['tag'] = $this->Orders->group_by_combined('article_tag');
		$viewData['ressorts'] = $this->Orders->group_by_combined('article_ressort');
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

}
