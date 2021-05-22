<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\auth\LoginHandler;
use flundr\auth\Auth;
use flundr\utility\Session;

class Admin extends Controller {

	public function __construct() {
		$this->view('DefaultLayout');
		$this->Auth = new LoginHandler();
	}

	public function index() {
		Session::set('referer','/admin');
		if (!Auth::logged_in()) {Auth::loginpage();}

		$viewData['typen'] = file_get_contents(CONFIGPATH . '/artikel_typen.txt');
		$viewData['tags'] = file_get_contents(CONFIGPATH . '/artikel_tags.txt');

		$this->view->title = 'Einstellungen';
		$this->view->render('admin/config', $viewData);
	}


	public function save_config() {

		$typen = strip_tags($_POST['typen']);
		file_put_contents(CONFIGPATH . '/artikel_typen.txt', $typen);

		$tags = strip_tags($_POST['tags']);
		file_put_contents(CONFIGPATH . '/artikel_tags.txt', $tags);

		$this->view->redirect('/admin');

	}

}
