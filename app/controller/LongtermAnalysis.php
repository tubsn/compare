<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;
use flundr\cache\RequestCache;

class LongtermAnalysis extends Controller {

	public function __construct() {
		$this->view('DefaultLayout');
		$this->models('Charts,Longterm,Orders,SalesKPIs');
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
	}

	public function overview() {

		$from = date('Y-m-d', strtotime('yesterday -3days'));
		$to = date('Y-m-d', strtotime('yesterday'));

		$lastYear = date('Y-m-d', strtotime('first day of this month -2 year -1 month'));
		$firstDate = '2021-04';

		$kpiData = $this->Longterm->kpis($lastYear);

		$kpiHistory = $this->Longterm->compare_fields_with_past($kpiData, ['pageviews','sessions','subscriberviews','avgmediatime']);
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
		$this->view->render('stats/portal-kpis');

	}

	public function brandenburg() {

		$orderData = $this->Longterm->portal_orders();
		$salesData = $this->Longterm->portal_SalesKPIs();
		$kpiData = $this->Longterm->portal_KPIs();

		// LR has one Month more then the other Portals :/ this causes trouble with the comparisment
		unset($salesData['LR']['2020-08']);

		$swp['order'] = $this->Charts->convert($orderData['SWP']);
		$moz['order'] = $this->Charts->convert($orderData['MOZ']);
		$lr['order'] = $this->Charts->convert($orderData['LR']);

		//dd($lr['order']);

		$swp['kpi'] = $this->Charts->convert($kpiData['SWP']);
		$moz['kpi'] = $this->Charts->convert($kpiData['MOZ']);
		$lr['kpi'] = $this->Charts->convert($kpiData['LR']);

		$swp['sales'] = $this->Charts->convert($salesData['SWP']);
		$moz['sales'] = $this->Charts->convert($salesData['MOZ']);
		$lr['sales'] = $this->Charts->convert($salesData['LR']);

		$lastYear = date('Y-m-d', strtotime('first day of this month -2 year -1 month'));
		$firstDate = '2021-04';

		$this->view->salesData = $salesData;
		$this->view->orderData = $orderData;
		$this->view->kpiData = $kpiData;

		$this->view->swp = $swp;
		$this->view->moz = $moz;
		$this->view->lr = $lr;

		$this->view->charts = $this->Charts;

		$this->view->title = 'Plus-Angebote der Mediengruppe Brandenburg';
		$this->view->templates['footer'] = null;
		$this->view->render('stats/portals-bbg');


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

	public function started_payment() {
		dd($this->Orders->active_after_days(190));
	}


}
