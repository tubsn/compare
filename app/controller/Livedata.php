<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use app\importer\ArticleImport;
use app\importer\PortalImport;

class Livedata extends Controller {

	private $start;
	private $end;
	private $items = 100;
	private $paidOnly = true;

	public function __construct() {

		$this->view('DefaultLayout');
		$this->models('Articles,Plenigo,Linkpulse,Kilkaya,DailyKPIs,Orders,Readers,Charts');
	}

	public function index() {
		$this->view->redirect('/orders/today');
	}

	public function live_dashboard() {

		$this->start = date('Y-m-d', strtotime('today'));
		$this->end = date('Y-m-d', strtotime('today'));
		$orders = $this->Plenigo->orders($this->start, $this->end, $includeAppOrders = 0);
		$viewData['orders'] = array_reverse($orders);

		$liveData = $this->Kilkaya->today();
		$viewData['chart']['data'] = $liveData['values'];
		$viewData['chart']['time'] = $liveData['timestamps'];
		$viewData['pageviews'] = $liveData['pageviews'];

		$viewData['articles'] = $this->Kilkaya->articles_today();

		$this->view->title = 'Livedashboard von Heute';
		$this->view->render('stats/live', $viewData);

	}

	public function live_article($id = null) {

		//dd($this->Articles->get($id));

		$data = $this->Kilkaya->article($id,'7days ago');
		$this->view->json($data);

		//Array(4) [ "66542171", "66560951", "66582167", "66580995" ]

	}

	public function compared_conversion_sources() {

		$from = date('Y-m-d', strtotime(DEFAULT_FROM));
		$to = date('Y-m-d', strtotime(DEFAULT_TO));

		if (Session::get('from')) {$from = Session::get('from');}
		if (Session::get('to')) {$to = Session::get('to');}

		//$from = '2022-08-30';
		//$to = '2022-09-15';

		$kilkaya = $this->Kilkaya->conversions($from, $to);
		$kilkaya = array_column($kilkaya, 'conversions', 'day');

		$this->Orders->from = $from;
		$this->Orders->to = $to;

		$plenigo = $this->Orders->kpi_grouped_by('order_id','DATE(order_date)','count');

		//dd($plenigo);

		$plenigo = array_column($plenigo, 'order_id', 'DATE(order_date)');

		$both = [];
		foreach ($kilkaya as $day => $kilkayaOrders) {

			$both[$day]['kilkaya'] = $kilkayaOrders;
			$both[$day]['plenigo'] = $plenigo[$day] ?? null;

		}

		$this->view->charts = $this->Charts;
		$this->view->data = $this->Charts->convert($both);

		$this->view->title = 'Conversion Quellvergleich';
		$this->view->render('orders/source-compare');

	}


	public function api_article($id) {

		$article = $this->Articles->get($id);

		if (empty($article)) {
			$import = new ArticleImport();
			$article = $import->detail_rss($id);
		}

		$pubdate = $article['pubdate'] ?? date('Y-m-d', strtotime('-7days'));
		$stats = $this->Kilkaya->article($id, $pubdate);

		header('Access-Control-Allow-Origin: *');
		$this->view->json(array_merge($article, $stats));

	}

	public function article_compare($swpID, $mozID, $lrID) {

		$import = new PortalImport();
		$this->view->articles = $import->articles($swpID, $mozID, $lrID);
		$this->view->render('articles/article-compare');

	}


	public function api_orders_today() {

		$from = date('Y-m-d', strtotime('today'));
		$to = date('Y-m-d', strtotime('today'));
		$orders = $this->Plenigo->orders($from, $to, $includeAppOrders = 0);
		$orders = array_reverse($orders);

		header('Access-Control-Allow-Origin: *');
		$this->view->json($orders);

	}

	public function api_stats_today($resolution = 3) {
		$stats['users'] = $this->Kilkaya->today('pageview', $resolution);
		$stats['subs'] = $this->Kilkaya->today('subscriber', $resolution);
		header('Access-Control-Allow-Origin: *');
		$this->view->json($stats);
	}

	public function api_articles_today() {
		$articles = $this->Kilkaya->articles_today();
		header('Access-Control-Allow-Origin: *');
		$this->view->json($articles);
	}

	public function api_active_users() {
		$output['users'] = $this->Kilkaya->active_users();
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
		$viewData['orders'] = $this->Plenigo->orders($this->start, $this->end, $paidFilter);
		$this->view->render('orders/live/list',$viewData);

	}

	public function orders_yesterday() {

		$paidFilter = Session::get('paid_filter');

		$this->start = date('Y-m-d', strtotime('yesterday'));
		$this->end = date('Y-m-d', strtotime('yesterday'));

		$viewData['orders'] = $this->Plenigo->orders($this->start, $this->end, $paidFilter);

		if (is_array($viewData['orders']) && count($viewData['orders']) > 0) {
			$this->view->revenue = array_sum(array_column($viewData['orders'],'order_price'));
		}

		$this->view->title = 'Bestellungen Gestern';
		$this->view->referer('/orders/yesterday');
		$this->view->render('orders/live/list',$viewData);

	}

	public function orders_date($date) {

		$paidFilter = Session::get('paid_filter');

		$viewData['date'] = $date;
		$viewData['orders'] = $this->Plenigo->orders($date, $date, $paidFilter);

		if (is_array($viewData['orders']) && count($viewData['orders']) > 0) {
			$this->view->revenue = array_sum(array_column($viewData['orders'],'order_price'));
		}

		$this->view->title = $date . ' - Bestellungen';
		$this->view->referer('/orders/' . $date);
		$this->view->render('orders/live/list',$viewData);

	}


	public function orders_today() {

		$paidFilter = Session::get('paid_filter');

		$this->start = date('Y-m-d', strtotime('today'));
		$this->end = date('Y-m-d', strtotime('today'));

		$viewData['orders'] = $this->Plenigo->orders($this->start, $this->end, $paidFilter);

		if (is_array($viewData['orders']) && count($viewData['orders']) > 0) {
			$this->view->revenue = array_sum(array_column($viewData['orders'],'order_price'));
		}

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
