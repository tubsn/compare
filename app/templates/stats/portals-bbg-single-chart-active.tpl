<main>

<h1 class="text-center"><?=$page['title']?></h1>
<!--<p class="nt text-center">Hinweis: Für Einige Kennzahlen stehen nicht alle Zeiträume zur Verfügung und sind daher abgeschnitten.</p>-->

<?php 
$lastMonth = date('Y-m', strtotime('first day of last month'));
$lastMonthWord = date('M', strtotime('first day of last month'));

$previousMonth = date('Y-m', strtotime('last month -1 month'));
$previousMonthWord = date('M', strtotime('last month -1 month'));

$lrSales = $salesData['LR'][date('Y-m', strtotime('first day of last month'))];
$lrSales1 = $salesData['LR'][date('Y-m', strtotime('first day of last month -1 month'))];
$lrSales2 = $salesData['LR'][date('Y-m', strtotime('first day of last month -2 month'))];

$mozSales = $salesData['MOZ'][date('Y-m', strtotime('first day of last month'))];
$mozSales1 = $salesData['MOZ'][date('Y-m', strtotime('first day of last month -1 month'))];
$mozSales2 = $salesData['MOZ'][date('Y-m', strtotime('first day of last month -2 month'))];

function diff($value1, $value2){
	$num = $value1 - $value2;
	$class = 'pos'; if ($num < 0) {$class = 'neg';}
	$num = sprintf("%+d",$num);
	return '&thinsp;<span class="'.$class.'">'.$num.'</span>';
}
?>

<style>
	h1 {margin-top:2.5em !important;}
	h3 {margin-bottom: 0;}
	th {width: 180px;}
	figure .mb {margin-bottom: 0;}
	.pos, .neg {color:#33a023;font-size: 0.7em;}
	.neg {color: #a02323;}
	.lr {color: #df886d;}
	.moz {color: #0967a8;}
	.backdrop {opacity: 0.8;}
	.backdrop:hover {opacity: 1;}
</style>





	<figure>
		<h3 class="text-center">
			Aktive Kunden <small>(LR: <span class="lr"><?=gnum($lrSales['active'])?></span> | MOZ: <span class="moz"><?=gnum($mozSales['active'])?></span>)</small>
		</h3>
		<?=$charts->create([
			'metric' => [$charts->cut_left($lr['sales']['active'],5),
			 			 $charts->cut_left($moz['sales']['active'],5),],
			'dimension' => $charts->cut_left($lr['sales']['dimensions'],5),
			'color' => ['#df886d', '#0967a8'],
			'height' => 650,
			//'legend' => 'top',
			'area' => false,
			'stacked' => true,
			'showValues' => false,
			'name' => ['LR', 'MOZ'],
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>






</main>
