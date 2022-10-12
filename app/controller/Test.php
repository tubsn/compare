<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;
use app\models\helpers\CSVImports;
use app\importer\PlenigoAPI;

class Test extends Controller {

	public function __construct() {
		$this->view('DefaultLayout');
		$this->models('Articles,Orders,Conversions,Analytics,Charts,Readers,Plenigo,Cleverpush,Orders,DailyKPIs');
	}

	public function audience_sizes() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
		dd($this->Readers->audience_sizes());
	}

	public function api() {
		dd($this->Plenigo->apple_orders());
	}

	public function test() {

		// Sets Conversions in Daily KPIs

		$startDate = '2022-09-28';
		$endDate = '2022-10-06';

		$start = $this->create_date_object($startDate);
		$end = $this->create_date_object($endDate);

		$interval = new \DateInterval('P1D');
		$period = new \DatePeriod($start, $interval, $end);

		$monthList = [];
		foreach ($period as $date) {
			$date = $date->format('Y-m-d');

			$this->Orders->from = $date;
			$this->Orders->to = $date;
			$conversions = $this->Orders->count();
			$this->DailyKPIs->update(['conversions' => $conversions],$date);

		}


	}

	private function create_date_object($dateString = null) {
		if (is_null($dateString)) {return new \DateTime(date('Y-m-d'));}
		return new \DateTime(date('Y-m-d', strtotime($dateString)));
	}


	public function reader($id) {
		dd($this->Readers->live_from_api($id));
	}



}
