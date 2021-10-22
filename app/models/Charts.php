<?php

namespace app\models;

use flundr\utility\Session;
use flundr\rendering\TemplateEngine;

class Charts
{

	public $source;
	public $kpi;
	public $groupby = 'ressort';
	public $operation = 'sum';
	public $sort = null;
	public $showValues;
	public $color;
	public $template = 'charts/default_bar_chart';
	public $name;

	public $metric;
	public $dimension;

	private $chartID;
	private $templateEngine;
	private $datasource;
	private $data;

	function __construct() {}


	public function get($name) {
		return $this->predefined($name);
	}


	public function predefined($name) {

		$chart = new Charts();

		switch ($name) {

			case 'pageviewsByRessort':
				$chart->kpi = 'pageviews';
				$chart->name = 'Pageviews';
				$chart->color = '#6088b4';
			break;

			case 'mediatimeByRessort':
				$chart->kpi = 'avgmediatime';
				$chart->operation = 'avg';
				$chart->name = 'Mediatime';
				$chart->color = '#6ea681';
			break;

			case 'fullMediatimeByRessort':
				$chart->kpi = 'mediatime';
				$chart->operation = 'sum';
				$chart->name = 'Mediatime';
				$chart->color = '#6ea681';
				$chart->template = 'charts/default_bar_chart';
			break;


			case 'subscribersByRessort':
				$chart->kpi = 'subscribers';
				$chart->name = 'Subscribers';
				$chart->color = '#314e6f';
			break;

			case 'subscribersByDate':
				$chart->kpi = 'subscribers';
				$chart->groupby = 'DATE(pubdate)';
				$chart->name = 'Subscribers';
				$chart->color = '#314e6f';
				$chart->template = 'charts/default_line_chart';
				if ($this->timeframe() > 31) {$chart->groupby = "DATE_FORMAT(pubdate,'%Y-%u')";}
				if ($this->timeframe() > 91) {$chart->groupby = "DATE_FORMAT(pubdate,'%Y-%m')";}
			break;

			case 'conversionsByDate':
				$chart->source = 'Orders';
				$chart->kpi = 'order_id';
				$chart->groupby = 'DATE(order_date)';
				$chart->operation = 'count';
				$chart->name = 'Conversions';
				$chart->color = '#df886d';
				$chart->template = 'charts/default_line_chart';
				if ($this->timeframe() > 31) {$chart->groupby = "DATE_FORMAT(order_date,'%Y-%u')";}
				if ($this->timeframe() > 91) {$chart->groupby = "DATE_FORMAT(order_date,'%Y-%m')";}
			break;

			case 'conversionsByWeekday':
				$chart->source = 'Orders';
				$chart->kpi = 'order_id';
				$chart->groupby = "DATE_FORMAT(order_date,'%W')";
				$chart->operation = 'count';
				$chart->name = 'Conversions';
				$chart->color = '#df886d';
				$chart->sort = 'WEEKDAY';
				$chart->template = 'charts/default_bar_chart';
			break;

			case 'conversionsByTime':
				$chart->source = 'Orders';
				$chart->kpi = 'order_id';
				$chart->groupby = "DATE_FORMAT(order_date,'%k')";
				$chart->operation = 'count';
				$chart->name = 'Conversions';
				$chart->color = '#df886d';
				$chart->sort = 'HOUR';
				$chart->template = 'charts/default_bar_chart';
			break;


			case 'conversionsByRessort':
				$chart->source = 'Orders';
				$chart->kpi = 'order_id';
				$chart->groupby = 'article_ressort';
				$chart->operation = 'count';
				$chart->name = 'Conversions';
				$chart->color = '#df886d';
			break;

			case 'conversionsByRessortWithValues':
				$chart->source = 'Orders';
				$chart->kpi = 'order_id';
				$chart->groupby = 'article_ressort';
				$chart->operation = 'count';
				$chart->name = 'Conversions';
				$chart->showValues = true;
				$chart->color = '#df886d';
			break;

			case 'avgPageviewsByRessortDashboard':
				$chart->kpi = 'pageviews';
				$chart->groupby = 'ressort';
				$chart->operation = 'avg';
				//$chart->sort = 'DESC';
				$chart->name = 'Durchschnittliche Pageviews';
				$chart->color = '#6088b4';
			break;

			case 'avgPageviewsByRessort':
				$chart->kpi = 'pageviews';
				$chart->groupby = 'ressort';
				$chart->operation = 'avg';
				$chart->sort = 'DESC';
				$chart->name = 'Durchschnittliche Pageviews';
				$chart->color = '#6088b4';
			break;

			case 'avgPageviewsByTag':
				$chart->kpi = 'pageviews';
				$chart->groupby = 'tag';
				$chart->operation = 'avg';
				$chart->sort = 'DESC';
				$chart->name = 'Durchschnittliche Pageviews';
				$chart->color = '#6088b4';
			break;

			case 'avgPageviewsByAudience':
				$chart->kpi = 'pageviews';
				$chart->groupby = 'audience';
				$chart->operation = 'avg';
				$chart->sort = 'DESC';
				$chart->name = 'Durchschnittliche Pageviews';
				$chart->color = '#6088b4';
			break;

			case 'avgPageviewsByType':
				$chart->kpi = 'pageviews';
				$chart->groupby = 'type';
				$chart->operation = 'avg';
				$chart->sort = 'DESC';
				$chart->name = 'Durchschnittliche Pageviews';
				$chart->color = '#6088b4';
			break;

			case 'articlesByDate':
				$chart->kpi = 'id';
				$chart->groupby = "DATE(pubdate)";
				$chart->operation = 'count';
				$chart->name = 'produzierte Artikel';
				$chart->color = '#515151';
				//$chart->showValues = true;
				$chart->template = 'charts/default_line_chart';
				if ($this->timeframe() > 31) {$chart->groupby = "DATE_FORMAT(pubdate,'%Y-%u')";}
				if ($this->timeframe() > 91) {$chart->groupby = "DATE_FORMAT(pubdate,'%Y-%m')";}
			break;

			case 'plusquoteByDate':
				//$chart->kpi = 'id';
				//$chart->kpi = 'COUNT(if(plus = 1, 1, 0))';
				$chart->kpi = 'round(SUM( IF(plus = 1, 1, 0) ) / count(id) * 100,2)';

				$chart->groupby = "DATE(pubdate)";
				$chart->operation = null;
				$chart->name = 'Plusquote in %';
				$chart->color = '#cc9b8d';
				//$chart->showValues = true;
				$chart->template = 'charts/default_line_chart';
				if ($this->timeframe() > 31) {$chart->groupby = "DATE_FORMAT(pubdate,'%Y-%u')";}
				if ($this->timeframe() > 91) {$chart->groupby = "DATE_FORMAT(pubdate,'%Y-%m')";}
			break;


			case 'articlesByWeekday':
				$chart->kpi = 'id';
				$chart->groupby = "DATE_FORMAT(pubdate,'%W')";
				$chart->operation = 'count';
				$chart->name = 'produzierte Artikel';
				$chart->color = '#515151';
				$chart->sort = 'WEEKDAY';
				$chart->template = 'charts/default_bar_chart';
			break;

			case 'articlesByTime':
				$chart->kpi = 'id';
				$chart->groupby = "DATE_FORMAT(pubdate,'%k')";
				$chart->operation = 'count';
				$chart->name = 'produzierte Artikel';
				$chart->color = '#515151';
				$chart->sort = 'HOUR';
				$chart->template = 'charts/default_bar_chart';
			break;

			case 'articlesByRessort':
				$chart->kpi = 'id';
				$chart->groupby = 'ressort';
				$chart->operation = 'count';
				$chart->name = 'produzierte Artikel';
				$chart->color = '#515151';
				//$chart->showValues = true;
				$chart->template = 'charts/default_bar_chart';
			break;

			case 'conversionRateByRessort':
				$chart->kpi = '( sum(conversions) / sum(sessions))*100';
				$chart->groupby = 'ressort';
				$chart->operation = null;
				$chart->name = 'Conversonrate';
				$chart->color = '#df886d';
				//$chart->sort = 'DESC';
				//$chart->showValues = false;
				$chart->template = 'charts/default_bar_chart';
			break;

			case 'conversionsByType':
				$chart->kpi = 'conversions';
				$chart->groupby = 'type';
				$chart->operation = 'sum';
				$chart->name = 'Conversonrate';
				$chart->color = '#df886d';
				//$chart->sort = 'DESC';
				$chart->showValues = true;
				$chart->template = 'charts/default_bar_chart';
			break;

			case 'subscriberQuoteByRessort':
				$chart->kpi = 'round(sum(subscribers)/sum(pageviews)*100)';
				$chart->groupby = 'ressort';
				$chart->operation = null;
				$chart->name = 'Subscriber Anteil in %';
				$chart->color = '#314e6f';
				//$chart->showValues = true;
				$chart->sort = 'DESC';
				$chart->template = 'charts/default_bar_chart';
			break;

			case 'subscriberQuoteByType':
				$chart->kpi = 'round(sum(subscribers)/sum(pageviews)*100)';
				$chart->groupby = 'type';
				$chart->operation = null;
				$chart->name = 'Subscriber Anteil in %';
				$chart->color = '#314e6f';
				$chart->sort = 'DESC';
				//$chart->showValues = true;
				$chart->template = 'charts/default_bar_chart';
			break;

			case 'subscriberQuoteByAudience':
				$chart->kpi = 'round(sum(subscribers)/sum(pageviews)*100)';
				$chart->groupby = 'audience';
				$chart->operation = null;
				$chart->name = 'Subscriber Anteil in %';
				$chart->color = '#314e6f';
				$chart->sort = 'DESC';
				//$chart->showValues = true;
				$chart->template = 'charts/default_bar_chart';
			break;

			case 'subscriberQuoteByTag':
				$chart->kpi = 'round(sum(subscribers)/sum(pageviews)*100)';
				$chart->groupby = 'tag';
				$chart->operation = null;
				$chart->name = 'Subscriber Anteil in %';
				$chart->color = '#314e6f';
				$chart->sort = 'DESC';
				//$chart->showValues = true;
				$chart->template = 'charts/default_bar_chart';
			break;

			default: return null; break;
		}

		return $chart->init();

	}



	public function init() {

		$this->chartID = uniqid();
		$this->load_data();
		$this->sort();
		$this->to_aphex_chart();
		return $this->export();

	}

	public function render($template, $data) {
		$templateEngine = new TemplateEngine($template, $data);
		return $templateEngine->burn();
	}

	private function export() {

		$data = [
			'metric' => $this->metric,
			'dimension' => $this->dimension,
			'color' => $this->color,
			'showValues' => $this->showValues,
			'name' => $this->name,
			'id' => $this->chartID,
		];

		return $this->render($this->template, $data);

	}

	private function to_aphex_chart() {

		$metric = null; $dimension = null;
		foreach ($this->data as $data) {

			if (empty($data[$this->groupby])) {continue;}
			if ($this->operation == 'avg') {
				$data[$this->kpi] = round($data[$this->kpi]);
			}

			$metric .= $data[$this->kpi] . ',';
			$dimension .= "'" . ucfirst($data[$this->groupby]) . "'" . ',';

		}

		$this->metric = rtrim($metric, ',');
		$this->dimension = rtrim($dimension, ',');

	}

	public function sort() {
		if (!$this->sort) {return;}
		if ($this->sort == 'DESC') {$this->sort_desc(); return;}
		if ($this->sort == 'WEEKDAY') {$this->sort_weekday(); return;}
		if ($this->sort == 'HOUR') {$this->sort_hour(); return;}
		$this->sort_asc();
	}

	private function sort_desc() {
		usort($this->data, function($a, $b) {
		    return $b[$this->kpi] <=> $a[$this->kpi];
		});
	}

	private function sort_asc() {
		usort($this->data, function($a, $b) {
		    return $a[$this->kpi] <=> $b[$this->kpi];
		});
	}

	private function sort_hour() {

		$output = [
			'0' => null,
			'1' => null,
			'2' => null,
			'3' => null,
			'4' => null,
			'5' => null,
			'6' => null,
			'7' => null,
			'8' => null,
			'9' => null,
			'10' => null,
			'11' => null,
			'12' => null,
			'13' => null,
			'14' => null,
			'15' => null,
			'16' => null,
			'17' => null,
			'18' => null,
			'19' => null,
			'20' => null,
			'21' => null,
			'22' => null,
			'23' => null,
		];

		foreach ($this->data as $entry) {
			$output[$entry[$this->groupby]] = $entry;
		}

		$output = array_values($output);
		$this->data = $output;

	}

	private function sort_weekday() {



		$output = [
			'Monday' => null,
			'Tuesday' => null,
			'Wednesday' => null,
			'Thursday' => null,
			'Friday' => null,
			'Saturday' => null,
			'Sunday' => null,
		];

		foreach ($this->data as $entry) {
			$output[$entry[$this->groupby]] = $entry;
		}

		$output = array_values($output);
		$this->data = $output;

	}

	private function load_data() {

		switch ($this->source) {
			case 'Orders': $datasource = new Orders(); break;
			default: $datasource = new Articles(); break;
		}

		$this->data = $datasource->kpi_grouped_by(
			$this->kpi,
			$this->groupby,
			$this->operation
		);
	}


	private function timeframe() {
		$from = new \DateTime(Session::get('from'));
		$to = new \DateTime(Session::get('to'));
		$timeframe = $from->diff($to);
		$days = $timeframe->format('%a');
		return $days;
	}

}
