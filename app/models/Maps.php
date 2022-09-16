<?php

namespace app\models;
use \flundr\mvc\Model;
use \app\models\Orders;

class Maps extends Model
{

	function __construct() {
		$this->Orders = new Orders();
	}

	public function colored_geo_orders_3steps() {
		return $this->colored_geo_orders(true);
	}



	public function csv_import($PLZtoThree = false) {

		$file = ROOT . 'import/plz.csv';
		$lines = file($file, FILE_IGNORE_NEW_LINES);
		$orders = array_count_values($lines);

		$file = ROOT . 'import/kuendiger.csv';
		$lines = file($file, FILE_IGNORE_NEW_LINES);
		$cancellations = array_count_values($lines);

		$orders = $this->filter_invalid_plz($orders);
		$cancellations = $this->filter_invalid_plz($cancellations);

		if ($PLZtoThree) {
			$orders = $this->shorten_and_group_PLZ_keys($orders);
			$cancellations = $this->shorten_and_group_PLZ_keys($cancellations);
		}

		$min = 0;

		$multiplier = .9;
		$maxCancellations = max($cancellations) * $multiplier;
		$maxOrders = max($orders) * $multiplier;
		$maxQuote = 100;

		$orderColors = ['c4d7f2', '4b83d4', '0646a4'];
		$cancellationColors = ['d57272', 'ba3838', '6d2121'];

		$areaData = [];
		foreach ($orders as $plz => $amountOfOrders) {
			$areaData[$plz]['orders'] = $amountOfOrders;
			$areaData[$plz]['orderColor'] = '#' . $this->percentage_color($areaData[$plz]['orders'], $min, $maxOrders, $orderColors);
			$areaData[$plz]['cancellations'] = $cancellations[$plz] ?? 0;
			$areaData[$plz]['cancellationColor'] = '#' . $this->percentage_color($areaData[$plz]['cancellations'], $min, $maxCancellations, $cancellationColors);
			$areaData[$plz]['cancellationQuote'] = round($areaData[$plz]['cancellations'] / $areaData[$plz]['orders']*100);
			$areaData[$plz]['cancellationQuoteColor'] = '#' . $this->percentage_color($areaData[$plz]['cancellationQuote'], $min, $maxQuote, $cancellationColors);
		}

		return $areaData;

	}


	public function colored_plz_users($PLZtoThree = false) {

		$readers = new Readers();
		$zipCodes = $readers->users_by_zipcode();
		$zipCodes = $this->filter_invalid_plz($zipCodes);
		//return $this->Orders->group_by('customer_postcode');

		if ($PLZtoThree) {
			$zipCodes = $this->shorten_and_group_PLZ_keys($zipCodes);
		}

		$min = -3;

		$multiplier = 0.5;
		$maxAmount = max($zipCodes) * $multiplier;
		$maxQuote = 100;

		$zipCodeColors = ['a3bf6a', '3a7b40', '0a3c11'];

		$areaData = [];
		foreach ($zipCodes as $plz => $amount) {
			$areaData[$plz]['orders'] = $amount;
			$areaData[$plz]['orderColor'] = '#' . $this->percentage_color($areaData[$plz]['orders'], $min, $maxAmount, $zipCodeColors);
		}

		return $areaData;

	}



	public function colored_geo_orders($PLZtoThree = false) {

		$orders = $this->Orders->group_by('customer_postcode');

		$cancellations = $this->Orders->group_by('customer_postcode', 'conversions.cancelled = 1');

		$orders = $this->filter_invalid_plz($orders);
		$cancellations = $this->filter_invalid_plz($cancellations);

		if ($PLZtoThree) {
			$orders = $this->shorten_and_group_PLZ_keys($orders);
			$cancellations = $this->shorten_and_group_PLZ_keys($cancellations);
		}

		$min = -3;

		$multiplier = 0.5;
		$maxCancellations = max($cancellations) * $multiplier;
		$maxOrders = max($orders) * $multiplier;
		$maxQuote = 100;

		$orderColors = ['a3bf6a', '3a7b40', '0a3c11'];
		$cancellationColors = ['d57272', 'ba3838', '6d2121'];

		$areaData = [];
		foreach ($orders as $plz => $amountOfOrders) {
			$areaData[$plz]['orders'] = $amountOfOrders;
			$areaData[$plz]['orderColor'] = '#' . $this->percentage_color($areaData[$plz]['orders'], $min, $maxOrders, $orderColors);
			$areaData[$plz]['cancellations'] = $cancellations[$plz] ?? 0;
			$areaData[$plz]['cancellationColor'] = '#' . $this->percentage_color($areaData[$plz]['cancellations'], $min, $maxCancellations, $cancellationColors);
			$areaData[$plz]['cancellationQuote'] = round($areaData[$plz]['cancellations'] / $areaData[$plz]['orders']*100);
			$areaData[$plz]['cancellationQuoteColor'] = '#' . $this->percentage_color($areaData[$plz]['cancellationQuote'], $min, $maxQuote, $cancellationColors);
		}

		return $areaData;

	}



	private function shorten_and_group_PLZ_keys($array, $steps = 3) {

		$output = [];
		foreach (array_keys($array) as $key) {

			$shortkey = substr($key, 0, $steps);

			if (isset($output[$shortkey])) {
				$output[$shortkey] = $output[$shortkey] + $array[$key];
			}

			else {$output[$shortkey] = $array[$key];}
		}

		return $output;

	}

	private function filter_invalid_plz(array $plz) {

		$pattern = '/^\d{5}$/';
		$faultyEntries = preg_grep($pattern, array_keys($plz), true);

		foreach ($faultyEntries as $key) {
			unset($plz[$key]);
		}

		return $plz;

	}

	/* Heatmap Colors from https://stackoverflow.com/questions/35848025/php-red-to-green-rgb-color-heatmap - Martin Zvarík */
	public function percentage_color($n, $min, $max, $colors) {
	    $tablecolors = [];
	    $prevcolor = array_shift($colors);
	    foreach ($colors as $color) {
	        $tablecolors = array_merge($tablecolors, $this->create_gradient($prevcolor, $color, 10));
	        $prevcolor = $color;
	    }
	    $max = $max-$min;
	    $n-= $min;
	    if ($n > $max) $n = $max;

	    $ncolor = round(count($tablecolors)/$max * $n)-1;

		if ($ncolor<0) {$ncolor=1;}

	    return $tablecolors[$ncolor];
	}


	private function create_gradient($HexFrom, $HexTo, $ColorSteps) {
	    // credit: Hailwood (stackoverflow)
	    $FromRGB['r'] = hexdec(substr($HexFrom, 0, 2));
	    $FromRGB['g'] = hexdec(substr($HexFrom, 2, 2));
	    $FromRGB['b'] = hexdec(substr($HexFrom, 4, 2));

	    $ToRGB['r'] = hexdec(substr($HexTo, 0, 2));
	    $ToRGB['g'] = hexdec(substr($HexTo, 2, 2));
	    $ToRGB['b'] = hexdec(substr($HexTo, 4, 2));

	    $StepRGB['r'] = ($FromRGB['r'] - $ToRGB['r']) / ($ColorSteps - 1);
	    $StepRGB['g'] = ($FromRGB['g'] - $ToRGB['g']) / ($ColorSteps - 1);
	    $StepRGB['b'] = ($FromRGB['b'] - $ToRGB['b']) / ($ColorSteps - 1);

	    $GradientColors = array();

	    for($i = 0; $i <= $ColorSteps; $i++) {
	        $RGB['r'] = floor($FromRGB['r'] - ($StepRGB['r'] * $i));
	        $RGB['g'] = floor($FromRGB['g'] - ($StepRGB['g'] * $i));
	        $RGB['b'] = floor($FromRGB['b'] - ($StepRGB['b'] * $i));

	        $HexRGB['r'] = sprintf('%02x', ($RGB['r']));
	        $HexRGB['g'] = sprintf('%02x', ($RGB['g']));
	        $HexRGB['b'] = sprintf('%02x', ($RGB['b']));

	        $GradientColors[] = implode(NULL, $HexRGB);
	    }
	    $GradientColors = array_filter($GradientColors, function($val){
	        return (strlen($val) == 6 ? true : false );
	    });
	    return $GradientColors;
	}

}
