<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;

class Stats extends Controller {

	public function __construct() {

		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Articles,Conversions,Stats,Orders');
	}

	public function index() {

		$viewData['articles'] = $this->Articles->count('*');
		$viewData['plusarticles'] = $this->Articles->sum('plus');
		$viewData['pageviews'] = $this->Articles->sum('pageviews');
		$viewData['subscribers'] = $this->Articles->sum('subscribers');
		$viewData['mediatime'] = $this->Articles->sum('mediatime');
		$viewData['sessions'] = $this->Articles->sum('sessions');
		$viewData['conversions'] = $this->Articles->sum('conversions');
		$viewData['buyintents'] = $this->Articles->sum('buyintent');
		$viewData['cancelled'] = $this->Articles->sum('cancelled');

		$viewData['ressortStats'] = $this->Articles->stats_grouped_by($column = 'ressort', $order = 'conversions DESC, ressort ASC');
		$viewData['authorStats'] = $this->Articles->stats_grouped_by($column = 'author', $order = 'author ASC');
		$viewData['typeStats'] = $this->Articles->stats_grouped_by($column = 'type', $order = 'conversions DESC');
		$viewData['tagStats'] = $this->Articles->stats_grouped_by($column = 'tag', $order = 'conversions DESC');

		//dd($viewData['ressortStats']);

		Session::set('referer', '/stats');
		$this->view->title = 'Statistik Daten';
		$this->view->render('pages/stats', $viewData);
	}


	public function cancellations() {

		$viewData['conversions'] = $this->Conversions->list();
		$viewData['cancelled'] = $this->Conversions->cancelled_orders();
		$viewData['types'] = $this->Conversions->group_by_combined('article_type');
		$viewData['tags'] = $this->Conversions->group_by_combined('article_tag');
		$viewData['ressorts'] = $this->Conversions->group_by_combined('article_ressort');
		$viewData['cities'] = $this->Conversions->group_by_combined('ga_city');
		$viewData['sources'] = $this->Conversions->group_by_combined('ga_source');
		$viewData['payments'] = $this->Conversions->group_by_combined('subscription_payment_method');
		$viewData['gender'] = $this->Conversions->group_by_combined('customer_gender');
		$viewData['authors'] = $this->Conversions->group_by_combined('article_author');

		if (count($viewData['conversions']) > 0) {
			$viewData['quote'] = count($viewData['cancelled']) / count($viewData['conversions']) * 100;
		} else {$viewData['quote'] = 0;}

		$this->view->title = 'Kündiger Statistiken';
		$this->view->render('pages/cancellation-stats', $viewData);

	}


	public function dashboard() {

		$viewData['articles'] = $this->Articles->count('*');
		$viewData['subscribers'] = $this->Articles->sum('subscribers');
		$viewData['avgmediatime'] = $this->Articles->average('avgmediatime');
		$viewData['mediatime'] = $this->Articles->sum('mediatime');

		//dd($viewData['mediatime']);

		$viewData['mtDays'] = floor($viewData['mediatime'] / 60 / 60 / 24);
		//$viewData['mtDays'] = date('z', mktime(0,0,0,$viewData['mediatime'] / 60 / 60));
		$viewData['mtHours'] = date('G', mktime($viewData['mediatime'] / 60 / 60));
		$viewData['mtMinutes'] = date('i', mktime(0,$viewData['mediatime'] / 60));
		$viewData['mtSeconds'] = date('s', mktime(0,0,$viewData['mediatime']));

		$viewData['orders'] = $this->Orders->list();
		$viewData['numberOfOrders'] = count($viewData['orders'] ?? []);
		$viewData['numberOfCancelled'] = count($this->Orders->filter_cancelled($viewData['orders']));

		if ($viewData['orders']) {
			$viewData['cancelQuote'] = round(($viewData['numberOfCancelled'] / $viewData['numberOfOrders']) * 100, 1);
		} else {$viewData['cancelQuote'] = null;}

		$viewData['plusOnly'] = count($this->Orders->filter_plus_only($viewData['orders']));
		$viewData['externalOnly'] = count($this->Orders->filter_external($viewData['orders']));
		$viewData['averageRetention'] = $this->Orders->average($this->Orders->filter_cancelled($viewData['orders']),'retention');

		$viewData['singleChart'] = $this->Articles->subscribers_for_chart();
		$viewData['barChart'] = $this->Articles->subscribers_by_ressort_chart();

		$viewData['barChart2'] = $this->Orders->ressorts_for_chart();
		$viewData['singleChart2'] = $this->Orders->orders_for_chart();

		//$viewData['combinedChart'][0] = $this->Articles->mediatime_by_ressort_chart();
		//$viewData['combinedChart'][1] = $this->Articles->pageviews_by_ressort_chart();

		$viewData['barChart3'] = $this->Articles->mediatime_by_ressort_chart();
		$viewData['barChart4'] = $this->Articles->pageviews_by_ressort_chart();

		$this->view->title = 'Dashboard';
		$this->view->render('pages/dashboard', $viewData);

	}


}
