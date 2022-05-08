<?php

namespace app\models;

use flundr\utility\Session;
use flundr\rendering\TemplateEngine;

class Chartengine
{

	public $source;
	public $kpi;
	public $groupby = 'ressort';
	public $operation = 'sum';
	public $filter = null;
	public $sort = null;
	public $showValues = false;
	public $color;
	public $height = 300;
	public $template = 'charts/default_bar_chart';
	public $name;
	public $area;
	public $percent;
	public $seconds;
	public $stacked;
	public $xfont;
	public $prefix;
	public $suffix;
	public $legend;
	public $tickamount;

	public $metric;
	public $dimension;

	private $templateEngine;
	private $datasource;
	private $data;

	public function chart_from_scratch($chartData) {

		if (isset($chartData['template'])) {
			$template = $chartData['template'];
		} else $template = $this->template;

		return $this->render($template, $chartData);
	}

	public function init() {

		if (empty($this->metric) || empty($this->dimension)) {
			$this->load_data();
			$this->sort();
			$this->to_aphex_chart();
		}

		return $this->export();

	}

	public function render($template, $data) {
		$data['id'] = uniqid(); // add a Unique ChartID for css classes
		$templateEngine = new TemplateEngine($template, $data);
		return $templateEngine->burn();
	}

	public function export() {

		$data = [
			'metric' => $this->metric,
			'dimension' => $this->dimension,
			'color' => $this->color,
			'height' => $this->height,
			'area' => $this->area,
			'percent' => $this->percent,
			'seconds' => $this->seconds,
			'xfont' => $this->xfont,
			'legend' => $this->legend,
			'tickamount' => $this->tickamount,
			'prefix' => $this->prefix,
			'suffix' => $this->suffix,
			'showValues' => $this->showValues,
			'name' => $this->name,
		];

		return $this->render($this->template, $data);

	}

	public function array_to_chartdata($data, $asInteger = false) {

		$output['dimensions'] = array_keys($data);

		// For Multidimensional Chart Arrays e.g. [2022-01-01][data][values]
		$values = array_values($data);
		if (isset($values[0]) && is_array($values[0])) {
			$kpis = array_keys(array_values($data)[0]);
			foreach ($kpis as $key) {$output[$key] = [];}

			foreach ($data as $set) {
				foreach ($set as $key => $value) {
					if (isset($output[$key])) {
						array_push($output[$key], $value);
					}
				}
			}
		}

		foreach ($output as $key => $value) {
			if ($key == 'dimensions') {
				$output[$key] = $this->implode($value, 0);
			}
			else {
				$output[$key] = $this->implode($value, $asInteger);
			}
		}

		return $output;

	}

	public function cutoff($chartDataString, $amountOfElements) {
		if ($amountOfElements == 0) {return $chartDataString;}
		$output = explode(',', $chartDataString);
		array_splice($output, $amountOfElements * -1);
		return implode(',', $output);
	}

	public function cutoff_left($chartDataString, $amountOfElements) {
		if ($amountOfElements == 0) {return $chartDataString;}
		$output = explode(',', $chartDataString);
		array_splice($output, 0, $amountOfElements);
		return implode(',', $output);
	}


	private function to_aphex_chart() {

		$metric = null; $dimension = null;
		foreach ($this->data as $data) {

			if (empty($data[$this->groupby])) {continue;}
			if ($this->operation == 'avg') {
				$data[$this->kpi] = round($data[$this->kpi]);
			}

			if (empty($data[$this->kpi])) {$data[$this->kpi] = 0;}

			$metric .= $data[$this->kpi] . ',';
			$dimension .= "'" . ucfirst($data[$this->groupby]) . "'" . ',';

		}

		$this->metric = rtrim($metric, ',');
		$this->dimension = rtrim($dimension, ',');

	}

	public function implode($array, $asInteger = false, $caps = false) {
		if ($caps) {$array = array_map(function ($set) { return ucfirst($set); }, $array);}
		if ($asInteger) {
			$string = implode(",", $array);
			return trim($string, ',');
		}
		$string = "'" . implode("','", $array) . "'";
		$string = str_replace("''", 'null', $string);

		return $string;
	}

	public function implode_with_caps($array) {
		return $this->implode($array,false,true);
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
			case 'DailyKPIs': $datasource = new DailyKPIs(); break;
			default: $datasource = new Articles(); break;
		}

		$this->data = $datasource->kpi_grouped_by(
			$this->kpi,
			$this->groupby,
			$this->operation,
			$this->filter
		);
	}


}
