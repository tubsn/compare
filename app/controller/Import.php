<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\auth\Auth;
use flundr\cache\RequestCache;
use \flundr\utility\Log;
use app\importer\ArticleImport;
use app\models\helpers\CSVImports;

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

				// DPA Artikel Filter deaktiviert 08.11.2022
				// $dpaFilterPattern = "/\b(?:dpa)\b/i"; // Filter DPA
				// if (preg_match($dpaFilterPattern,$article['author'])) {return null;}

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

	public function import_dailyKPI_segments() {
		$from = date('Y-m-d', strtotime('yesterday -2days'));
		$to = date('Y-m-d', strtotime('yesterday'));
		$segments = $this->Readers->import_user_segments($from, $to);
		echo 'Segment Import abgeschlossen! <a href="/admin">zurück</a><br/>';
		echo 'Processing-Time: <b>'.round((microtime(true)-APP_START)*1000,2) . '</b>ms';
	}

	// Used for manuell reimports 
	public function segments() {
		$this->Readers->import_user_segments('2022-08-19', '2022-08-21');
		//$CSVImport = new CSVImports();
		//$CSVImport->csv_import_segments_by_date();
	}

	public function experiment_data() {
		$CSVImports = New CSVImports();
		$CSVImports->import_drive_experiment_data_from_csv();
	}

	public function topics() {

		$CSVImports = New CSVImports();
		$CSVImports->import_drive_topics();

	}

}
