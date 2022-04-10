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
		$this->models('Articles,Orders,Campaigns,Longterm,Charts,Maps');
	}


	public function list() {

		Session::set('referer', '/orders/list');

		$viewData['orders'] = $this->Orders->list_plain();
		$viewData['numberOfOrders'] = count($viewData['orders'] ?? []);
		$viewData['numberOfCancelled'] = count($this->Orders->filter_cancelled($viewData['orders']));
		$viewData['plusOnly'] = count($this->Orders->filter_plus_only($viewData['orders']));
		$viewData['aboshopOnly'] = count($this->Orders->filter_aboshop($viewData['orders']));
		$viewData['externalOnly'] = count($this->Orders->filter_external($viewData['orders']));
		$viewData['umwandlungOnly'] = count($this->Orders->filter_umwandlung($viewData['orders']));
		$viewData['averageRetention'] = $this->Orders->average($this->Orders->filter_cancelled($viewData['orders']),'retention');
		$viewData['mediatimeMax'] = max(array_column($viewData['orders'], 'customer_cancel_mediatime'));

		$this->view->title = 'Bestelldaten - Eingang';
		$this->view->render('orders/list', $viewData);

	}

	public function list_cancellations() {

		Session::set('referer', '/orders/list-cancellactions');

		$viewData['cancellations'] = $this->Orders->cancellations_plain();
		$viewData['championOnly'] = count($this->Orders->filter_cancel_segment($viewData['cancellations'],'champion'));
		$viewData['loyalOnly'] = count($this->Orders->filter_cancel_segment($viewData['cancellations'],'loyal'));

		$viewData['averageRetention'] = $this->Orders->average($this->Orders->filter_cancelled($viewData['cancellations']),'retention');

		$mtMAX = max(array_column($viewData['cancellations'], 'customer_cancel_mediatime'));
		$mtSUM = array_sum(array_column($viewData['cancellations'], 'customer_cancel_mediatime'));
		$mtAVG = $mtSUM / count($viewData['cancellations']);

		$viewData['mediatimeMax'] = 5000;

		$this->view->title = 'Kündigungsdaten - Eingang';
		$this->view->render('orders/cancellations-list', $viewData);

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

		//dd($viewData['yearlyActiveTimespan']);

		$viewData['churnSameDay'] = $this->Orders->sum_up($this->Orders->cancelled_by_retention_days('retention = 0'),'cancelled_orders');
		$viewData['churn30'] = $this->Orders->sum_up($this->Orders->cancelled_by_retention_days('retention < 31'),'cancelled_orders');
		$viewData['churn90'] = $this->Orders->sum_up($this->Orders->cancelled_by_retention_days('retention < 90'),'cancelled_orders');
		$viewData['churnAfter90'] = $this->Orders->sum_up($this->Orders->cancelled_by_retention_days('retention > 90'),'cancelled_orders');

		$this->view->charts = $this->Charts;
		$this->view->longterm = $this->Longterm->chartdata('orders');

		//dd($this->view->longterm);

		$this->view->render('orders/cancellations', $viewData);

	}

	public function map_local($cancelled = false) {

		Session::set('referer', '/orders/map/local');

		$datasets = count($this->Orders->group_by('customer_postcode') ?? 0);
		$this->view->title = 'Verteilung von Käufen nach Postleitzahl - Datensätze: ' . $datasets;

		if ($cancelled) {
			$this->view->showCancelled = true;
			$this->view->title = 'Kündigerquote nach Postleitzahl - Datensätze: ' . $datasets;
		}

		$this->view->PLZs = $this->Maps->colored_geo_orders();
		$this->view->render('orders/map-local');
	}

	public function map_germany($cancelled = false) {

		Session::set('referer', '/orders/map/germany');

		$datasets = count($this->Orders->group_by('customer_postcode') ?? 0);
		$this->view->title = 'Verteilung von Käufen nach Postleitzahl - Datensätze: ' . $datasets;

		if ($cancelled) {
			$this->view->showCancelled = true;
			$this->view->title = 'Kündigerquote nach Postleitzahl - Datensätze: ' . $datasets;
		}

		$this->view->PLZs = $this->Maps->colored_geo_orders_3steps();
		$this->view->render('orders/map-germany');
	}

	public function map_local_cancelled() {$this->map_local(true);}
	public function map_germany_cancelled() {$this->map_germany(true);}



	public function map_print_local($cancelled = false) {

		Session::set('referer', '/print/local');

		$this->view->title = 'LR Printabos nach Postleitzahl (54336 Abonennten)';

		if ($cancelled) {
			$this->view->showCancelled = true;
			$this->view->title = 'Kündiger letzte 3 Monate nach Postleitzahl';
		}

		$this->view->PLZs = $this->Maps->csv_import();
		$this->view->render('orders/map-print-local');
	}

	public function map_print_germany($cancelled = false) {

		Session::set('referer', '/print/germany');

		$this->view->title = 'LR Printabos nach Postleitzahl (54336 Abonennten)';

		if ($cancelled) {
			$this->view->showCancelled = true;
			$this->view->title = 'Kündiger letzte 3 Monate nach Postleitzahl';
		}

		$this->view->PLZs = $this->Maps->csv_import();
		$this->view->render('orders/map-print-ger');
	}

	public function map_print_local_cancelled() {$this->map_print_local(true);}
	public function map_print_germany_cancelled() {$this->map_print_germany(true);}



}
