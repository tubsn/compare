<?php

namespace app\controller;

use flundr\mvc\Controller;

class Error extends Controller {

	function __construct($errorData) {

		$this->view('DefaultLayout');

		$viewData['error']['code'] = $errorData->getCode();
		$viewData['error']['message'] = $errorData->getMessage();

		if (!ENV_PRODUCTION) {
			$viewData['error']['trace'] = $errorData->getTraceAsString();
			$viewData['error']['line'] = $errorData->getLine();
			$viewData['error']['file'] = $errorData->getFile();
		}

		http_response_code(intval($viewData['error']['code']) ?? 404);

		$this->view->navigation = null;
		$this->view->render('pages/error', $viewData);
	}



}