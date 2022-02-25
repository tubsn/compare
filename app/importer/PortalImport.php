<?php

namespace app\importer;

use \flundr\cache\RequestCache;

class PortalImport
{

	public function __construct() {
	}


	public function orders() {

		$swp = $this->curl('https://reports-swp.lr-digital.de/api/orders');
		$moz = $this->curl('https://reports-moz.lr-digital.de/api/orders');
		$lr = $this->curl('https://reports.lr-digital.de/api/orders');

		return ['SWP' => $swp, 'MOZ' => $moz, 'LR' => $lr];

	}

	public function KPIs() {

		$swp = $this->curl('https://reports-swp.lr-digital.de/api/kpis');
		$moz = $this->curl('https://reports-moz.lr-digital.de/api/kpis');
		$lr = $this->curl('https://reports.lr-digital.de/api/kpis');

		return ['SWP' => $swp, 'MOZ' => $moz, 'LR' => $lr];

	}

	public function articles($swp = null, $moz = null, $lr = null) {

		$swp = $this->curl('https://reports-swp.lr-digital.de/api/article/' . $swp);
		$moz = $this->curl('https://reports-moz.lr-digital.de/api/article/' . $moz);
		$lr = $this->curl('https://reports.lr-digital.de/api/article/' . $lr);

		return ['SWP' => $swp, 'MOZ' => $moz, 'LR' => $lr];

	}


	private function curl($url) {

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

		$recievedData = curl_exec($ch);
		if ($recievedData === false) {
			dd(curl_error($ch));
		}

		curl_close ($ch);

		return json_decode($recievedData, true);

	}

}
