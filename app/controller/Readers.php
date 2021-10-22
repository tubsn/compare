<?php

namespace app\controller;
use flundr\mvc\Controller;
use app\importer\AnalyticsReaderAdapter;
use flundr\auth\Auth;

class Readers extends Controller {

	public function __construct() {

		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Articles,Readers,Orders');
	}


	public function detail($id) {

		$viewData['user'] = $this->Readers->get_from_api($id);
		$this->view->render('pages/reader-detail',$viewData);

	}


	public function list($segment = null) {

		if ($segment) {
			$realSegments = [
				'champion',
				'fly-by',
				'low-usage-irregular',
				'high-usage-irregular',
				'loyal',
				'non-engaged',
				'unknown'
			];

			if (!in_array($segment, $realSegments)) {
				throw new \Exception("Segment not Available", 404);
			}
		}

		$orders = $this->Orders->list_plain();

		$this->view->completeOrders = $orders;

		$out = [];

		foreach ($orders as $order) {

			$reader = $this->Readers->get($order['customer_id']);

			if ($reader) {
			$newOrder = array_merge($order, $reader);
			array_push($out, $newOrder);				
			};

		}

		if ($segment) {
			$out = $this->filter_segment($out, $segment);
		}

		$cancelled = $this->filter_cancelled($out);

		$this->view->orders = $out;
		$this->view->cancelled = $cancelled;
		$this->view->segment = $segment;
		$this->view->title = 'Käufe mit Usersegmenten';
		$this->view->render('orders/readerlist');

	}




	public function filter_cancelled($orders) {
		if (empty($orders)) {return [];}
		return array_filter($orders, function($order) {
			if ($order['cancelled']) {return $order;}
		});
	}


	public function filter_segment($orders, $segment) {
		if (empty($orders)) {return [];}
		return array_filter($orders, function($order) USE ($segment) {
			if ($order['user_segment'] == $segment) {return $order;}
		});
	}



/*
	public function index() {

		$this->view->render('pages/reader-list');

	}

	public function detail($plenigoID) {

		$plenigoID = strip_tags($plenigoID);

		$reader = new AnalyticsReaderAdapter($plenigoID.'.json');

		$viewData['user'] = $reader->id;

		foreach ($reader->articles as $key => $article) {
			$articleFromDB = $this->Articles->get($article['id']);

			if ($articleFromDB) {
			$reader->articles[$key] = array_merge($reader->articles[$key], $articleFromDB);	// code...
			}

		}

		//dd($reader->articles);

		$viewData['articles'] = $reader->articles;

		$viewData['user'] = $this->Plenigo->user($plenigoID);
		$viewData['products'] = $this->Plenigo->products($plenigoID);
		$viewData['subscriptions'] = $this->Plenigo->subscriptions($plenigoID);

		$this->view->render('pages/reader-detail',$viewData);

	}
*/


}
