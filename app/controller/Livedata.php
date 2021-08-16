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
		$this->models('Plenigo');
	}

	public function index() {
		$this->view->redirect('/orders/today');
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
		$paidFilter = boolval($_POST['paid_filter']);
		Session::set('paid_filter', $paidFilter);
		$this->view->back();
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
