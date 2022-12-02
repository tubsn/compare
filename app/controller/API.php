<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;
use flundr\cache\RequestCache;

class API extends Controller {

	public function __construct() {
		$this->view('DefaultLayout');
		$this->models('Longterm,Articles,Orders,DailyKPIs');
		header('Access-Control-Allow-Origin: *');
	}

	public function provide_portal_orders() {
		$this->view->json($this->Longterm->orders());
	}

	public function provide_portal_kpis() {
		$this->view->json($this->Longterm->kpis());
	}

	public function provide_portal_sales() {
		$this->view->json($this->Longterm->sales());
	}

	public function provide_combined_kpis() {
		$out['kpis'] = $this->Longterm->portal_KPIs();
		$out['orders'] = $this->Longterm->portal_orders();
		$out['quotes'] = $this->Longterm->combine_portal_data($out['orders'], $out['kpis']);
		$this->view->json($out);
	}

	public function weekly($from = null, $to = null) {

		$this->Articles->from = $from;
		$this->Articles->to = $to;

		$this->Orders->from = $from;
		$this->Orders->to = $to;

		$this->DailyKPIs->from = $from;
		$this->DailyKPIs->to = $to;

		$articles = $this->Articles->count();
		$audienceArticles = $this->Articles->count_with_filter('audience is not null');
		$ghostArticles = $this->Articles->count_with_filter('(conversions IS NULL OR conversions=0) AND (subscriberviews IS NULL OR subscriberviews<100)');
		$ghostQuote = percentage($ghostArticles,$articles,1);

		$pageviews = $this->DailyKPIs->sum('pageviews');
		$subscriberviews = $this->DailyKPIs->sum('subscriberviews');
		$mediatime = round($this->DailyKPIs->avg('avgmediatime'),1);

		$orders = $this->Orders->count();
		$ordersYearly = $this->Orders->count("subscription_internal_title like '%12M%'");
		$ordersMonthly = $orders - $ordersYearly;
		$ordersExternal = $this->Orders->count("(order_origin = 'Aboshop' OR order_origin = 'Plusseite')");


		$out = [
			'pageviews' => $pageviews,
			'subscriberviews' => $subscriberviews,
			'mediatime' => $mediatime,

			'articles' => $articles,
			'audienceArticles' => $audienceArticles,
			'ghostArticles' => $ghostArticles,
			'ghostQuote' => $ghostQuote,

			'orders' => $orders,
			'ordersMonthly' => $ordersMonthly,
			'ordersYearly' => $ordersYearly,
			'ordersExternal' => $ordersExternal,

		];

		$this->view->json($out);

	}



	public function show_ip() {
		echo $_SERVER['REMOTE_ADDR'];
		die;
	}


}
