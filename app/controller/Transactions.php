<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;
use flundr\cache\RequestCache;
use flundr\date\Datepicker;

class Transactions extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
		$this->view('DefaultLayout');
		$this->models('Plenigo,Orders,SalesKPIs,Charts');
	}


	public function index() {

		$start = date('Y-m-d', strtotime('first day of this month'));
		$end = date('Y-m-d', strtotime('last day of this month'));

		$datepicker = new Datepicker();

		$maxAge = '2020-02-01';
		$this->view->months = $datepicker->months($maxAge);

		$titleSuffix = ' - aktueller Monat';
		if (!empty($_GET['month'])) {
			$selection = $_GET['month'];
			if (isset($this->view->months[$selection])) {
				$selectedMonth = $this->view->months[$selection];
				$this->view->selectedMonth = $selection;
				$start = $selectedMonth['start'];
				$end = $selectedMonth['end'];
				$titleSuffix = ' - <em>' . $selection . '</em>';
			}
		}

		$this->Orders->from = $start;
		$this->Orders->to = $end;
		$this->view->orders = $this->Orders->count();

		$clusters = $this->Plenigo->transactions_clustered($start, $end);

		$costs = $this->Plenigo->calculate_transaction_costs($clusters['Erfolgreiche Transaktionen']);
		$costs = $costs + $this->Plenigo->calculate_chargeback_costs($clusters['Rückzahlungen']); 

		$payments = array_column($clusters['Erfolgreiche Transaktionen'], 'amount');
		$chargeback = array_column($clusters['Rückzahlungen'], 'amount');
		$chargeback = array_map('abs', $chargeback); // Returns only Positive Values because the values might be positive or negative

		$this->view->duplicates = $this->get_duplicates(array_column($clusters['Erfolgreiche Transaktionen'], 'customerId'));
		$this->view->duplicateChargebacks = $this->get_duplicates(array_column($clusters['Rückzahlungen'], 'customerId'));
		$this->view->kse = $this->filter_kse_chargebacks($clusters['Rückzahlungen']);

		$this->view->customers = $this->count_customers($clusters);
		$this->view->yearly = count($this->filter_yearly($clusters['Erfolgreiche Transaktionen']));
		$this->view->fullprice = count($this->filter_fullprice($clusters['Erfolgreiche Transaktionen']));
		$this->view->payments = round(array_sum($payments),2);
		$this->view->chargeback = round(array_sum($chargeback),2) * -1;
		$this->view->revenue = round(array_sum($payments) - array_sum($chargeback) - $costs,2);
		$this->view->costs = $costs * -1;
		//$this->save_to_db($start, $this->view->revenue);

		$this->view->kpis = $this->Charts->convert($this->SalesKPIs->list());
		$this->view->charts = $this->Charts;

		$this->view->clusters = $clusters;
		$this->view->title = 'Transaktionen / Zahlungsübersicht' . $titleSuffix;
		$this->view->templates['footer'] = null;
		$this->view->render('orders/transactions/list');

	}


	public function filter_kse_chargebacks($transactions) {

		return count(array_filter($transactions, function($entry) {
			if (
				$entry['paymentAction'] == 'PAYPAL_REFUND' ||
				$entry['paymentAction'] == 'SEPA_VOID' ||
				$entry['paymentAction'] == 'CREDIT_CARD_REFUND'
			) {return $entry;}
		}));

	}


	public function invoice_download($month = null) {

		if (!Auth::has_right('invoice')) {
			throw new \Exception("Sie haben, keine Berechtigung für diesen Bereich", 404);
		}

		$start = date('Y-m-d', strtotime('first day of this month'));
		$end = date('Y-m-d', strtotime('last day of this month'));

		$datepicker = new Datepicker();
		$monthList = $datepicker->months('2020-02-01');
		$titleSuffix = null;

		if ($month && isset($monthList[$month])) {
			$selectedMonth = $monthList[$month];
			$start = $selectedMonth['start'];
			$end = $selectedMonth['end'];
			$titleSuffix = '-' . $month;
		}

		$invoices = $this->Plenigo->invoices($start,$end);
		$this->view->title = PORTAL . '-Rechungen'. $titleSuffix;
		
		$invoices = array_map([$this, 'map_for_lorenz_table'], $invoices);	
		$this->view->csv($invoices);
	}



	private function save_to_db($startDate, $revenue) {

		$thisMonth = date('Y-m-d', strtotime('first day of this month'));
		$firstMonth = '2020-09-00';
		if ($startDate >= $thisMonth) {return;}
		if ($startDate <= $firstMonth) {return;}

		$date = substr($startDate,0,7) . '-00';
		$revenue = round($revenue);

		$this->SalesKPIs->create_or_update(['date' => $date, 'revenue' => $revenue]);

	}

	private function filter_yearly(array $array) {
		return array_filter($array, function($entry) {
			if ($entry['amount'] > 10) {return $entry;}
		});
	}

	private function filter_fullprice(array $array) {
		return array_filter($array, function($entry) {
			if ($entry['amount'] > 1) {return $entry;}
		});
	}


	private function count_customers($clusters) {
		$output = [];
		foreach ($clusters as $clusterName => $transactions) {
			$output[$clusterName] = $this->distinct_count($transactions);
		}
		return $output;
	}

	private function distinct_count($transactionArray, $fieldName = 'customerId') {
		return count(array_flip(array_column($transactionArray, $fieldName)));
	}

	private function get_duplicates($array) {
		return array_count_values(array_diff_assoc($array, array_unique($array)));
	}

	private function map_for_lorenz_table($org) {

		$new['RE-Nummer'] = $org['invoice_id'];
		$new['RE-Datum'] = date('d.m.Y', strtotime($org['invoice_date']));;

		$finalName = $org['customer_email'];

		if ($org['customer_firstname'] && $org['customer_lastname']) {
			$finalName = $org['customer_firstname'] . ' ' . $org['customer_lastname'];
		}

		if ($org['customer_company']) {
			$finalName = $finalName . ' (' . $org['customer_company'] . ')';
		}

		$new['Kunde'] = $finalName;

		$new['Zahlungsart'] = ucfirst(strtolower($org['invoice_payment_method']));
		$new['Betrag'] = $org['invoice_price'];
		$new['Typ'] = ucfirst(strtolower($org['invoice_type']));
		$new['Status'] = ucfirst(strtolower($org['invoice_status']));

		return $new;

	}

}
