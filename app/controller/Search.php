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
		Session::set('referer', '/search' . '?=' . $query);

		if (empty($query)) {
			throw new \Exception("Bitte Suchbegriff eingeben", 400);
		}

		$articles = $this->Articles->search($query, ['title','kicker','description']);
		$viewData['articles'] = $articles;
		$viewData['query'] = $query;
		$viewData['pageviews'] = $this->Articles->sum_up($articles,'pageviews');
		$viewData['conversions'] = $this->Articles->sum_up($articles,'conversions');
		$viewData['numberOfArticles'] = 0;

		if (is_array($articles)) {
			$viewData['numberOfArticles'] = count($articles);
		}

		$this->view->title = 'Suchergebnisse: ' . $query;
		$this->view->render('pages/list', $viewData);
	}


}
