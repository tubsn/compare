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


	public function test() {


		//$this->Readers->import_user_segments('2022-11-21', '2022-11-23');

		//$this->Plenigo->order_with_details(1270289);

	}




	public function conversionupdater() {

		// Sets Conversions in Daily KPIs

		$startDate = '2022-10-07';
		$endDate = '2022-11-28';

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
