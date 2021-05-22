<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\auth\Auth;
use flundr\utility\Session;

class MassPlenigo extends Controller {

	public function __construct() {
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
		$this->view('CSV');
		$this->models('Plenigo');
	}

	public function export() {

		die('pls do importfile... :D');

		$ids = file_get_contents(CONFIGPATH . '/plenigo-ids.txt');
		$ids = explode_and_trim("\n", $ids);

		$conversions = [];

		foreach ($ids as $id) {
			array_push($conversions, $this->get_plenigo_data($id));
		}

		$viewData['conversions'] = $conversions;

		$this->view->render('export/excel-plenigo', $viewData);

	}

	private function get_plenigo_data($id) {

		$plenigo = $this->Plenigo->order_with_details($id);

		if (!empty($plenigo)) {

			$data['cancelled'] = false;
			$data['retention'] = null;

			$data['customer_id']			= $plenigo['customerID'];
			$data['external_customer_id']	= $plenigo['externalCustomerID'];

			$data['order_product_id']		= $plenigo['productID'];
			$data['order_date']				= $plenigo['orderDate'];
			$data['order_title']			= $plenigo['productTitle'];
			$data['order_price']			= $plenigo['productPrice'];
			$data['order_status']			= $plenigo['orderStatus'];

			if ($plenigo['subscription']) {
				$data['subscription_title']				= $plenigo['subscription']['title'];
				$data['subscription_price']				= $plenigo['subscription']['price'];
				$data['subscription_payment_method']		= $plenigo['subscription']['paymentMethod'];
				$data['subscription_start_date']		= $plenigo['subscription']['startDate'];
				$data['subscription_cancellation_date']	= $plenigo['subscription']['cancellationDate'];
				$data['subscription_end_date']			= $plenigo['subscription']['endDate'];
				$data['subscription_count']				= $plenigo['subscription_count'];

				if ($plenigo['subscription']['cancellationDate']) {
					$data['cancelled'] = true;

					$start = new \DateTime(formatDate($plenigo['subscription']['startDate'], 'Y-m-d'));
					$end = new \DateTime(formatDate($plenigo['subscription']['cancellationDate'], 'Y-m-d'));
					$interval = $start->diff($end);
					$data['retention'] = $interval->format('%r%a');

				}
			}

			if ($plenigo['user']) {
				$data['customer_consent'] = $plenigo['user']['agreementState'];
				$data['customer_status'] = $plenigo['user']['userState'];
				$data['customer_gender'] = $plenigo['user']['gender'];
				$data['customer_city'] = $plenigo['user']['city'];
			}

		}

		return $data;

	}

}
