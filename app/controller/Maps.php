<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;
use flundr\cache\RequestCache;

class Maps extends Controller {

	public function __construct() {

		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Orders,Maps');
	}


	public function users_local() {

		Session::set('referer', '/readers/map/local');
		$this->view->PLZs = $this->Maps->colored_plz_users(false);
		
		$this->view->title = 'Verteilungskarte unserer Nutzer nach Drive IP Daten';
		$this->view->render('pages/readers/maps/local');

	}

	public function users_germany() {

		Session::set('referer', '/readers/map/germany');
		$this->view->PLZs = $this->Maps->colored_plz_users(true);
		
		$this->view->title = 'Verteilungskarte unserer Nutzer nach Drive IP Daten';
		$this->view->render('pages/readers/maps/germany');

	}


	public function map_local($cancelled = false) {

		Session::set('referer', '/orders/map/local');

		$datasets = count($this->Orders->group_by('customer_postcode') ?? 0);
		$this->view->title = 'Verteilung von Käufen nach Postleitzahl - Datensätze: ' . $datasets;

		if ($cancelled) {
			$this->view->showCancelled = true;
			$this->view->title = 'Kündigerquote nach Postleitzahl - Datensätze: ' . $datasets;
		}

		$this->view->PLZs = $this->Maps->colored_geo_orders();
		$this->view->render('orders/maps/local');
	}

	public function map_germany($cancelled = false) {

		Session::set('referer', '/orders/map/germany');

		$datasets = count($this->Orders->group_by('customer_postcode') ?? 0);
		$this->view->title = 'Verteilung von Käufen nach Postleitzahl - Datensätze: ' . $datasets;

		if ($cancelled) {
			$this->view->showCancelled = true;
			$this->view->title = 'Kündigerquote nach Postleitzahl - Datensätze: ' . $datasets;
		}

		$this->view->PLZs = $this->Maps->colored_geo_orders_3steps();
		$this->view->render('orders/maps/germany');
	}

	public function map_local_cancelled() {$this->map_local(true);}
	public function map_germany_cancelled() {$this->map_germany(true);}



	public function map_print_local($cancelled = false) {

		Session::set('referer', '/print/local');

		$this->view->title = 'LR Printabos nach Postleitzahl (54336 Abonennten)';

		if ($cancelled) {
			$this->view->showCancelled = true;
			$this->view->title = 'Kündiger letzte 3 Monate nach Postleitzahl';
		}

		$this->view->PLZs = $this->Maps->csv_import();
		$this->view->render('orders/maps/print-local');
	}

	public function map_print_germany($cancelled = false) {

		Session::set('referer', '/print/germany');

		$this->view->title = 'LR Printabos nach Postleitzahl (54336 Abonennten)';

		if ($cancelled) {
			$this->view->showCancelled = true;
			$this->view->title = 'Kündiger letzte 3 Monate nach Postleitzahl';
		}

		$this->view->PLZs = $this->Maps->csv_import();
		$this->view->render('orders/maps/print-ger');
	}

	public function map_print_local_cancelled() {$this->map_print_local(true);}
	public function map_print_germany_cancelled() {$this->map_print_germany(true);}

}
