<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;
use app\models\helpers\CSVImports;
use app\importer\PlenigoAPI;

class Test extends Controller {

	public function __construct() {
		$this->view('DefaultLayout');
		$this->models('Articles,Orders,Conversions,Analytics,Charts,Readers,Plenigo,Cleverpush');
	}

	public function audience_sizes() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
		dd($this->Readers->audience_sizes());
	}

	public function optins() {

		/*
		$unsetIDs = $this->Articles->get_unset_ids();
		$type = null;

		foreach ($unsetIDs as $id) {

			$article = $this->Articles->get($id,['type_lr','ressort']);

			switch ($article['type_lr']) {
				case 'Sport':
					$type = 'Sport';
					if ($article['ressort'] = 'energie-cottbus') {$type = 'Fußball';}
				break;

				case 'Politik/Wirtschaft': $type = 'Politik'; break;
				case 'Crime/Blaulicht': $type = 'Katastrophen'; break;
				case 'Tourismus': $type = 'Kultur'; break;
				case 'Geschichte': $type = 'Soziales'; break;
				case 'Landwirtschaft': $type = 'Wirtschaft'; break;
				case '': $type = null; break;
				
				default: $type = $article['type_lr']; break;
			}

			echo $id . ' | OLD: ' . $article['type_lr'] . '| NEW: ' . $type . '<br>';

			//$this->Articles->update(['type' => $type], $id);

		}
		*/

		/*
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
		$plenigo = new PlenigoApi;
		dd($plenigo->test());

		//$this->view->active = $this->Orders->active_after_days(60);
		*/

	}


	public function reader($id) {
		dd($this->Readers->live_from_api($id));
	}



}
