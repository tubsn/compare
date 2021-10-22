<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\auth\LoginHandler;
use flundr\auth\Auth;
use flundr\utility\Session;

class Admin extends Controller {

	public function __construct() {
		$this->view('DefaultLayout');
		$this->view->templates['footer'] = null;
		$this->models('Articles');
		$this->Auth = new LoginHandler();
	}

	public function index() {
		Session::set('referer','/admin');
		if (!Auth::logged_in()) {Auth::loginpage();}

		$viewData['typen'] = file_get_contents(CONFIGPATH . PORTAL .DIRECTORY_SEPARATOR . 'artikel_typen.txt');
		$viewData['tags'] = file_get_contents(CONFIGPATH . PORTAL . DIRECTORY_SEPARATOR . 'artikel_tags.txt');
		$viewData['audiences'] = file_get_contents(CONFIGPATH . PORTAL . DIRECTORY_SEPARATOR . 'artikel_audiences.txt');

		$this->view->title = 'Einstellungen';
		$this->view->render('admin/config', $viewData);
	}


	public function save_config() {

		$typen = strip_tags($_POST['typen']);
		file_put_contents(CONFIGPATH . PORTAL . DIRECTORY_SEPARATOR . 'artikel_typen.txt', $typen);

		$tags = strip_tags($_POST['tags']);
		file_put_contents(CONFIGPATH . PORTAL . DIRECTORY_SEPARATOR . 'artikel_tags.txt', $tags);

		$audiences = strip_tags($_POST['audiences']);
		file_put_contents(CONFIGPATH . PORTAL . DIRECTORY_SEPARATOR . 'artikel_audiences.txt', $audiences);


		$this->view->redirect('/admin');

	}

	public function cluster_manager() {

		if (!Auth::has_right('type')) {
			throw new \Exception("Sie haben keine Berechtigung diese Seite aufzurufen", 403);
		}

		$viewData['types'] = $this->Articles->count_distinct('type');
		$viewData['tags'] = $this->Articles->count_distinct('tag');
		$viewData['audiences'] = $this->Articles->count_distinct('audience');

		// Gather All Types
		$this->Articles->from = '0000-00-00';
		$this->Articles->to = '3000-01-01';

		$viewData['availableTypes'] = $this->Articles->list_distinct('type');
		$viewData['availableAudiences'] = $this->Articles->list_distinct('audience');
		$viewData['availableTags'] = $this->Articles->list_distinct('tag');

		$this->view->title = 'Themen-Cluster-Manager';
		$this->view->render('admin/type-manager', $viewData);

	}

	public function set_clusters() {

		if (!Auth::has_right('type')) {
			throw new \Exception("Sie haben keine Berechtigung diese Seite aufzurufen", 403);
		}

		if (isset($_POST['type'])) {$newClusterGroup = 'type'; $newClusterValue = strip_tags($_POST['type']);}
		if (isset($_POST['audience'])) {$newClusterGroup = 'audience'; $newClusterValue = strip_tags($_POST['audience']);}
		if (isset($_POST['tag'])) {$newClusterGroup = 'tag'; $newClusterValue = strip_tags($_POST['tag']);}
		if (isset($_POST['cluster'])) {$oldClusterGroup = strip_tags($_POST['cluster']);}
		if (isset($_POST['clusterValue'])) {$oldClusterValue = strip_tags($_POST['clusterValue']);}
		if (!isset($oldClusterGroup)) {throw new \Exception("Cluster-Art nicht erkannt", 400);}
		if (!isset($oldClusterValue)) {throw new \Exception("Original Cluster nicht erkannt", 400);}

		$changedArticles = $this->Articles->bulk_change_cluster($oldClusterValue, $oldClusterGroup, $newClusterValue, $newClusterGroup);

		if ($newClusterGroup != $oldClusterGroup) {
			$this->Articles->reset_cluster($oldClusterValue, $oldClusterGroup);
		}

		$this->view->redirect('/admin/cluster');

	}

}
