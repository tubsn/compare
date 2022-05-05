<?php

namespace app\models;
use app\importer\AnalyticsImport;
use app\models\helpers\UTMCampaign;
use \flundr\utility\Session;
use \flundr\cache\RequestCache;

class Analytics
{

	private $ga;

	function __construct() {
		$this->ga = new AnalyticsImport;
	}

	public function by_article_id($articleID, $from = '30daysAgo', $to = 'today') {

		$articleID = htmlspecialchars($articleID, ENT_QUOTES, 'UTF-8');
		if (strlen($articleID) != 8) {return false;}

		$this->ga->metrics = 'ga:pageViews,ga:sessions,ga:itemQuantity,ga:timeOnPage,ga:avgTimeOnPage';
		$this->ga->from = $from;
		$this->ga->to = $to;
		$this->ga->dimensions = 'ga:date';
		$this->ga->sort = '-ga:date';
		$this->ga->filters = 'ga:pagePath=@' . $articleID . ';ga:pageviews>0';
		$this->ga->maxResults = '365';

		$details = $this->ga->fetch();

		return [
			'totals' => $this->ga->metric_totals(),
			'details' => $details
		];

	}


	public function buy_intention_by_article_id($articleID, $from = '30daysAgo', $to = 'today') {

		$articleID = htmlspecialchars($articleID, ENT_QUOTES, 'UTF-8');
		if (strlen($articleID) != 8) {return false;}

		$this->ga->metrics = 'ga:totalEvents';
		$this->ga->from = $from;
		$this->ga->to = $to;
		$this->ga->dimensions = 'ga:eventLabel';
		$this->ga->sort = 'ga:eventLabel';
		$this->ga->filters = 'ga:pagePath=@' . $articleID . ';ga:eventLabel=@subscription btn;ga:eventCategory==article';
		$this->ga->maxResults = '365';

		$this->ga->fetch();
		$stats = $this->ga->metric_totals();

		return $stats['Totalevents'] ?? null;

	}


	public function list_buy_intention($dayCount = 5) {

		/* Data is Sampled... Fuck*/

		$this->ga->metrics = 'ga:totalEvents';
		$this->ga->from = '30daysAgo';
		$this->ga->to = 'today';
		$this->ga->dimensions = 'ga:pagePath';
		$this->ga->sort = null;
		$this->ga->filters = 'ga:eventLabel=@register;ga:pagePath=~\d+.html';
		$this->ga->maxResults = '1000';

		return $this->ga->fetch();

		$stats = $this->ga->metric_totals();
		return $stats;

	}


	public function conversions_by_article_id($articleID, $from = '30daysAgo', $to = 'today') {

		$articleID = htmlspecialchars($articleID, ENT_QUOTES, 'UTF-8');
		if (strlen($articleID) != 8) {return false;}

		$this->ga->metrics = 'ga:itemQuantity';
		$this->ga->from = $from;
		$this->ga->to = $to;
		$this->ga->dimensions = 'ga:transactionId,ga:source,ga:city,ga:sessionCount';
		$this->ga->sort = 'ga:transactionId';
		$this->ga->filters = 'ga:pagePath=@' . $articleID . ';ga:itemQuantity>0';
		$this->ga->maxResults = '365';

		return $this->ga->fetch();

	}


	public function sources_by_article_id($articleID, $from = '30daysAgo', $to = 'today') {

		$articleID = htmlspecialchars($articleID, ENT_QUOTES, 'UTF-8');
		if (strlen($articleID) != 8) {return false;}

		$this->ga->metrics = 'ga:pageViews, ga:itemQuantity';
		$this->ga->from = $from;
		$this->ga->to = $to;
		$this->ga->dimensions = 'ga:source';
		$this->ga->sort = '-ga:pageViews';
		$this->ga->filters = 'ga:pagePath=@' . $articleID;
		$this->ga->maxResults = '365';

		return $this->ga->fetch();

	}

	public function transaction_metainfo_as_list($dayCount = 5) {

		$this->ga->metrics = 'ga:transactions';
		$this->ga->from = $dayCount . 'daysAgo';
		$this->ga->to = 'today';
		$this->ga->dimensions = 'ga:transactionId, ga:sessionCount, ga:source, ga:city';
		$this->ga->sort = 'ga:transactionId';
		$this->ga->filters = 'ga:transactions>0';
		$this->ga->maxResults = '2000';

		return $this->ga->fetch();


	}


	public function page_data($dayCount = 3) {

		$this->ga->metrics = 'ga:pageviews, ga:sessions, ga:timeOnPage, ga:avgTimeOnPage';
		$this->ga->from = $dayCount . 'daysAgo';
		$this->ga->to = 'today';
		$this->ga->dimensions = 'ga:date';
		$this->ga->sort = 'ga:date';
		$this->ga->filters = null;
		$this->ga->maxResults = '1000';

		return $this->ga->fetch();

	}


	public function use_timeframe_by_audience($audience) {

		$from = date('Y-m-d', strtotime(DEFAULT_FROM));
		$to = date('Y-m-d', strtotime(DEFAULT_TO));
		if (Session::get('from')) {$from = Session::get('from');}
		if (Session::get('to')) {$to = Session::get('to');}

		if ($from <= '2005-01-01') {$from = '2019-01-01';}

		$this->ga->metrics = 'ga:sessions';
		$this->ga->from = $from;
		$this->ga->to = $to;
		$this->ga->dimensions = 'ga:hour';
		$this->ga->sort = 'ga:hour';
		$this->ga->filters = 'ga:contentGroup1==' . $audience;
		$this->ga->maxResults = '50';

		$cache = new RequestCache('AuciendesByHour' . PORTAL . $audience . $from . $to, 60*60);
		$data = $cache->get();
		if (empty($data)) {
			$data = $this->ga->fetch();
			$cache->save($data);
		}

		return $data;

	}



	public function utm_campaigns($dayCount = 30, $grouped = false) {

		$this->ga->metrics = 'ga:transactions';
		$this->ga->from = $dayCount . 'daysAgo';
		$this->ga->to = 'today';

		if ($grouped) {
		$this->ga->dimensions = 'ga:medium';
		$this->ga->sort = '-ga:transactions';
		}
		else {
		$this->ga->dimensions = 'ga:date,ga:source,ga:medium,ga:campaign,ga:keyword,ga:transactionId';
		$this->ga->sort = '-ga:date';
		}

		$this->ga->filters = 'ga:transactions>0;ga:medium!=(none);ga:campaign!=(not set)';
		$this->ga->maxResults = '1000';

		return $this->ga->fetch();

	}

}
