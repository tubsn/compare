<main>

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

if (isset($selectedStart)) {
	$lastMonthWord = substr($selectedMonth,0,-5);

	$lrSales = $salesData['LR'][substr($selectedStart,0,7)];
	$lrSales1 = $salesData['LR'][date('Y-m', strtotime($selectedStart . ' -1 month'))];
	$lrSales2 = $salesData['LR'][date('Y-m', strtotime($selectedStart . ' -2 month'))];

	$mozSales = $salesData['MOZ'][substr($selectedStart,0,7)];
	$mozSales1 = $salesData['MOZ'][date('Y-m', strtotime($selectedStart . ' -1 month'))];
	$mozSales2 = $salesData['MOZ'][date('Y-m', strtotime($selectedStart . ' -2 month'))];
}

$gesamtActive = $lrSales['active'] + $mozSales['active'];

function diff($value1, $value2){
	$num = $value1 - $value2;
	$class = 'pos'; if ($num < 0) {$class = 'neg';}
	$num = sprintf("%+d",$num);
	return '&thinsp;<span class="'.$class.'">'.$num.'</span>';
}
?>

<style>
	h3 {margin-bottom: 0;}
	th {width: 180px;}
	figure .mb {margin-bottom: 0;}
	.pos, .neg {color:#33a023;font-size: 0.7em;}
	.neg {color: #a02323;}
	.lr {color: #df886d;}
	.moz {color: #0967a8;}
	.backdrop {opacity: 0.8;}
	.backdrop:hover {opacity: 1;}
	.js-portal-select {display:none !important;}
</style>

<?php include tpl('navigation/month-picker');?>


<h1 class="text-left"><?=$page['title']?> | aktive Abos: <?=gnum($gesamtActive)?></h1>
<!--<p class="nt text-center">Hinweis: Für Einige Kennzahlen stehen nicht alle Zeiträume zur Verfügung und sind daher abgeschnitten.</p>-->

<div class="col-2" style="grid-template-columns: 3fr 2fr; margin-bottom:1em;">

<figure>
<table class="fancy neutral wide compact" style="table-layout: fixed; border: 2px solid #404040;">

	<tr>
		<th style="text-align:right">Produkt / Monat</th>
		<td style="text-align:center; background-color:#ffb9a4"><i>LR+ | <?=$lastMonthWord?></i></td>
		<td style="text-align:center; background-color:#9bccee"><i>MOZplus | <?=$lastMonthWord?></i></td>
		<td style="text-align:center; background-color:#e1e1e1"><b><i>BB gesamt | <?=$lastMonthWord?></i></b></td>

	</tr>

	<tr>
		<th style="text-align:right">Conversions</th>

		<td class="text-center"><?=gnum($lrSales['orders'])?><?=diff($lrSales['orders'], $lrSales1['orders'])?></td>
		<td class="text-center"><?=gnum($mozSales['orders'])?><?=diff($mozSales['orders'], $mozSales1['orders'])?></td>
		<td class="text-center"><?=gnum($lrSales['orders'] + $mozSales['orders'])?>
			<?=diff($lrSales['orders'] + $mozSales['orders'], $lrSales1['orders'] + $mozSales1['orders'])?></td>
	</tr>		

	<tr>
		<th style="text-align:right">Proben</th>
		<td class="text-center"><?=gnum($lrSales['trial1month'])?><?=diff($lrSales['trial1month'], $lrSales1['trial1month'])?></td>
		<td class="text-center"><?=gnum($mozSales['trial1month'])?><?=diff($mozSales['trial1month'], $mozSales1['trial1month'])?></td>
		<td class="text-center"><?=gnum($lrSales['trial1month'] + $mozSales['trial1month'])?>
			<?=diff($lrSales['trial1month'] + $mozSales['trial1month'], $lrSales1['trial1month'] + $mozSales1['trial1month'])?></td>

	</tr>

	<tr>
		<th style="text-align:right">Jahresabos</th>
		<td class="text-center"><?=gnum($lrSales['yearly'])?><?=diff($lrSales['yearly'], $lrSales1['yearly'])?></td>
		<td class="text-center"><?=gnum($mozSales['yearly'])?><?=diff($mozSales['yearly'], $mozSales1['yearly'])?></td>
		<td class="text-center"><?=gnum($lrSales['yearly'] + $mozSales['yearly'])?>
			<?=diff($lrSales['yearly'] + $mozSales['yearly'], $lrSales1['yearly'] + $mozSales1['yearly'])?></td>
	</tr>

	<tr>
		<th style="text-align:right">Vollabos</th>

		<td class="text-center"><?=gnum($lrSales['paying'])?><?=diff($lrSales['paying'], $lrSales1['paying'])?></td>
		<td class="text-center"><?=gnum($mozSales['paying'])?><?=diff($mozSales['paying'], $mozSales1['paying'])?></td>
		<td class="text-center"><?=gnum($lrSales['paying'] + $mozSales['paying'])?>
			<?=diff($lrSales['paying'] + $mozSales['paying'], $lrSales1['paying'] + $mozSales1['paying'])?></td>
	</tr>

	<tr>
		<th style="text-align:right">Aktive Kunden</th>
		<td class="text-center"><?=gnum($lrSales['active'])?><?=diff($lrSales['active'], $lrSales1['active'])?></td>
		<td class="text-center"><?=gnum($mozSales['active'])?><?=diff($mozSales['active'], $mozSales1['active'])?></td>
		<td class="text-center"><b><?=gnum($lrSales['active'] + $mozSales['active'])?>
			<?=diff($lrSales['active'] + $mozSales['active'], $lrSales1['active'] + $mozSales1['active'])?></b></td>
	</tr>

</table>

<p style="margin-top: -1em; color: #777; margin-bottom: 1em; font-size: 0.7em; text-align:right;"><i>Tabellendaten zum Stichtag Monatsende</i></p>
</figure>


	<figure>
		<h3 class="text-center">
			Netto Zuwachs <small>(LR: <span class="lr"><?=gnum($lrSales['netto'])?></span> | MOZ: <span class="moz"><?=gnum($mozSales['netto'])?></span>)</small>
		</h3>
		<?=$charts->create([
			'metric' => [$charts->cut_left($lr['sales']['netto'],5),
			 			 $charts->cut_left($moz['sales']['netto'],5),],
			'dimension' => $charts->cut_left($lr['sales']['dimensions'],5),
			'color' => ['#df886d', '#0967a8'],
			'height' => 250,
			//'legend' => 'top',
			'area' => false,
			'stacked' => true,
			'showValues' => false,
			'name' => ['LR', 'MOZ'],
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>









<!--
<table class="fancy neutral wide compact backdrop" style="table-layout: fixed;">

	<tr>
		<th style="text-align:right">Produkt / Monat</th>
		<td style="text-align:center; background-color:#ffb9a4"><i>LR+ | <?=$previousMonthWord?></i></td>
		<td style="text-align:center; background-color:#9bccee"><i>MOZplus | <?=$previousMonthWord?></i></td>
		<td style="text-align:center; background-color:#e1e1e1"><b><i>BB gesamt | <?=$previousMonthWord?></i></b></td>

	</tr>

	<tr>
		<th style="text-align:right">Conversions</th>
		<td class="text-center"><?=gnum($lrSales1['orders'])?><?=diff($lrSales1['orders'], $lrSales2['orders'])?></td>
		<td class="text-center"><?=gnum($mozSales1['orders'])?><?=diff($mozSales1['orders'], $mozSales2['orders'])?></td>
		<td class="text-center"><?=gnum($lrSales1['orders'] + $mozSales1['orders'])?>
			<?=diff($lrSales1['orders'] + $mozSales1['orders'], $lrSales2['orders'] + $mozSales2['orders'])?></td>
	</tr>		

	<tr>
		<th style="text-align:right">Proben</th>
		<td class="text-center"><?=gnum($lrSales1['trial1month']+$lrSales1['trial3for3'])?><?=diff($lrSales1['trial1month']+$lrSales1['trial3for3'], $lrSales2['trial1month']+$lrSales2['trial3for3'])?></td>
		<td class="text-center"><?=gnum($mozSales1['trial1month']+$mozSales1['trial3for3'])?><?=diff($mozSales1['trial1month']+$mozSales1['trial3for3'], $mozSales2['trial1month']+$mozSales2['trial3for3'])?></td>
		<td class="text-center"><?=gnum($lrSales1['trial1month']+$lrSales1['trial3for3'] + $mozSales1['trial1month']+$mozSales1['trial3for3'])?>
			<?=diff($lrSales1['trial1month']+$lrSales1['trial3for3']+$mozSales1['trial1month']+$mozSales1['trial3for3'],
			 $lrSales2['trial1month']+$lrSales2['trial3for3']+$mozSales2['trial1month']+$mozSales2['trial3for3'])?></td>


	</tr>

	<tr>
		<th style="text-align:right">Jahresabos</th>
		<td class="text-center"><?=gnum($lrSales1['yearly'])?><?=diff($lrSales1['yearly'], $lrSales2['yearly'])?></td>
		<td class="text-center"><?=gnum($mozSales1['yearly'])?><?=diff($mozSales1['yearly'], $mozSales2['yearly'])?></td>
		<td class="text-center"><?=gnum($lrSales1['yearly'] + $mozSales1['yearly'])?>
			<?=diff($lrSales1['yearly'] + $mozSales1['yearly'], $lrSales2['yearly'] + $mozSales2['yearly'])?></td>
	</tr>

	<tr>
		<th style="text-align:right">Vollabos</th>
		<td class="text-center"><?=gnum($lrSales1['paying'])?><?=diff($lrSales1['paying'], $lrSales2['paying'])?></td>
		<td class="text-center"><?=gnum($mozSales1['paying'])?><?=diff($mozSales1['paying'], $mozSales2['paying'])?></td>
		<td class="text-center"><?=gnum($lrSales1['paying'] + $mozSales1['paying'])?>
			<?=diff($lrSales1['paying'] + $mozSales1['paying'], $lrSales2['paying'] + $mozSales2['paying'])?></td>
	</tr>

	<tr>
		<th style="text-align:right">Aktive Kunden</th>
		<td class="text-center"><?=gnum($lrSales1['active'])?><?=diff($lrSales1['active'], $lrSales2['active'])?></td>
		<td class="text-center"><?=gnum($mozSales1['active'])?><?=diff($mozSales1['active'], $mozSales2['active'])?></td>
		<td class="text-center"><?=gnum($lrSales1['active'] + $mozSales1['active'])?>
			<?=diff($lrSales1['active'] + $mozSales1['active'], $lrSales2['active'] + $mozSales2['active'])?></td>
	</tr>

</table>
-->

</div>



<hr>


<!--<h1 class="text-center">Kundenentwicklung</h1>-->

<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr; margin-top:0em;">

	<figure>
		<h3 class="text-center">
			Aktive Kunden <small>(LR: <span class="lr"><?=gnum($lrSales['active'])?></span> | MOZ: <span class="moz"><?=gnum($mozSales['active'])?></span>)</small>
		</h3>
		<?=$charts->create([
			'metric' => [$charts->cut_left($lr['sales']['active'],5),
			 			 $charts->cut_left($moz['sales']['active'],5),],
			'dimension' => $charts->cut_left($lr['sales']['dimensions'],5),
			'color' => ['#df886d', '#0967a8'],
			'height' => 250,
			//'legend' => 'top',
			'area' => false,
			'stacked' => true,
			'showValues' => false,
			'name' => ['LR', 'MOZ'],
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>


	<figure>
		<h3 class="text-center">
			Subscriberviews <small>(LR: <span class="lr"><?=gnum($kpiData['LR'][$lastMonth]['subscriberviews'])?></span> | MOZ: <span class="moz"><?=gnum($kpiData['MOZ'][$lastMonth]['subscriberviews'])?></span>)</small>
		</h3>		
		<?=$charts->create([
			'metric' => [$charts->cut_left($lr['kpi']['subscriberviews'],1),
			 			 $charts->cut_left($moz['kpi']['subscriberviews'],1),],
			'dimension' => $charts->cut_left($lr['kpi']['dimensions'],1),
			'color' => ['#df886d', '#0967a8'],
			'height' => 250,
			//'legend' => 'top',
			'area' => false,
			'showValues' => false,
			'name' => ['LR', 'MOZ'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

<figure>
		<h3 class="text-center">
			Pageviews <small>(LR: <span class="lr"><?=gnum($kpiData['LR'][$lastMonth]['pageviews'])?></span> | MOZ: <span class="moz"><?=gnum($kpiData['MOZ'][$lastMonth]['pageviews'])?></span>)</small>
		</h3>			
		<?=$charts->create([
			'metric' => [$charts->cut_left($lr['kpi']['pageviews'],4),
			 			 $charts->cut_left($moz['kpi']['pageviews'],4),],
			'dimension' => $charts->cut_left($lr['kpi']['dimensions'],4),
			'color' => ['#df886d', '#0967a8'],
			'height' => 250,
			//'legend' => 'top',
			'area' => false,
			'showValues' => false,
			'name' => ['LR', 'MOZ',],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>
<!--
	<figure>
		<h3 class="text-center">Daily Active Subscribers</h3>
		<?=$charts->create([
			'metric' => [$charts->cut_left($lr['kpi']['dailyActiveSubscribers'],4),
			 			 $charts->cut_left($moz['kpi']['dailyActiveSubscribers'],4),],
			'dimension' => $charts->cut_left($lr['kpi']['dimensions'],4),
			'color' => ['#df886d', '#0967a8'],
			'height' => 250,
			//'legend' => 'top',
			'area' => false,
			'showValues' => false,
			'name' => ['LR', 'MOZ',],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>
-->
</div>


<!--<h1 class="text-center">Inhaltsproduktion</h1>-->




<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr; margin-top:0em;">

	<figure>
		<h3 class="text-center">
			Produzierte Artikel <small>(LR: <span class="lr"><?=gnum($kpiData['LR'][$lastMonth]['articles'])?></span> | MOZ: <span class="moz"><?=gnum($kpiData['MOZ'][$lastMonth]['articles'])?></span>)</small>
		</h3>			
		<?=$charts->create([
			'metric' => [$charts->cut_left($lr['kpi']['articles'],7),
			 			 $charts->cut_left($moz['kpi']['articles'],7),],
			'dimension' => $charts->cut_left($lr['kpi']['dimensions'],7),
			'color' => ['#df886d', '#0967a8'],
			'height' => 250,
			//'legend' => 'top',
			'area' => false,
			'showValues' => false,
			'name' => ['LR', 'MOZ'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure>
		<h3 class="text-center">
			Plusartikel-Quote <small>(LR: <span class="lr"><?=gnum($kpiData['LR'][$lastMonth]['quotePlus'])?>%</span> | MOZ: <span class="moz"><?=gnum($kpiData['MOZ'][$lastMonth]['quotePlus'])?>%</span>)</small>
		</h3>			
		<?=$charts->create([
			'metric' => [$charts->cut_left($lr['kpi']['quotePlus'],7),
			 			 $charts->cut_left($moz['kpi']['quotePlus'],7),],
			'dimension' => $charts->cut_left($lr['kpi']['dimensions'],7),
			'color' => ['#df886d', '#0967a8'],
			'height' => 250,
			//'legend' => 'top',
			'area' => false,
			'percent' => true,
			'showValues' => false,
			'name' => ['LR', 'MOZ'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure>
		<h3 class="text-center">
			Geister-Quote <small>(LR: <span class="lr"><?=gnum($kpiData['LR'][$lastMonth]['quoteGeister'])?>%</span> | MOZ: <span class="moz"><?=gnum($kpiData['MOZ'][$lastMonth]['quoteGeister'])?>%</span>)</small>
		</h3>	
		<?=$charts->create([
			'metric' => [$charts->cut_left($lr['kpi']['quoteGeister'],7),
			 			 $charts->cut_left($moz['kpi']['quoteGeister'],7),],
			'dimension' => $charts->cut_left($lr['kpi']['dimensions'],7),
			'color' => ['#df886d', '#0967a8'],
			'height' => 250,
			//'legend' => 'top',
			'area' => false,
			'percent' => true,
			'showValues' => false,
			'name' => ['LR', 'MOZ'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>


</div>


<!--<h1 class="text-center">Kündigerdaten</h1>-->
<!--<p class="nt text-center">Die Gesamtkündigungs-Quote wächst ganz natürlich mit zunehmender Zeit. Kongruent dazu sinkt die Menge der aktiv gehaltenden Kunden. <br/>Kündigerquoten nach X Tagen sind eingefrorene Kennzahlen. </p>-->


<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr; margin-top:0em;">

	<figure>
		<h3 class="text-center">
			Kündigerquoten am 1 Tag <small>(LR: <span class="lr"><?=gnum($orderData['LR'][$lastMonth]['quoteChurnSameDay'])?>%</span> | MOZ: <span class="moz"><?=gnum($orderData['MOZ'][$lastMonth]['quoteChurnSameDay'])?>%</span>)</small>
		</h3>		
		<?=$charts->create([
			'metric' => [$lr['order']['quoteChurnSameDay'], $moz['order']['quoteChurnSameDay']],
			'dimension' => $lr['order']['dimensions'],
			'color' => ['#df886d', '#0967a8'],
			'height' => 250,
			//'legend' => 'top',
			'percent' => true,
			'area' => false,
			'name' => ['LR', 'MOZ'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure>
		<h3 class="text-center">
			Kündigerquoten nach 30 Tagen <small>(LR: <span class="lr"><?=gnum($orderData['LR'][$previousMonth]['quoteChurn30'])?>%</span> | MOZ: <span class="moz"><?=gnum($orderData['MOZ'][$previousMonth]['quoteChurn30'])?>%</span>)</small>
		</h3>			
		<?=$charts->create([
			'metric' => [$charts->cut($lr['order']['quoteChurn30'],1),
			 			 $charts->cut($moz['order']['quoteChurn30'],1),],
			'dimension' => $charts->cut($lr['order']['dimensions'],1),
			'color' => ['#df886d', '#0967a8'],
			'height' => 250,
			//'legend' => 'top',
			'percent' => true,
			'area' => false,
			'name' => ['LR', 'MOZ'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>


	<figure>
<?
	$data = array_column($orderData['LR'],'quote', $lastMonth);
	$avgLR = array_sum($data)/count($data);
	$data = array_column($orderData['MOZ'],'quote', $lastMonth);
	$avgMOZ = array_sum($data)/count($data);
?>
		<h3 class="text-center">
			Kündigerquoten gesamt <small>(Ø-LR: <span class="lr"><?=gnum($avgLR)?>%</span> | Ø-MOZ: <span class="moz"><?=gnum($avgMOZ)?>%</span>)</small>
		</h3>		
		<?=$charts->create([
			'metric' => [$lr['order']['quote'], $moz['order']['quote']],
			'dimension' => $lr['order']['dimensions'],
			'color' => ['#df886d', '#0967a8'],
			'height' => 250,
			//'legend' => 'top',
			'percent' => true,
			'area' => false,
			'name' => ['LR', 'MOZ'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

</div>



</main>
