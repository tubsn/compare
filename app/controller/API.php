<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;
use flundr\cache\RequestCache;

class API extends Controller {

	public function __construct() {
		$this->view('DefaultLayout');
		$this->models('Longterm');
		header('Access-Control-Allow-Origin: *');
	}

	public function provide_portal_orders() {
		$this->view->json($this->Longterm->orders());
	}

	public function provide_portal_kpis() {
		$this->view->json($this->Longterm->kpis());
	}

	public function provide_combined_kpis() {
		$out['kpis'] = $this->Longterm->portal_KPIs();
		$out['orders'] = $this->Longterm->portal_orders();
		$out['quotes'] = $this->Longterm->combine_portal_data($out['orders'], $out['kpis']);
		$this->view->json($out);
	}

	public function show_ip() {
		echo $_SERVER['REMOTE_ADDR'];
		die;
	}


}
