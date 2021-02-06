<?php

namespace app\controller;
use flundr\mvc\Controller;
use app\importer\AnalyticsReaderAdapter;
use flundr\auth\Auth;

class Readers extends Controller {

	public function __construct() {

		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Readers,Articles,Plenigo');
	}

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

}
