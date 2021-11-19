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
		$this->models('Charts,LongtermKPIs');
	}

	public function overview() {

		$this->view->charts = $this->Charts;

		$cancellations = $this->LongtermKPIs->cancellations();
		$this->view->cancellations = $this->LongtermKPIs->as_chartdata($cancellations);

		$this->view->title = 'Langzeit Analysen';
		$this->view->render('stats/longterm');

	}


}
