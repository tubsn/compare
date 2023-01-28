<?php

namespace app\models;
use app\importer\PortalImport;
use \flundr\database\SQLdb;
use \flundr\mvc\Model;
use \flundr\utility\Session;
use \flundr\cache\RequestCache;

class Portals extends Model
{

	public $from = '0000-00-00';
	public $to = '3000-01-01';

	private $articleID;
	protected $db;

	function __construct() {

		$this->from = date('Y-m-d', strtotime(DEFAULT_FROM));
		$this->to = date('Y-m-d', strtotime(DEFAULT_TO));

		if (Session::get('from')) {$this->from = Session::get('from');}
		if (Session::get('to')) {$this->to = Session::get('to');}

		$this->Portals = new PortalImport();

	}


	public function weekly() {

		$data = $this->Portals->weekly($this->from,$this->to);
		return $data;

	}



}
