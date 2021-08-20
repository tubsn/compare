<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;

class Newsletter extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Articles,Orders');
	}

	public function chefredaktion() {

		$this->view('BlankLayout');

		$this->Orders->from = date('Y-m-d', strtotime('yesterday'));
		$this->Orders->to = date('Y-m-d', strtotime('today'));

		$viewData['pageviews'] = $this->Articles->top_pageviews_days_ago(1);
		//$viewData['conversions'] = $this->Articles->top_conversions_days_ago(1);
		$viewData['conversions'] = $this->Orders->latest_grouped();
		$viewData['conversionCount'] =$this->Orders->count();

		$this->view->render('newsletter/top-articles', $viewData);

	}

	public function sport_newsletter() {

		$this->view('BlankLayout');
		$viewData['pageviews'] = $this->Articles->top_articles_by_ressort_and_days_ago('sport,energie-cottbus',4);
		$this->view->render('newsletter/sport', $viewData);

	}

}
