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
		$orders = $this->Orders->import($date);
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


		$path = ROOT . 'import/temp/dau-user-alle.csv';

		if (!file_exists($path)) {
			throw new \Exception($path . ' Not found', 500);
		}

		$data = file($path, FILE_IGNORE_NEW_LINES);
		$header = str_getcsv(array_shift($data),',');
		//$csv = array_map('str_getcsv', $data);
		$csv = array_map(function($set){
			return str_getcsv($set,",");
		},$data);

		foreach ($csv as $key => $row) {
			$csv[$key] = array_combine($header, $row);
		}

		$lr = array_filter($csv, function($data) {
			return $data['publisher'] == 'LR';
		});
		$lr = array_column($lr, 'COUNT_DISTINCT(inferred_user_id)', '__timestamp');
		ksort($lr);

		foreach ($lr as $day => $users) {
			$this->DailyKPIs->update(['daily_active_users' => $users],$day);
		}



	}


	private function all_segments_save_to_db($csv) {

		$fly = array_filter($csv, function($data) {
			return $data['user_engagement_segment'] == 'fly-by';
		});
		$fly = array_column($fly, 'Anzahl Nutzer', '__timestamp');
		ksort($fly);

		$non = array_filter($csv, function($data) {
			return $data['user_engagement_segment'] == 'non-engaged';
		});
		$non = array_column($non, 'Anzahl Nutzer', '__timestamp');
		ksort($non);

		$lui = array_filter($csv, function($data) {
			return $data['user_engagement_segment'] == 'low-usage-irregular';
		});
		$lui = array_column($lui, 'Anzahl Nutzer', '__timestamp');
		ksort($lui);

		$loyal = array_filter($csv, function($data) {
			return $data['user_engagement_segment'] == 'loyal';
		});
		$loyal = array_column($loyal, 'Anzahl Nutzer', '__timestamp');
		ksort($loyal);

		$hui = array_filter($csv, function($data) {
			return $data['user_engagement_segment'] == 'high-usage-irregular';
		});
		$hui = array_column($hui, 'Anzahl Nutzer', '__timestamp');
		ksort($hui);

		$champ = array_filter($csv, function($data) {
			return $data['user_engagement_segment'] == 'champion';
		});
		$champ = array_column($champ, 'Anzahl Nutzer', '__timestamp');
		ksort($champ);

		foreach ($champ as $day => $users) {
			$this->DailyKPIs->update(['champions' => $users],$day);
		}

		foreach ($hui as $day => $users) {
			$this->DailyKPIs->update(['high_usage_irregulars' => $users],$day);
		}

		foreach ($lui as $day => $users) {
			$this->DailyKPIs->update(['low_usage_irregulars' => $users],$day);
		}

		foreach ($loyal as $day => $users) {
			$this->DailyKPIs->update(['loyals' => $users],$day);
		}

		foreach ($non as $day => $users) {
			$this->DailyKPIs->update(['nonengaged' => $users],$day);
		}

		foreach ($fly as $day => $users) {
			$this->DailyKPIs->update(['flybys' => $users],$day);
		}

	}

	private function registered_segments_save_to_db($csv) {

		$fly = array_filter($csv, function($data) {
			return $data['user_engagement_segment'] == 'fly-by';
		});
		$fly = array_column($fly, 'Anzahl Nutzer', '__timestamp');
		ksort($fly);

		$non = array_filter($csv, function($data) {
			return $data['user_engagement_segment'] == 'non-engaged';
		});
		$non = array_column($non, 'Anzahl Nutzer', '__timestamp');
		ksort($non);

		$lui = array_filter($csv, function($data) {
			return $data['user_engagement_segment'] == 'low-usage-irregular';
		});
		$lui = array_column($lui, 'Anzahl Nutzer', '__timestamp');
		ksort($lui);

		$loyal = array_filter($csv, function($data) {
			return $data['user_engagement_segment'] == 'loyal';
		});
		$loyal = array_column($loyal, 'Anzahl Nutzer', '__timestamp');
		ksort($loyal);

		$hui = array_filter($csv, function($data) {
			return $data['user_engagement_segment'] == 'high-usage-irregular';
		});
		$hui = array_column($hui, 'Anzahl Nutzer', '__timestamp');
		ksort($hui);

		$champ = array_filter($csv, function($data) {
			return $data['user_engagement_segment'] == 'champion';
		});
		$champ = array_column($champ, 'Anzahl Nutzer', '__timestamp');
		ksort($champ);

		foreach ($champ as $day => $users) {
			$this->DailyKPIs->update(['champions_reg' => $users],$day);
		}

		foreach ($hui as $day => $users) {
			$this->DailyKPIs->update(['high_usage_irregulars_reg' => $users],$day);
		}

		foreach ($lui as $day => $users) {
			$this->DailyKPIs->update(['low_usage_irregulars_reg' => $users],$day);
		}

		foreach ($loyal as $day => $users) {
			$this->DailyKPIs->update(['loyals_reg' => $users],$day);
		}

		foreach ($non as $day => $users) {
			$this->DailyKPIs->update(['nonengaged_reg' => $users],$day);
		}

		foreach ($fly as $day => $users) {
			$this->DailyKPIs->update(['flybys_reg' => $users],$day);
		}

	}



	public function import_segments_by_date() {

		$segments = $this->Readers->import_user_segments_by_day();

		$segment_table_mapping = [
			'low-usage-irregular' => 'low_usage_irregulars',
			'high-usage-irregular' => 'high_usage_irregulars',
			'loyal' => 'loyals',
			'champion' => 'champions',
		];

		foreach ($segments as $set) {
			$segmentField = $segment_table_mapping[$set['segment']];
			$segmentFieldRegistered = $segmentField . '_reg';

			$this->DailyKPIs->update([
				$segmentField => $set['users'],
				$segmentFieldRegistered => $set['registered_users'],
			], $set['date']);
		}

		dump($segments);

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
		//$csv = array_map('str_getcsv', $data);
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
