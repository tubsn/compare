<?php

namespace app\models;
use flundr\utility\Session;

class Charts
{

	function __construct() {
		$this->engine = new Chartengine();
	}

	public function new($chartData) {
		return $this->create($chartData);
	}

	public function create($chartData) {
		return $this->engine->chart_from_scratch($chartData);
	}

	public function convert($array, $metricAsInteger = false) {
		return $this->engine->array_to_chartdata($array, $metricAsInteger);
	}

	public function implode($array) {
		return $this->engine->implode($array);
	}

	public function convert_as_integer($array) {
		return $this->engine->array_to_chartdata($array, true);
	}

	public function cut($chartDataString, $amountOfElements = 1) {
		return $this->engine->cutoff($chartDataString, $amountOfElements);
	}

	public function cut_left($chartDataString, $amountOfElements = 1) {
		return $this->engine->cutoff_left($chartDataString, $amountOfElements);
	}

	public function get($chartname, $parameters = null) {

		if (!method_exists($this, $chartname)) {
			return "\n<pre style=\"text-align:center\"><mark>Achtung: Chart '". $chartname .  "' nicht gefunden</mark></pre>\n";
		}

		if (is_array($parameters)) {
			return call_user_func_array(array($this, $chartname), $parameters);
		}

		if (isset($parameters)) {
			return call_user_func(array($this, $chartname), $parameters);
		}

		return call_user_func(array($this, $chartname));
	}

	private function timeframe() {
		$from = new \DateTime(Session::get('from'));
		$to = new \DateTime(Session::get('to'));
		$timeframe = $from->diff($to);
		$days = $timeframe->format('%a');
		return $days;
	}

	/*
	********  Start of Predefined Charts ********
	*/

	public function cancellations_by_retention_days($height = 400) {

		$orders = new Orders();
		$orders = $this->convert($orders->cancelled_by_retention_days());

		$data = [
			'metric' => $orders['cancelled_orders'],
			'dimension' => $orders['dimensions'],
			'color' => '#f77474',
			'height' => $height,
			'showValues' => false,
			'name' => 'Kündiger',
			'prefix' => 'Tag ',
			'template' => 'charts/default_bar_chart',
		];

		return $this->create($data);

	}

	public function first_day_churns_by($group = 'article_ressort') {

		$orders = new Orders();
		$conversions = $orders->group_by($group);
		$churners = $orders->group_by($group, 'retention = 0');

		if (empty($churners)) {return '<span class="orangebg">Keine Daten für <b>'.$group.'</b></span>';}

		// Calculate Churn Quote
		$churnersPercent = $churners;
		foreach ($churnersPercent as $key => $value) {
			if (!isset($conversions[$key])) {continue;}
			$churnersPercent[$key] = round($value / $conversions[$key] * 100,1);
		}

		$dimension = $this->engine->implode_with_caps(array_keys($churners));
		$churners = $this->engine->implode(array_values($churners));
		$churnersPercent = $this->engine->implode(array_values($churnersPercent));

		$data = [
			'metric' => [$churners, $churnersPercent],
			'dimension' => $dimension,
			'color' => '#f77474',
			'height' => 400,
			'showValues' => false,
			'name' => ['Kündiger am Bestelltag','Anteil an Gesamtbestellungen'],
			//'percent' => true,
			'legend' => 'top',
			'template' => 'charts/default_bar_chart',
		];

		return $this->create($data);

	}

	public function longterm_orders() {

		$chart = new Chartengine();
		$longterm = new Longterm();
		$longterm = $longterm->chartdata('orders');
		$chart->metric = $longterm['orders'];
		$chart->dimension = $longterm['dimensions'];
		$chart->name = 'Bestellungen';
		$chart->height = 300;
		$chart->color = '#df886d';
		$chart->area = true;
		$chart->template = 'charts/default_line_chart';
		return $chart->init();

	}


	public function longterm_product_orders($product) {

		$chart = new Chartengine();
		$orders = new Orders();
		$jahresbos = $orders->product_development($product);
		$jahresbos = $this->convert($jahresbos);

		$chart->metric = $jahresbos[$product];
		$chart->dimension = $jahresbos['dimensions'];
		$chart->name = 'Bestellungen';
		$chart->height = 300;
		$chart->color = '#df886d';
		$chart->area = true;
		$chart->template = 'charts/default_line_chart';
		return $chart->init();

	}

	public function epaper_stats_by_ressort($ressort = null) {

		$chart = new Chartengine();
		$epaper = new Epaper();
		$ressortStats = $epaper->ressort_stats($ressort);
		$ressortStats = $this->convert($ressortStats);

		$chart->metric = $ressortStats['pageviews'];
		$chart->dimension = $ressortStats['dimensions'];
		$chart->name = 'Pageviews';
		$chart->height = 300;
		$chart->color = '#6088b4';
		$chart->area = true;
		//$chart->tickamount = 6;
		$chart->template = 'charts/default_bar_chart';
		return $chart->init();

	}

	public function epaper_stats_by_date($ressort = null) {

		$chart = new Chartengine();
		$epaper = new Epaper();

		switch (true) {
			case $this->timeframe() > 91: $dateStats = $epaper->month_stats($ressort); break;
			case $this->timeframe() > 31: $dateStats = $epaper->week_stats($ressort); break;
			default: $dateStats = $epaper->date_stats($ressort); break;
		}

		$dateStats = $this->convert($dateStats);

		$chart->metric = $dateStats['pageviews'];
		$chart->dimension = $dateStats['dimensions'];
		$chart->name = 'Pageviews';
		$chart->height = 300;
		$chart->color = '#6088b4';
		$chart->area = true;
		//if ($this->timeframe() > 31) {$chart->tickamount = 12;}
		//if ($this->timeframe() > 91) {$chart->tickamount = 18;}
		$chart->template = 'charts/default_line_chart';
		return $chart->init();

	}



	public function pageviewsByRessort() {

		$chart = new Chartengine();
		$chart->kpi = 'pageviews';
		$chart->name = 'Pageviews';
		$chart->color = '#6088b4';
		return $chart->init();

	}

	public function mediatime_by($group = 'ressort') {

		$chart = new Chartengine();
		$chart->kpi = 'avgmediatime';
		$chart->groupby = $group;
		$chart->operation = 'avg';
		$chart->name = 'Mediatime';
		$chart->color = '#6ea681';
		$chart->seconds = 's';
		return $chart->init();

	}

	public function subscriberviews_by($group = 'ressort') {

		$chart = new Chartengine();
		$chart->kpi = 'subscriberviews';
		$chart->groupby = $group;
		$chart->name = 'Subscriberviews';
		$chart->color = '#314e6f';
		return $chart->init();

	}

	public function avg_subscriberviews_by($group = 'ressort', $sort = null) {

		$chart = new Chartengine();
		$chart->kpi = 'subscriberviews';
		$chart->groupby = $group;
		$chart->operation = 'avg';
		$chart->sort = $sort;
		$chart->name = 'Durchschnittliche Subscriberviews';
		$chart->color = '#314e6f';
		return $chart->init();

	}

	public function subscriberviews_by_date() {

		$chart = new Chartengine();
		$chart->kpi = 'subscriberviews';
		$chart->groupby = 'DATE(pubdate)';
		$chart->name = 'Subscriberviews';
		$chart->color = '#314e6f';
		$chart->template = 'charts/default_line_chart';
		if ($this->timeframe() > 31) {$chart->groupby = "DATE_FORMAT(pubdate,'%Y-%u')"; $chart->suffix = ' KW';}
		if ($this->timeframe() > 91) {$chart->groupby = "DATE_FORMAT(pubdate,'%Y-%m')"; $chart->suffix = null;}
		return $chart->init();

	}

	public function subscriberviews_by_date_wholepage() {

		$chart = new Chartengine();
		$chart->kpi = 'subscriberviews';
		$chart->source = 'DailyKPIs';
		$chart->groupby = 'DATE(date)';
		$chart->name = 'Subscriberviews';
		$chart->color = '#314e6f';
		$chart->template = 'charts/default_line_chart';
		if ($this->timeframe() > 31) {$chart->groupby = "DATE_FORMAT(date,'%Y-%u')"; $chart->suffix = ' KW';}
		if ($this->timeframe() > 91) {$chart->groupby = "DATE_FORMAT(date,'%Y-%m')"; $chart->suffix = null;}
		return $chart->init();

	}

	public function subscriberquote_by($group = 'ressort', $sort = null) {

		$chart = new Chartengine();
		$chart->kpi = 'round(sum(subscriberviews)/sum(pageviews)*100)';
		$chart->groupby = $group;
		$chart->operation = null;
		$chart->name = 'Anteil an Subscriberviews';
		$chart->percent = true;
		$chart->color = '#314e6f';
		$chart->sort = $sort;
		$chart->template = 'charts/default_bar_chart';
		return $chart->init();

	}

	public function avg_pageviews_by($group = 'ressort', $sort = null) {

		$chart = new Chartengine();
		$chart->kpi = 'pageviews';
		$chart->groupby = $group;
		$chart->operation = 'avg';
		$chart->sort = $sort;
		$chart->name = 'Durchschnittliche Pageviews';
		$chart->color = '#6088b4';
		return $chart->init();

	}

	public function conversions_by_date() {

		$chart = new Chartengine();
		$chart->source = 'Orders';
		$chart->kpi = 'order_id';
		$chart->groupby = 'DATE(order_date)';
		$chart->operation = 'count';
		$chart->name = 'Conversions';
		$chart->color = '#df886d';
		$chart->template = 'charts/default_line_chart';
		if ($this->timeframe() > 31) {$chart->groupby = "DATE_FORMAT(order_date,'%Y-%u')"; $chart->suffix = ' KW';}
		if ($this->timeframe() > 91) {$chart->groupby = "DATE_FORMAT(order_date,'%Y-%m')"; $chart->suffix = null;}
		return $chart->init();

	}

	public function conversions_by_weekday($group = 'article_ressort', $sort = null) {

		$chart = new Chartengine();
		$chart->source = 'Orders';
		$chart->kpi = 'order_id';
		$chart->groupby = "DATE_FORMAT(order_date,'%W')";
		$chart->operation = 'count';
		$chart->name = 'Conversions';
		$chart->color = '#df886d';
		$chart->sort = 'WEEKDAY';
		$chart->showValues = false;
		$chart->template = 'charts/default_bar_chart';
		return $chart->init();

	}

	public function conversions_by_daytime($group = 'article_ressort', $sort = null) {

		$chart = new Chartengine();
		$chart->source = 'Orders';
		$chart->kpi = 'order_id';
		$chart->groupby = "DATE_FORMAT(order_date,'%k')";
		$chart->operation = 'count';
		$chart->name = 'Conversions';
		$chart->color = '#df886d';
		$chart->sort = 'HOUR';
		$chart->showValues = false;
		$chart->template = 'charts/default_bar_chart';
		return $chart->init();

	}

	public function conversions_by($group = 'article_ressort', $sort = null) {

		$chart = new Chartengine();
		$chart->source = 'Orders';
		$chart->kpi = 'order_id';
		$chart->groupby = $group;
		$chart->operation = 'count';
		$chart->name = 'Conversions';
		$chart->sort = $sort;
		$chart->showValues = true;
		$chart->color = '#df886d';
		return $chart->init();

	}

	public function conversionrate_by($group = 'ressort', $sort = 'DESC') {

		$chart = new Chartengine();
		$chart->kpi = '( sum(conversions) / sum(sessions))*100';
		$chart->groupby = $group;
		$chart->operation = null;
		$chart->name = 'Conversonrate';
		$chart->color = '#df886d';
		$chart->sort = $sort;
		$chart->template = 'charts/default_bar_chart';
		return $chart->init();

	}

	public function avg_retention_by($group = 'article_ressort', $sort = 'DESC') {

		$chart = new Chartengine();
		$chart->source = 'Orders';
		$chart->kpi = 'retention';
		$chart->groupby = $group;
		$chart->filter = 'cancelled = 1';
		$chart->operation = 'avg';
		$chart->name = 'Haltedauer';
		$chart->showValues = true;
		$chart->color = '#f77474';
		$chart->sort = $sort;
		return $chart->init();

	}

	public function articles_by($group = 'ressort', $sort = null) {

		$chart = new Chartengine();
		$chart->kpi = 'id';
		$chart->groupby = $group;
		$chart->operation = 'count';
		$chart->name = 'produzierte Artikel';
		$chart->color = '#515151';
		$chart->showValues = false;
		$chart->sort = $sort;
		$chart->template = 'charts/default_bar_chart';

		return $chart->init();

	}

	public function articles_by_weekday() {
		return $this->articles_by("DATE_FORMAT(pubdate,'%W')", 'WEEKDAY');
	}

	public function articles_by_time() {
		return $this->articles_by("DATE_FORMAT(pubdate,'%k')", 'HOUR');
	}

	public function articles_by_date() {

		$chart = new Chartengine();
		$chart->kpi = 'id';
		$chart->groupby = "DATE(pubdate)";
		$chart->operation = 'count';
		$chart->name = 'produzierte Artikel';
		$chart->color = '#515151';
		$chart->template = 'charts/default_line_chart';

		if ($this->timeframe() > 31) {
			$chart->groupby = "DATE_FORMAT(pubdate,'%Y-%u')";
			$chart->suffix = ' KW';
		}

		if ($this->timeframe() > 91) {
			$chart->groupby = "DATE_FORMAT(pubdate,'%Y-%m')";
			$chart->suffix = null;
		}

		return $chart->init();

	}

	public function discover_articles_by($group = 'ressort', $sort = null) {

		$chart = new Chartengine();
		$chart->kpi = 'SUM( IF(discover = 1, 1, 0) )';
		$chart->groupby = $group;
		$chart->operation = null;
		$chart->name = 'Discover Artikel';
		$chart->color = '#515151';
		$chart->showValues = false;
		$chart->sort = $sort;
		$chart->template = 'charts/default_bar_chart';

		return $chart->init();

	}

	public function discover_articles_by_date($sort = null) {

		$chart = new Chartengine();
		$chart->kpi = 'SUM( IF(discover = 1, 1, 0) )';
		$chart->groupby = 'DATE(pubdate)';
		$chart->operation = null;
		$chart->name = 'Discover Artikel';
		$chart->color = '#515151';
		$chart->showValues = false;
		$chart->sort = $sort;
		$chart->template = 'charts/default_line_chart';

		if ($this->timeframe() > 31) {
			$chart->groupby = "DATE_FORMAT(pubdate,'%Y-%u')";
			$chart->suffix = ' KW';
		}

		if ($this->timeframe() > 91) {
			$chart->groupby = "DATE_FORMAT(pubdate,'%Y-%m')";
			$chart->suffix = null;
		}

		return $chart->init();

	}


	public function plusquote_by_date() {

		$chart = new Chartengine();
		$chart->kpi = 'round(SUM( IF(plus = 1, 1, 0) ) / count(id) * 100,2)';
		$chart->groupby = "DATE(pubdate)";
		$chart->operation = null;
		$chart->name = 'Plusquote in %';
		$chart->color = '#cc9b8d';
		$chart->template = 'charts/default_line_chart';

		if ($this->timeframe() > 31) {
			$chart->groupby = "DATE_FORMAT(pubdate,'%Y-%u')";
			$chart->suffix = ' KW';
		}

		if ($this->timeframe() > 91) {
			$chart->groupby = "DATE_FORMAT(pubdate,'%Y-%m')";
			$chart->suffix = null;
		}

		return $chart->init();

	}


	public function cancellations_by_ressort_filtered() {

		$chart = new Chartengine();
		$chart->source = 'Orders';
		$chart->kpi = 'order_id';
		$chart->groupby = 'article_ressort';

		//$chart->filter = 'cancelled = 1 AND retention < 31';
		//$chart->filter = 'cancelled = 1 AND retention < 90';
		$chart->filter = 'cancelled = 1 AND retention = 0';
		$chart->operation = 'count';
		$chart->name = 'Kündiger';
		$chart->showValues = true;
		$chart->color = '#f77474';
		$chart->sort = 'ASC';

		return $chart->init();

	}

}
