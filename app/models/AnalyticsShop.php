<?php

namespace app\models;
use app\importer\AnalyticsImport;
use app\models\helpers\UTMCampaign;
use \flundr\cache\RequestCache;
use \flundr\utility\Session;

class AnalyticsShop
{

	private $ga;

	function __construct() {
		$this->ga = new AnalyticsImport;
		if (PORTAL == 'LR') {$this->ga->profileViewID = 204494427;}
		if (PORTAL == 'MOZ') {$this->ga->profileViewID = 227572285;}
		if (PORTAL == 'SWP') {throw new \Exception("SWP Analytics Zugriff nicht aktiviert", 1);}

		$this->from = date('Y-m-d', strtotime('today -30days'));
		$this->to = date('Y-m-d', strtotime('today'));

		if (Session::get('from')) {$this->from = Session::get('from');}
		if (Session::get('to')) {$this->to = Session::get('to');}

	}

	public function utm_campaigns_shop($grouped = false) {

		$this->ga->metrics = 'ga:transactions';
		$this->ga->from = $this->from;
		$this->ga->to = $this->to;

		if ($grouped) {
		$this->ga->dimensions = 'ga:medium';
		$this->ga->sort = '-ga:transactions';
		}
		else {
		$this->ga->dimensions = 'ga:date,ga:source,ga:medium,ga:campaign,ga:transactionId';
		$this->ga->sort = '-ga:date';
		}

		$this->ga->filters = 'ga:transactions>0;ga:medium!=(none);ga:campaign!=(not set)';
		$this->ga->maxResults = '1000';

		if ($grouped) {$bust = 'grouped';} else {$bust = '';}
		$cache = new RequestCache('fbatransactions' . $bust . PORTAL , 30 * 60);
		$cached = $cache->get();
		if ($cached) {
			return $cached;
		}

		$gaData = $this->ga->fetch();

		$cache->save($gaData);
		return $gaData;

	}

	public function shop_pageviews_filterred_by_utm_campaign($campaignName = null) {

		$campaign = new UTMCampaign('Pageviews');

		$cache = new RequestCache('shop-' . $campaignName . PORTAL , 30 * 60);
		$cached = $cache->get();

		if ($cached) {
			$campaign->import($cached);
			return $campaign;
		}

		$this->ga->metrics = 'ga:pageviews';
		$this->ga->from = $this->from;
		$this->ga->to = $this->to;

		$this->ga->dimensions = 'ga:campaign,ga:source,ga:medium,ga:date';
		$this->ga->sort = '-ga:date';

		if ($campaignName) {
			$this->ga->filters = 'ga:pageviews>0;ga:medium!=(none);ga:campaign==' . $campaignName;
		}

		else {
			$this->ga->filters = 'ga:pageviews>0;ga:medium!=(none)';
		}

		$this->ga->maxResults = '1000';

		$gaData = $this->ga->fetch();

		$cache->save($gaData);
		$campaign->import($gaData);

		return $campaign;

	}

	public function shop_events_filterred_by_label($label = null) {

		$label = strip_tags($label);

		$campaign = new UTMCampaign('Totalevents');

		$cache = new RequestCache('shop-' . $label . PORTAL , 30 * 60);
		$cached = $cache->get();

		if ($cached) {
			$campaign->import($cached);
			return $campaign;
		}

		$this->ga->metrics = 'ga:totalEvents';
		$this->ga->from = $this->from;
		$this->ga->to = $this->to;

		$this->ga->dimensions = 'ga:eventLabel,ga:campaign,ga:source,ga:medium,ga:date';
		$this->ga->sort = '-ga:date';

		if ($label) {
			$this->ga->filters = 'ga:totalEvents>0;ga:medium!=(none);ga:eventLabel==' . $label;
		}

		else {
			$this->ga->filters = 'ga:totalEvents>0;ga:medium!=(none)';
		}

		$this->ga->maxResults = '1000';

		$gaData = $this->ga->fetch();

		$cache->save($gaData);
		$campaign->import($gaData);

		return $campaign;

	}

}
