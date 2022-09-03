<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;
use app\models\helpers\CSVImports;
use app\importer\PlenigoAPI;

class Test extends Controller {

	public function __construct() {

		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Articles,Orders,Conversions,Analytics,Charts,Readers,Plenigo');
	}


	public function optins() {
		$plenigo = new PlenigoApi;
		dd($plenigo->test());

		//$this->view->active = $this->Orders->active_after_days(60);

	}


}
