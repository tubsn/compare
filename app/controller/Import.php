<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\auth\Auth;
use app\importer\ArticleImport;

class Import extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
		$this->view('DefaultLayout');
		$this->models('Articles');
	}

	public function feeds() {

		$feeds = IMPORT_FEEDS; // Feeds in Config
		$import = new ArticleImport();

		foreach ($feeds as $feed) {

			$articles = $import->rss($feed);

			$articles = array_filter($articles, function($article) {
				$dpaFilterPattern = "/\b(?:dpa)\b/i"; // Filter DPA
				if (preg_match($dpaFilterPattern,$article['author'])) {return null;}
				if ($article['ressort'] == 'Bilder') {return null;}  // Filter Bildergalerien
				return $article;
			});

			$articles = array_values($articles); // Reindex the array Keys

			$this->Articles->add_to_database($articles);

		}

		echo 'Import abgeschlossen! <a href="/admin">zurück</a><br/>';
		echo 'Processing-Time: <b>'.round((microtime(true)-APP_START)*1000,2) . '</b>ms';

		//$this->view->redirect('/');

	}


}
