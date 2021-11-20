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
		$this->models('Charts,Longterm');
	}

	public function overview() {

		$this->view->charts = $this->Charts;
		$this->view->longterm = $this->Longterm->chartdata('orders');

		$this->view->title = 'Langzeit Analysen';
		$this->view->render('stats/longterm');

	}


}
