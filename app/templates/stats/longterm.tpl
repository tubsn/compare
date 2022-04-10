<main>


<?php if (PORTAL == 'MOZ' || PORTAL == 'SWP'): ?>
	<div class="box mbig" style="margin-bottom:3em; background-color:#953737; color:white;"><b>Achtung Aboentwicklung für SWP und MOZ unvollständig!</b></div>
<?php endif ?>


<style>
.calendar-timeframe, .calendar-datepicker {display:none !important;} 
</style>

<?php include tpl('navigation/date-picker');?>

<h1 class="text-center"><?=PORTAL?>+ Aboentwicklung </h1>


<figure class="mb">
	<h3 class="text-center">aktive Abonnenten</h3>
	<?=$charts->create([
		'metric' => [$sales['paying'], $sales['trial1month'], $sales['trial3for3'], $sales['reduced'], $sales['yearly']],
		'dimension' => $sales['dimensions'],
		'color' => ['#1572A1', '#9AD0EC', '#9AD0EC', '#EFDAD7', '#D885A3'],
		'height' => 450,
		'legend' => 'top',

		'stacked' => true,
		'showValues' => false,
		'name' => ['Vollabo', 'Probe 1Monat', 'Probe 3Für3', '99cent (PrintKombi)', 'Jahresabos'],
		'template' => 'charts/default_bar_chart',
	]);?>
</figure>


<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr; margin-top:2em;">


	<figure class="mb">
		<h3 class="text-center">aktive Vollabos + Proben + Jahresabos</h3>
		<?=$charts->create([
			'metric' => $sales['active'],
			'dimension' => $sales['dimensions'],
			'color' => '#1572A1',
			'height' => 350,
			'legend' => 'top',
			'xfont' => '13px',
			'area' => true,
			'showValues' => false,
			'name' => 'Aktive Abos',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">aktive Proben</h3>
		<?=$charts->create([
			'metric' => [$sales['trial1month'], $sales['trial3for3']],
			'dimension' => $sales['dimensions'],
			'color' => '#9AD0EC',
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'stacked' => true,
			'xfont' => '13px',
			'showValues' => false,
			'name' => ['1Monat Proben', '3für3 Proben'],
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>


	<figure class="mb">
		<h3 class="text-center">Bestelleingang</h3>
		<?=$charts->create([
			'metric' => $orders['orders'],
			'dimension' => $orders['dimensions'],
			'color' => '#e29881',
			'height' => 350,
			'legend' => 'top',
			'area' => true,
			'showValues' => false,
			'xfont' => '13px',
			'name' => 'Bestellungen',
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>


</div>


<?php $salesData = array_reverse($salesData);?>

<figure style="margin-bottom:4em; overflow-y: hidden;">

	<table class="fancy neutral wide" style="max-width:100%; overflow-y: scroll;">

		<tr>
			<th style="text-align:right">Monat</th>
			<?php foreach ($salesData as $month => $set): ?>
			<td style="text-align:right"><i><?=formatDate($month, 'M\'y')?></i>
			<?php endforeach; ?>
		</tr>

		<tr>
			<th style="text-align:right">Vollabos</th>
			<?php foreach ($salesData as $month => $set): ?>
			<td class="text-right"><?=gnum($set['paying'])?></td>
			<?php endforeach; ?>
		</tr>

		<tr>
			<th style="text-align:right">Proben</th>
			<?php foreach ($salesData as $month => $set): ?>
			<td class="text-right"><?=gnum($set['trial1month'] + $set['trial3for3'])?></td>
			<?php endforeach; ?>
		</tr>

		<tr>
			<th style="text-align:right">Jahresabos</th>
			<?php foreach ($salesData as $month => $set): ?>
			<td class="text-right"><?=$set['yearly'] ?? '-'?></td>
			<?php endforeach; ?>
		</tr>

		<tr>
			<th style="text-align:right">Aktive Kunden</th>
			<?php foreach ($salesData as $month => $set): ?>
			<td class="text-right"><b><?=gnum($set['active'])?></b></td>
			<?php endforeach; ?>
		</tr>

		<?php $plenigoOrders = array_slice($orderHistory,3);?>

		<tr>
			<th style="text-align:right">Bestellungen</th>
			<?php foreach ($salesData as $month => $set): ?>			
			<td class="text-right"><?=$plenigoOrders[$month]['orders'] ?? '-'?></td>
			<?php endforeach; ?>
		</tr>

		<tr>
			<th style="text-align:right">Kündigungseingang</th>
			<?php foreach ($salesData as $month => $set): ?>
			<td class="text-right"><?=gnum($set['cancelled'])?></td>
			<?php endforeach; ?>
		</tr>

	</table>

</figure>


<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr 1fr 1fr">


	<figure class="mb">
		<h3 class="text-center">Kündigereingang</h3>
		<?=$charts->create([
			'metric' => $sales['cancelled'],
			'dimension' => $sales['dimensions'],
			'color' => '#DD4A48',
			'height' => 250,
			'legend' => 'top',
			'area' => false,
			'xfont' => '13px',
			'showValues' => false,
			'name' => 'Kündiger',
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Kündigerquote Gesamt</h3>
		<?=$charts->create([
			'metric' => $orders['quote'],
			'dimension' => $orders['dimensions'],
			'color' => '#DD4A48',
			'height' => 250,
			'legend' => 'top',
			'percent' => true,
			'area' => true,
			'name' => 'Quote',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Kündigerquote am 1 Tag</h3>
		<?=$charts->create([
			'metric' => $orders['quoteChurnSameDay'],
			'dimension' => $orders['dimensions'],
			'color' => '#DD4A48',
			'height' => 250,
			'legend' => 'top',
			'percent' => true,
			'area' => true,
			'name' => 'Quote',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>


	<figure class="mb">
		<h3 class="text-center">Kündigerquote nach 30 Tagen</h3>
		<?=$charts->create([
			'metric' => $charts->cut($orders['quoteChurn30'],1),
			'dimension' => $charts->cut($orders['dimensions'],1),
			'color' => '#DD4A48',
			'height' => 250,
			'legend' => 'top',
			'percent' => true,
			'area' => true,
			'name' => 'Quote',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Kündigerquote nach 90 Tagen</h3>
		<?=$charts->create([
			'metric' => $charts->cut($orders['quoteChurn90'],2),
			'dimension' => $charts->cut($orders['dimensions'],2),
			'color' => '#DD4A48',
			'height' => 250,
			'legend' => 'top',
			'percent' => true,
			'area' => true,
			'name' => 'Quote',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

</div>

<p class="text-center" style="margin-top:-3em; margin-bottom:4em;">Kündigungseingang bezieht sich auf das <b>Eingangsdatum einer Kündigung</b> (nicht den Tag der Kündigungswirksamkeit). Die Gesamtkündigungs-Quote wächst natürlich mit zunehmender Zeit. <br/>Die Kündigerquoten nach X Tagen sind <b>eingefrorene Kennzahlen</b>, abgelesen jeweils nach einem, 30 oder 90 Tagen des jeweiligen Monats.</p>



<h1 class="text-center"><?=PORTAL_URL_SHORT?></h1>


<div class="col-2" style="grid-template-columns: 3fr 1fr ">

	<figure class="mb">
		<?=$charts->create([
			'metric' => [$kpis['pageviews'], $kpis['sessions'], $kpis['subscribers']],
			'dimension' => $kpis['dimensions'],
			'color' => ['#7698be', '#a5bcd5', '#4e6783'],
			'height' => 350,
			'legend' => 'top',
			'area' => true,
			'showValues' => false,

			'name' => ['Pageviews', 'Visits', 'Subscriberviews'],
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>


	<figure class="mb">
		<h3 class="text-right" style="margin-bottom:0">Subscriberviews</h3>
		<?=$charts->create([
			'metric' => $charts->cut_left($kpis['subscribers'],0),
			'dimension' => $charts->cut_left($kpis['dimensions'],0),
			'color' => '#4e6783',
			'height' => 350,
			'legend' => 'top',
			'area' => true,
			'showValues' => false,
			'name' => 'Subscriberviews',
			//'tickamount' => '10',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>
</div>


<figure>


	<h3 class="text-center">Pageviews</h3>

	<?php $kpiHistory = array_reverse($kpiHistory);?>

	<table class="fancy neutral wide" style="margin-bottom:3em">

		<tr>
			<th style="text-align:right">Monat</th>
			<?php foreach ($kpiHistory as $month => $set): ?>
			<td style="text-align:right"><i><?=formatDate($month, 'M\'y')?></i><br />
		 	<?=$difference($set['pageviews'], $set['pageviews_past'])?></td>
			<?php endforeach; ?>
		</tr>

		<tr>
			<th style="text-align:right">Aktuell</th>
			<?php foreach ($kpiHistory as $month => $set): ?>
			<td class="text-right"><b><?=gnum($set['pageviews'])?></b></td>
			<?php endforeach; ?>
		</tr>

		<tr>
			<th style="text-align:right">Vorjahr</th>
			<?php foreach ($kpiHistory as $month => $set): ?>
			<td class="text-right"><?=gnum($set['pageviews_past'])?></td>
			<?php endforeach; ?>
		</tr>

	</table>

	<h3 class="text-center">Visits</h3>

	<table class="fancy neutral wide" style="margin-bottom:3em">

		<tr>
			<th style="text-align:right">Monat</th>
			<?php foreach ($kpiHistory as $month => $set): ?>
			<td style="text-align:right"><i><?=formatDate($month, 'M\'y')?></i><br />
		 	<?=$difference($set['sessions'], $set['sessions_past'])?></td>
			<?php endforeach; ?>
		</tr>

		<tr>
			<th style="text-align:right">Aktuell</th>
			<?php foreach ($kpiHistory as $month => $set): ?>
			<td class="text-right"><b><?=gnum($set['sessions'])?></b></td>
			<?php endforeach; ?>
		</tr>

		<tr>
			<th style="text-align:right">Vorjahr</th>
			<?php foreach ($kpiHistory as $month => $set): ?>
			<td class="text-right"><?=gnum($set['sessions_past'])?></td>
			<?php endforeach; ?>
		</tr>

	</table>


	<h3 class="text-center">Subscriberviews</h3>

	<table class="fancy neutral wide" style="margin-bottom:2em">

		<tr>
			<th style="text-align:right">Monat</th>
			<?php foreach ($kpiHistory as $month => $set): ?>
			<td style="text-align:right"><i><?=formatDate($month, 'M\'y')?></i><br />
		 	<?=$difference($set['subscribers'], $set['subscribers_past'])?></td>
			<?php endforeach; ?>
		</tr>

		<tr>
			<th style="text-align:right">Aktuell</th>
			<?php foreach ($kpiHistory as $month => $set): ?>
			<td class="text-right"><b><?=gnum($set['subscribers'])?></b></td>
			<?php endforeach; ?>
		</tr>

		<tr>
			<th style="text-align:right">Vorjahr</th>
			<?php foreach ($kpiHistory as $month => $set): ?>
			<td class="text-right"><?=gnum($set['subscribers_past'])?></td>
			<?php endforeach; ?>
		</tr>

	</table>

</figure>


<details style="margin-bottom:3em;">
<summary>weitere Details ausklappen</summary>
<figure>

	<table class="fancy wide js-sortable">
		<thead>
			<th>Datum</th>
			<th style="text-align:right">Pageviews</th>
			<th style="text-align:right">Pageviews Last Year</th>
			<th style="text-align:right">Subscribers</th>
			<th style="text-align:right">Subs Last Year</th>
			<th style="text-align:right">Mediatime</th>
			<th style="text-align:right">Mediatime</th>
		</thead>
		<tbody>

	<?php foreach ($kpiHistory as $month => $set): ?>

		<tr>
			<td><?=$month?></td>
			<td class="text-right"><?=gnum($set['pageviews'])?> &thinsp; <?=$difference($set['pageviews'], $set['pageviews_past'])?></td>
			<td class="text-right"><?=gnum($set['pageviews_past'])?></td>
			<td class="text-right"><?=gnum($set['subscribers'])?> &thinsp; <?=$difference($set['subscribers'], $set['subscribers_past'])?></td>
			<td class="text-right"><?=gnum($set['subscribers_past'])?></td>
			<td class="text-right"><?=$set['avgmediatime']?>&thinsp;s</td>
			<td class="text-right"><?=$set['avgmediatime_past']?>&thinsp;s</td>
		</tr>


	<?php endforeach; ?>
		</tbody>
	</table>

</figure>
</details>





<h1 class="text-center">Weitere Kennzahlen</h1>


<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr 1fr; margin-top:2em;">

	<figure class="mb">
		<h3 class="text-center">Wertschöpfende Artikel</h3>
		<?=$charts->create([
			'metric' => [$charts->cut_left($kpis['quoteGeister'],5), $charts->cut_left($kpis['quoteSpielmacher'],5)],
			'dimension' => $charts->cut_left($kpis['dimensions'],5),
			'color' => ['#bd6161', '#5fb486'],
			'height' => 350,
			'legend' => 'top',
			'percent' => true,
			'area' => true,
			'showValues' => false,
			'xfont' => '13px',
			'name' => ['Geisterquote', 'Spielmacherquote'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>


	<figure class="mb">
		<h3 class="text-center">Plusartikel-Quote</h3>
		<?=$charts->create([
			'metric' => $charts->cut_left($kpis['quotePlus'],5),
			'dimension' => $charts->cut_left($kpis['dimensions'],5),
			'color' => '#6a6a6a',
			'height' => 350,
			'legend' => 'top',
			'area' => true,
			'percent' => true,
			'showValues' => false,
			'name' => 'Quote',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>


	<figure class="mb">
		<h3 class="text-center">Produzierte Artikel</h3>
		<?=$charts->create([
			'metric' => $charts->cut_left($kpis['articles'],5),
			'dimension' => $charts->cut_left($kpis['dimensions'],5),
			'color' => '#6a6a6a',
			'height' => 350,
			'legend' => 'top',
			'area' => true,
			'showValues' => false,
			'name' => 'Anzahl',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>


	<figure class="mb">
		<h3 class="text-center">⌀-Mediatime Entwicklung in Sekunden (GA)</h3>
		<?=$charts->create([
			'metric' => $kpis['avgmediatime'],
			'dimension' => $kpis['dimensions'],
			'color' => '#82b292',
			'height' => 350,
			'legend' => 'top',
			'area' => true,
			'name' => 'Mediatime',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>




</div>


<div class="col-2" style="grid-template-columns: 2fr 1fr;">

	<figure class="mb">
		<h3 class="text-center">App Nutzer nach Gerät</h3>
		<?=$charts->create([
			'metric' => [$sales['app_user_android'], $sales['app_user_ios']],
			'dimension' => $sales['dimensions'],
			'color' => ['#37bab2','#ab2a5f'],
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'stacked' => true,
			'xfont' => '13px',
			'showValues' => false,
			'name' => ['Android User', 'iOS User'],
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">App User Entwicklung</h3>
		<?=$charts->create([
			'metric' => $sales['app_user'],
			'dimension' => $sales['dimensions'],
			'color' => '#d0bd85',
			'height' => 350,
			'legend' => 'top',
			'area' => true,
			'name' => 'App Nutzer',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

</div>

<p class="text-center" style="margin-top:-1em; margin-bottom:4em;">App Nutzer sind <b>aktive User in dem Zeitraum</b> (nicht die Anzahl der App installationen).</p>

<figure class="mb">
	<h3 class="text-center">Kaufreiz zu Bestellung</h3>
	<?=$charts->create([
		'metric' => [$charts->cut_left($kpis['buyintents'],5), $orders['orders']],
		'dimension' => $orders['dimensions'],
		'color' => ['#e29881','#82b292'],
		'height' => 450,
		'legend' => 'top',
		'area' => true,
		'name' => ['Klicks auf Bestellknopf','Bestellungen'],
		'template' => 'charts/default_line_chart',
	]);?>
</figure>


</main>
