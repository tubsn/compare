<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\auth\Auth;
use flundr\cache\RequestCache;
use app\importer\ArticleImport;

class Epaper extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
		$this->view('DefaultLayout');
		$this->models('Epaper');
	}

	public function index() {
		$this->view->title = 'ePaper - Top Artikel';
		$this->view->articles = $this->Epaper->articles();
		$this->view->render('epaper/list');
	}


	public function ressort($ressort) {
		$this->view->title = 'ePaper - Artikel';
		$this->view->articles = $this->Epaper->by_ressort($ressort);
		$this->view->render('epaper/list');
	}


	public function ressort_list() {
		$this->view->title = 'ePaper - Ressortübersicht';
		$this->view->ressorts = $this->Epaper->ressorts();
		$this->view->render('epaper/ressorts');
	}

	public function detail($id) {

		$this->view->title = 'ePaper - Artikel Detail';
		$this->view->article = $this->Epaper->by_article_id($id);
		$this->view->render('epaper/detail');

	}


}
