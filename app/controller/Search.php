<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;

class Search extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Articles');
	}

	public function show() {

		$query = $_GET['q'] ?? null;
		$query = htmlspecialchars($query);
		Session::set('referer', '/search' . '?q=' . $query);

		if (empty($query)) {
			throw new \Exception("Bitte Suchbegriff eingeben", 400);
		}

		if (preg_match("/^[0-9]{7}$/", $query)) {
			$this->view->redirect('/orders/' . $query);
		}

		if (preg_match("/^[0-9]{8}$/", $query)) {
			$this->view->redirect('/artikel/' . $query);
		}

		if (preg_match("/^[0-9]{12}$/", $query)) {
			$this->view->redirect('/readers/' . $query);
		}


		$articles = $this->Articles->search($query, ['id','title','kicker','description']);
		$viewData['articles'] = $articles;
		$viewData['query'] = $query;
		$viewData['pageviews'] = $this->Articles->sum_up($articles,'pageviews');
		$viewData['buyintents'] = $this->Articles->sum_up($viewData['articles'],'buyintent');
		$viewData['conversions'] = $this->Articles->sum_up($articles,'conversions');
		$viewData['cancelled'] = $this->Articles->sum_up($viewData['articles'],'cancelled');
		$viewData['numberOfArticles'] = 0;

		if (is_array($articles)) {
			$viewData['numberOfArticles'] = count($articles);
		}

		$this->view->title = 'Suchergebnisse: ' . $query;
		$this->view->render('pages/list', $viewData);
	}


}
