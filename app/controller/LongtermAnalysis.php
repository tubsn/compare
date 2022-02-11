<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;
use flundr\cache\RequestCache;

class LongtermAnalysis extends Controller {

	public function __construct() {

		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Charts,Longterm,Orders,SalesKPIs');
	}

	public function overview() {

		$from = date('Y-m-d', strtotime('yesterday -3days'));
		$to = date('Y-m-d', strtotime('yesterday'));

		$lastYear = date('Y-m-d', strtotime('first day of this month -2 year -1 month'));
		$firstDate = '2021-04';

		$kpiData = $this->Longterm->kpis($lastYear);

		$kpiHistory = $this->Longterm->compare_fields_with_past($kpiData, ['pageviews','sessions','subscribers','avgmediatime']);
		$kpiData = $this->Longterm->remove_before($kpiData, '2020-11');

		$orderData = $this->Longterm->orders($lastYear);
		$orderHistory = $this->Longterm->compare_fields_with_past($orderData, ['orders']);
		$orderData = $this->Longterm->remove_before($orderData, $firstDate);

		$salesData = $this->SalesKPIs->list();

		$this->view->orders = $this->Charts->convert($orderData);
		$this->view->kpis = $this->Charts->convert($kpiData);
		$this->view->sales = $this->Charts->convert($salesData);

		$this->view->orderHistory = $orderHistory;
		$this->view->kpiHistory = $kpiHistory;
		$this->view->salesData = $salesData;

		$this->view->charts = $this->Charts;
		$this->view->difference = array($this, 'view_calc_difference');

		$this->view->title = 'Langzeit Analysen';
		$this->view->templates['footer'] = null;
		$this->view->render('stats/longterm');

	}

	public function view_calc_difference($current, $historic) {

		$class = '';
		$percent = percentage($current, $historic,1);
		if ($percent != 0) {
			$class = 'negative';
			$percent = round($percent - 100,1);
			$percent .= '&thinsp;%';

			if (strpos($percent, '-') !== 0) {
				$percent = '+' . $percent;
				$class = 'positive';
			}

		}

		return '<span style="font-family:var(--font-highlight)" class="' . $class . '">' . $percent . '</span>';

	}


	public function all_portals() {

		$orderData = $this->Longterm->portal_orders();
		$kpiData = $this->Longterm->portal_KPIs();
		$combinedData = $this->Longterm->combine_portal_data($orderData, $kpiData);

		$swp['order'] = $this->Charts->convert($orderData['SWP']);
		$moz['order'] = $this->Charts->convert($orderData['MOZ']);
		$lr['order'] = $this->Charts->convert($orderData['LR']);

		$swp['kpi'] = $this->Charts->convert($kpiData['SWP']);
		$moz['kpi'] = $this->Charts->convert($kpiData['MOZ']);
		$lr['kpi'] = $this->Charts->convert($kpiData['LR']);

		$swp['quotes'] = $this->Charts->convert($combinedData['SWP']);
		$moz['quotes'] = $this->Charts->convert($combinedData['MOZ']);
		$lr['quotes'] = $this->Charts->convert($combinedData['LR']);

		$this->view->orderData = $orderData;
		$this->view->kpiData = $kpiData;

		$this->view->swp = $swp;
		$this->view->moz = $moz;
		$this->view->lr = $lr;

		$this->view->charts = $this->Charts;

		$this->view->title = 'Compare -> Compare² -> Compare³ - Portal-Vergleich';
		$this->view->templates['footer'] = null;
		$this->view->render('stats/portals');

	}


	public function provide_portal_orders() {
		header('Access-Control-Allow-Origin: *');
		$this->view->json($this->Longterm->orders());
	}

	public function provide_portal_kpis() {
		header('Access-Control-Allow-Origin: *');
		$this->view->json($this->Longterm->kpis());
	}

	public function provide_combined_kpis() {

		$out['kpis'] = $this->Longterm->portal_KPIs();
		$out['orders'] = $this->Longterm->portal_orders();
		$out['quotes'] = $this->Longterm->combine_portal_data($out['orders'], $out['kpis']);

		header('Access-Control-Allow-Origin: *');
		$this->view->json($out);

	}



	public function started_payment() {

		dd($this->Longterm->started_payment());

	}



	public function churnAPI($product = 'LR+__LR P08 3M PP3') {

		//$data = $this->Orders->cancelled_by_retention_days();
		dd($data);
		//if (!is_null($filter)) {$filter = 'AND ' . strip_tags($filter);}

		$retention = intval($_GET['days'] ?? 30);

		$this->Orders->from = '2021-06-01';
		$this->Orders->to = '2021-06-31';


		//dd($this->Longterm->monthly_date_range($this->Orders->from, $this->Orders->to));

		$orders = $this->Orders->count("(subscription_internal_title = '$product')");


		$cancelled = $this->Orders->cancelled_by_retention_days("retention <= $retention AND subscription_internal_title = '$product'");
		$cancelled = array_sum(array_column($cancelled,'cancelled_orders'));

		$this->view->retention = $retention;
		$this->view->product = $product;
		$this->view->orders = $orders;
		$this->view->cancelled = $cancelled;
		$this->view->quote = round($cancelled / $orders * 100,2);

		$this->view->products = $this->Orders->product_titles();
		$this->view->render('orders/churnapi');

	}


}
