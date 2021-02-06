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

		$this->view->render('admin/index', $viewData);
	}


	public function save_config() {
		$typen = strip_tags($_POST['typen']);
		file_put_contents(CONFIGPATH . '/artikel_typen.txt', $typen);
		$this->view->redirect('/admin');
	}

	public function login() {
		if (Auth::logged_in()) {$this->view->redirect('/');}
		$this->view('LoginLayout');
		$this->view->render('admin/login');
	}

	public function check_login() {
		$this->view('LoginLayout');

		if (!hash_equals($_POST['CSRFToken'], Session::get('CSRFToken'))) {
			throw new \Exception("Token Missmatch", 403); die;
		}

		$username = $_POST['username']; $password = $_POST['password'];

		try {
			$loggedIn = $this->Auth->login($username,$password);
			$this->view->redirect(Session::get('referer'));
		} catch (\Exception $e) {$error = $e->getMessage();}

		$this->view->render('admin/login', ['username' => $username, 'message' => $error]);

	}

	public function logout() {
		$this->Auth->logout();
		$this->view->redirect('/');
	}

	public function profile() {
		if (!Auth::logged_in()) {$this->view->redirect('/login');}

		// Update Current Authuser with Info from the UserDB
		Auth::refresh_auth(); // e.g. to quickly Refresh Rights

		$this->view->render('admin/profil', ['profile' => $this->Auth->profile()]);
	}



}
