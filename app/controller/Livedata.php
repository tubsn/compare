<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;

class Livedata extends Controller {

	private $start;
	private $end;
	private $items = 100;
	private $paidOnly = true;

	public function __construct() {

		$this->view('DefaultLayout');
		$this->models('Plenigo,Linkpulse,Kilkaya');
	}


	public function kilkaya() {

		dd($this->Kilkaya->test());

	}


	public function index() {
		$this->view->redirect('/orders/today');
	}


	public function live_dashboard() {

		$this->start = date('Y-m-d', strtotime('today'));
		$this->end = date('Y-m-d', strtotime('today'));
		$orders = $this->Plenigo->orders($this->start, $this->end, $maxOrders=100, $includeAppOrders = 0);
		$viewData['orders'] = array_reverse($orders);

		$linkpulseLiveData = $this->Linkpulse->today();
		$viewData['chart']['data'] = $linkpulseLiveData['values'];
		$viewData['chart']['time'] = $linkpulseLiveData['timestamps'];
		$viewData['pageviews'] = $linkpulseLiveData['pageviews'];

		$viewData['articles'] = $this->Linkpulse->articles_today();

		$this->view->title = 'Livedashboard von Heute';
		$this->view->render('pages/live', $viewData);

	}

	public function api_orders_today() {

		$from = date('Y-m-d', strtotime('today'));
		$to = date('Y-m-d', strtotime('today'));
		$orders = $this->Plenigo->orders($from, $to, $maxOrders=100, $includeAppOrders = 0);
		$orders = array_reverse($orders);

		header('Access-Control-Allow-Origin: *');
		$this->view->json($orders);

	}

	public function api_stats_today($resolution = 3) {
		$stats['users'] = $this->Linkpulse->today(null, $resolution);
		$stats['subs'] = $this->Linkpulse->today_subs(null, $resolution);
		header('Access-Control-Allow-Origin: *');
		$this->view->json($stats);
	}

	public function api_articles_today() {
		$articles = $this->Linkpulse->articles_today();
		header('Access-Control-Allow-Origin: *');
		$this->view->json($articles);
	}

	public function api_active_users() {
		$output['users'] = $this->Linkpulse->active_users();
		header('Access-Control-Allow-Origin: *');
		$this->view->json($output);
	}

	public function order($id) {

		$viewData['data'] = $this->Plenigo->order_with_details($id);
		$this->view->title = 'Bestellvorgang: '. $id;
		$this->view->referer('/orders/' . $id);
		$this->view->render('orders/live/detail',$viewData);

	}

	public function orders() {

		$this->gather_get_info();
		$paidFilter = Session::get('paid_filter');
		$viewData['orders'] = $this->Plenigo->orders($this->start, $this->end, $this->items, $paidFilter);
		$this->view->render('orders/live/list',$viewData);

	}

	public function orders_yesterday() {

		$paidFilter = Session::get('paid_filter');

		$this->start = date('Y-m-d', strtotime('yesterday'));
		$this->end = date('Y-m-d', strtotime('yesterday'));

		$viewData['orders'] = $this->Plenigo->orders($this->start, $this->end, $this->items, $paidFilter);
		$this->view->title = 'Bestellungen Gestern';
		$this->view->referer('/orders/yesterday');
		$this->view->render('orders/live/list',$viewData);

	}

	public function orders_date($date) {

		$paidFilter = Session::get('paid_filter');

		$viewData['date'] = $date;
		$viewData['orders'] = $this->Plenigo->orders($date, $date, $this->items, $paidFilter);

		$this->view->title = $date . ' - Bestellungen';
		$this->view->referer('/orders/' . $date);
		$this->view->render('orders/live/list',$viewData);

	}


	public function orders_today() {

		$paidFilter = Session::get('paid_filter');

		$this->start = date('Y-m-d', strtotime('today'));
		$this->end = date('Y-m-d', strtotime('today'));

		$viewData['orders'] = $this->Plenigo->orders($this->start, $this->end, $this->items, $paidFilter);
		$this->view->title = 'Bestellungen Heute';
		$this->view->referer('/orders/today');
		$this->view->render('orders/live/list',$viewData);

	}

	public function set_client() {
		$client = $_POST['client'];
		Session::set('client', $client);
		$this->view->back();
	}

	public function set_paid_filter() {

		$paidFilter = false;
		if (isset($_POST['paid_filter'])) {
			$paidFilter = boolval($_POST['paid_filter']);
		}

		Session::set('paid_filter', $paidFilter);
		$this->view->back();
		die;
	}


	public function set_date() {
		$date = $_POST['date'];
		//Session::set('date', $date);
		$this->view->redirect('/orders/' . $date);
	}

	private function gather_get_info() {
		$this->start = $_GET['start'] ?? date('Y-m-d', strtotime('yesterday'));
		$this->end = $_GET['end'] ?? date('Y-m-d', strtotime('today'));
		$this->items = $_GET['items'] ?? $this->items;
	}

}
