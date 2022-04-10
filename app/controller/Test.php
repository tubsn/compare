<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;

class Test extends Controller {

	public function __construct() {

		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Articles,Conversions,Analytics');
	}

	public function index($id) {

		$article = $this->Articles->get($id);
		$pubDate = formatDate($article['pubdate'],'Y-m-d');

		$buyintent = $this->Analytics->buy_intention_by_article_id($id, $pubDate);


		echo 'Paywallklicks: ' . $buyintent .'<br>';
		echo 'Conversions:' . $article['conversions'] .'<br>';
		dump($article);

	}


}
