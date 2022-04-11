<?php

namespace app\controller;
use flundr\mvc\Controller;
use app\importer\AnalyticsReaderAdapter;
use flundr\auth\Auth;
use \flundr\cache\RequestCache;

class Readers extends Controller {

	public function __construct() {

		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Articles,Readers,Orders,Plenigo');
	}


	public function with_multiple_orders() {
		$this->Orders->from = '2000-01-01';
		$this->Orders->to = '3000-01-01';
		$orders = $this->Orders->customers_with_multiple_orders();
		dump($orders);

	}

	public function engagement_alert() {

		//$this->Readers->add_segment_to_latest_orders();
		//$this->Readers->add_segement_to_latest_cancellations();
		echo 'tbd';

	}



	public function overview() {
		$this->view->render('pages/reader-list');
	}


	public function detail($id) {

		$this->Orders->from = '2000-01-01';
		$this->Orders->to = '3000-01-01';

		$this->view->reader = $this->Readers->get_from_api($id);

		$orders = $this->Orders->by_customer($id);
		$subscription = array_filter($orders, function($order) {
			if ($order['subscription_status'] == 'ACTIVE') {return $order;}
		});
		$subscription = $subscription[0] ?? null;

		$orders = array_reverse($orders);

		$this->view->orderSources = $this->Readers->favorites($orders, 'article_ressort');
		$this->view->mostRead = $this->Readers->favorites($this->view->reader['lastArticles'], 'ressort');
		$this->view->audience = $this->Readers->favorites($orders, 'article_audience');
		$this->view->cluster = $this->Readers->favorites($orders, 'article_type');

		$this->view->subscription = $subscription;
		$this->view->orders = $orders;
		$this->view->orderCount = count($orders) ?? 0;

		//$this->view->reader['additionalData'] = $this->Plenigo->customer_additional_data($id);

		$this->view->render('pages/reader-detail');

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


	public function session_list() {
		$users = $this->users_with_most_sessions();
		$this->view->csv($users);
	}

	public function users_with_most_sessions() {

		$cache = new RequestCache('mostsessions' . PORTAL, 60*60);

		$cachedData = $cache->get();
		if ($cachedData) {
			return $cachedData;
		}

		$users = $this->import_session_csv();

		foreach (array_keys($users) as $id) {
			$sessions = $users[$id];
			$users[$id] = $this->Plenigo->customer($id);
			$users[$id]['sessions'] = $sessions;
		}
		$cache->save($users);
		return $users;

	}

	public function import_session_csv() {

		$path = ROOT . 'import/sessionuser.csv';

		if (!file_exists($path)) {
			throw new \Exception($path . ' Not found', 500);
		}

		$data = file($path);

		$out = [];
		foreach ($data as $entry) {
			$out[str_getcsv($entry,";")[0]] = str_getcsv($entry,";")[1];
		}

		return $out;

	}




	public function plenigo($plenigoID) {

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



}
