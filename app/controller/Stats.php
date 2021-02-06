<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;

class Stats extends Controller {

	public function __construct() {

		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Articles,Stats');
	}

	public function index() {

		$viewData['articles'] = $this->Articles->count('*');
		$viewData['pageviews'] = $this->Articles->sum('pageviews');
		$viewData['sessions'] = $this->Articles->sum('sessions');
		$viewData['conversions'] = $this->Articles->sum('conversions');

		$viewData['ressortStats'] = $this->Articles->stats_grouped_by($column = 'ressort', $order = 'conversions DESC, ressort ASC');
		$viewData['authorStats'] = $this->Articles->stats_grouped_by($column = 'author', $order = 'author ASC');
		$viewData['typeStats'] = $this->Articles->stats_grouped_by($column = 'type', $order = 'conversions DESC');

		Session::set('referer', '/stats');
		$this->view->render('pages/stats', $viewData);
	}

}
