<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\auth\Auth;
use flundr\cache\RequestCache;
use \flundr\utility\Log;
use app\importer\ArticleImport;

class Import extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
		$this->view('DefaultLayout');
		$this->models('Articles,ArticleMeta,Orders,DailyKPIs,Analytics,Campaigns,Readers');
	}

	public function feeds() {

		$feeds = IMPORT_FEEDS; // Feeds in Config
		$import = new ArticleImport();

		foreach ($feeds as $feed) {

			try {$articles = $import->rss($feed);}
			catch (\Exception $e) {
				Log::error($e->getMessage());
				echo $e->getMessage() . '<br/>';
			}

			$articles = array_filter($articles, function($article) {

				// Always Import Articles with Audience
				if (isset($article['audience']) && !empty($article['audience'])) {return $article;}

				$dpaFilterPattern = "/\b(?:dpa)\b/i"; // Filter DPA
				if (preg_match($dpaFilterPattern,$article['author'])) {return null;}

				if (strtolower($article['ressort']) == 'bilder') {return null;}  // Filter Bildergalerien

				return $article;

			});

			$articles = array_values($articles); // Reindex the array Keys
			$this->Articles->add_to_database($articles);

		}

		$this->import_global_kpis();
		$this->import_utm_campaigns();
		$this->ArticleMeta->import_drive_data();

		echo 'Import abgeschlossen! <a href="/admin">zurück</a><br/>';
		echo 'Processing-Time: <b>'.round((microtime(true)-APP_START)*1000,2) . '</b>ms';

		//$this->view->redirect('/');

	}

	public function order_import_form() {
		$this->view->render('orders/import');
	}

	public function order_import($date = null) {
		$ignoreCancelled = false;
		if (isset($_GET['ignorecancelled'])) {$ignoreCancelled = true;}
		$orders = $this->Orders->import($date, $ignoreCancelled);
		$this->view->json($orders);
	}

	public function import_readers() {
		$this->Readers->import_readers();
	}


	private function import_global_kpis() {
		$this->DailyKPIs->import(3);
	}

	public function import_subscribers() {
		$from = date('Y-m-d', strtotime('yesterday -3days'));
		$to = date('Y-m-d', strtotime('yesterday'));
		$this->DailyKPIs->import_subscribers($from, $to);
	}

	public function import_utm_campaigns($days = 5) {

		$data = $this->Analytics->utm_campaigns($days);
		foreach ($data as $order) {
			$campaign['order_id'] = $order['Transactionid'];
			$campaign['ga_date'] =  date('Y-m-d', strtotime($order['Date']));
			$campaign['utm_source'] = $order['Source'];
			$campaign['utm_medium'] = $order['Medium'];
			$campaign['utm_campaign'] = $order['Campaign'];
			$this->Campaigns->create_or_update($campaign);
		}

	}

	public function csv_import_segments_by_date() {

		$pathNormal = ROOT . 'import/temp/lr-alle.csv';
		$pathPremium = ROOT . 'import/temp/lr-premium.csv';
		$normalUsers = $this->read_csv_with_header($pathNormal);
		$premiumUsers = $this->read_csv_with_header($pathPremium);

		$this->save_segments_to_db($normalUsers);
		$this->save_segments_to_db($premiumUsers, '_reg');

	}

	private function read_csv_with_header($filepath) {

		if (!file_exists($filepath)) {
			throw new \Exception($filepath . ' Not found', 500);
		}

		$data = file($filepath, FILE_IGNORE_NEW_LINES);
		$header = str_getcsv(array_shift($data),',');
		$csv = array_map(function($set){
			return str_getcsv($set,",");
		},$data);

		foreach ($csv as $key => $row) {
			$csv[$key] = array_combine($header, $row);
		}

		return $csv;

	}



	private function save_segments_to_db($csv, $suffix = null) {

		$segmentDatabaseMapping = [
			'low-usage-irregular' => 'low_usage_irregulars',
			'high-usage-irregular' => 'high_usage_irregulars',
			'loyal' => 'loyals',
			'champion' => 'champions',
			'fly-by' => 'flybys',
			'non-engaged' => 'nonengaged',
			'unknown' => 'unknown',
		];

		$segmentFieldName = 'user_engagement_segment';
		$amountFieldName = 'Anzahl aktive User';
		$dateFieldName = '__timestamp';

		$data = array_group_by($dateFieldName, $csv);

		$segmentsByDate = [];
		foreach ($data as $date => $segmentSets) {

			foreach ($segmentSets as $sets) {
				$mappedSegment = $segmentDatabaseMapping[$sets[$segmentFieldName]];
				if ($suffix) {$mappedSegment = $mappedSegment . $suffix;}
				$segmentsByDate[$date][$mappedSegment] = $sets[$amountFieldName];
			}

		}

		ksort($segmentsByDate);

		//dd($segmentsByDate);

		foreach ($segmentsByDate as $day => $segmentData) {
			$this->DailyKPIs->update($segmentData,$day);
		}

	}



	public function import_segments_by_date() {
		$segments = $this->Readers->import_user_segments();
		echo 'Segment Import abgeschlossen! <a href="/admin">zurück</a><br/>';
		echo 'Processing-Time: <b>'.round((microtime(true)-APP_START)*1000,2) . '</b>ms';
	}


	public function import_user_segments_from_csv() {

		$path = ROOT . 'import/user-groups.csv';

		if (!file_exists($path)) {
			throw new \Exception($path . ' Not found', 500);
		}

		$data = file($path, FILE_IGNORE_NEW_LINES);
		$header = str_getcsv(array_shift($data),',');

		$csv = array_map(function($set){
			return str_getcsv($set,",");
		},$data);

		foreach ($csv as $key => $row) {
			$csv[$key] = array_combine($header, $row);
		}

		$index = 'publisher';
		$value = PORTAL_NAME;

		$csv = array_filter($csv, function($data) use ($index, $value){
			return $data[$index] == $value;
		});

		$startSegment = array_column($csv, 'user_engagement_segment_subscription_start', 'order_id');
		$cancelSegment = array_column($csv, 'user_engagement_segment_subscription_cancellation', 'order_id');
		$testingGroup = array_column($csv, 'mode_experiment_group', 'order_id');

		foreach ($testingGroup as $id => $value) {
			if (empty($value)) {continue;}
			$this->Orders->update(['customer_testgroup' => $value], $id);
		}

		foreach ($cancelSegment as $id => $value) {
			if (empty($value) || $value == 'unknown') {
				$this->Orders->update(['customer_cancel_segment' => null], $id);
				continue;
			}
			$this->Orders->update(['customer_cancel_segment' => $value], $id);
		}

		foreach ($startSegment as $id => $value) {
			if (empty($value) || $value == 'unknown') {
				$this->Orders->update(['customer_cancel_segment' => null], $id);
				continue;
			}
			$this->Orders->update(['customer_order_segment' => $value], $id);
		}

	}





}
