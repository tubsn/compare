<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;

class Stats extends Controller {

	public function __construct() {

		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Articles,Conversions,Stats');
	}

	public function index() {

		$viewData['articles'] = $this->Articles->count('*');
		$viewData['pageviews'] = $this->Articles->sum('pageviews');
		$viewData['subscribers'] = $this->Articles->sum('subscribers');
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


}
