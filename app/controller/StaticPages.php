<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\auth\Auth;

class StaticPages extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
		$this->view('DefaultLayout');
		$this->view->templates['footer'] = null;
		$this->models('Articles');
	}

	public function faq() {
		$this->view->render('pages/faq');
	}


	public function favilink() {
		$this->view->render('pages/favilink');
	}

}
