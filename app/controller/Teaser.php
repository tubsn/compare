<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;

class Teaser extends Controller {

	public function __construct() {

		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Articles,Orders,TeaserPositions');
	}

	public function index($date = null) {

		if (is_null($date)) {
			$date = date('Y-m-d');
		}

		$this->view->date = strip_tags($date);
		$this->view->templates['footer'] = null;
		$this->view->render('pages/teasers');

	}


	public function api_positions($date, $hour) {

		$sets = $this->TeaserPositions->get($date, $hour);

		//dd($sets);

		$this->view->json($sets);

		//dd($this->view->sets);
		//$this->view->render('pages/teasers');

	}


}
