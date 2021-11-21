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
		$this->models('Charts,Longterm,Orders');
	}

	public function overview() {

		$this->view->charts = $this->Charts;
		$this->view->longterm = $this->Longterm->chartdata('orders');

		$this->view->title = 'Langzeit Analysen';
		$this->view->render('stats/longterm');

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
