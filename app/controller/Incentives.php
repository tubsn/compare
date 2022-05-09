<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;

class Incentives extends Controller {

	public function __construct() {

		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		if (!Auth::has_right('incentive')) {
			throw new \Exception("Nothing to see here", 404);
		}

		$this->view('DefaultLayout');
		$this->models('Linkpulse');
	}


	public function incentives() {

		//echo $this->Linkpulse->ressort_stats_base();

		$from = Session::get('from') ?? date('Y-m-d', strtotime('yesterday -6days'));
		$to = Session::get('to') ?? date('Y-m-d', strtotime('yesterday'));
		$weeks = $this->calculate_weeks($from, $to);

		$baseStats = $this->Linkpulse->ressort_stats_cache();
		$baseStats = $this->average_data($baseStats, 34);
		ksort($baseStats);

		$this->view->baseStats = $baseStats;

		$currentStats = $this->Linkpulse->ressort_stats($from, $to);
		$currentStats = $this->average_data($currentStats, $weeks);
		ksort($currentStats);

		$this->view->currentStats = $currentStats;

		$percentageStats = $this->percentage($currentStats, $baseStats);
		$finalStats = $this->css_classes($percentageStats);
		ksort($finalStats);

		$this->view->ressorts = $finalStats;

		Session::set('referer', '/incentives');
		$this->view->title = 'Entwicklung im Vergleich zum ersten Halbjahr';
		$this->view->render('stats/kpi-entwicklung');

	}

	private function average_data($data, $avgBase = 34) {

		return array_map(function($set) use ($avgBase) {
			$set['pageviews'] = round($set['pageviews'] / $avgBase,2);
			$set['subscriberviews'] = round($set['subscriberviews'] / $avgBase,2);
			$set['conversions'] = round($set['conversions'] / $avgBase,2);
			$set['mediatime'] = round($set['mediatime'] / $avgBase,2);
			$set['avgmediatime'] = round($set['avgmediatime'],2);
			return $set;
		}, $data);

	}

	private function percentage($current, $base) {

		$ressorts = [];
		foreach ($current as $ressort => $set) {


			$percentPageviews = $this->calculate_percentage($set['pageviews'], $base[$ressort]['pageviews']);
			$percentSubscriberviews = $this->calculate_percentage($set['subscriberviews'], $base[$ressort]['subscriberviews']);
			$percentConversions = $this->calculate_percentage($set['conversions'], $base[$ressort]['conversions']);
			$percentMediatime = $this->calculate_percentage($set['mediatime'], $base[$ressort]['mediatime']);

			$ressorts[$ressort]['pageviews'] = $percentPageviews;
			$ressorts[$ressort]['subscriberviews'] = $percentSubscriberviews;
			$ressorts[$ressort]['conversions'] = $percentConversions;
			$ressorts[$ressort]['mediatime'] = 	$percentMediatime;

		}

		return $ressorts;
	}

	private function calculate_percentage($current,$base) {
		if ($base == 0) {return 0;}
		$percent = $current / $base * 100;
		return round($percent - 100,1);
	}

	private function css_classes($data) {

		return array_map(function($set) {

			if ($set['pageviews'] >= 10) {$set['pvclass'] = 'bronze';}
			if ($set['pageviews'] >= 20) {$set['pvclass'] = 'silver';}
			if ($set['pageviews'] >= 30) {$set['pvclass'] = 'gold';}
			if ($set['pageviews'] <= -20) {$set['pvclass'] = 'negative';}

			if ($set['subscriberviews'] >= 10) {$set['subclass'] = 'bronze';}
			if ($set['subscriberviews'] >= 20) {$set['subclass'] = 'silver';}
			if ($set['subscriberviews'] >= 30) {$set['subclass'] = 'gold';}
			if ($set['subscriberviews'] <= -20) {$set['subclass'] = 'negative';}

			if ($set['conversions'] >= 10) {$set['convclass'] = 'bronze';}
			if ($set['conversions'] >= 20) {$set['convclass'] = 'silver';}
			if ($set['conversions'] >= 30) {$set['convclass'] = 'gold';}
			if ($set['conversions'] <= -20) {$set['convclass'] = 'negative';}

			if ($set['mediatime'] >= 10) {$set['mclass'] = 'bronze';}
			if ($set['mediatime'] >= 20) {$set['mclass'] = 'silver';}
			if ($set['mediatime'] >= 30) {$set['mclass'] = 'gold';}
			if ($set['mediatime'] <= -20) {$set['mclass'] = 'negative';}

			return $set;
		}, $data);

	}

	private function calculate_weeks($from, $to) {

		$origin = date_create($from);
		$target = date_create($to);
		$interval = date_diff($origin, $target);

		return round($interval->format('%a') / 7);

	}

}
