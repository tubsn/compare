<main>

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<p>
Auf dieser Seite sind Bestellungen gelistet, <b>die im eingestellten Zeitraum erfolgt sind</b>, und später zu einer Kündigung geführt haben. Es wird NICHT nach dem Kündigungszeitpunkt selektiert.
</p>



<div class="meta-info-box">

	<ul>
		<li>Gesamtbestellungen: <b class="conversions"><?=$numberOfOrders?></b></li>

		<li>davon Gekündigt: <b class="redish"><?=$numberOfCancelled?></b></li>
		<li>Kündigerquote Heute: <b class="orange"><?=$cancelQuote?>&thinsp;%</b></li>
		<li title="Anzahl: <?=$churn90?>">K-Quote nach 90-Tagen: <b class="orange"><?=round($churn90 / $numberOfOrders * 100,1)?>&thinsp;%</b></li>
		<li title="Anzahl: <?=$churn30?>">K-Quote nach 30-Tagen: <b class="orange"><?=round($churn30 / $numberOfOrders * 100,1)?>&thinsp;%</b></li>
		<li title="Anzahl: <?=$churnSameDay?>">K-Quote am Bestelltag: <b class="orange"><?=round($churnSameDay / $numberOfOrders * 100,1)?>&thinsp;%</b></li>


		<li>⌀-Haltedauer: <b class="blue"><?=number_format($averageRetention,2,',','.')?> Tage</b></li>

	</ul>

</div>



<div class="col-2" style="grid-template-columns: 2fr 1fr; margin-top: 2em; margin-bottom:0em;">

	<figure class="">
		<h3 class="text-center">Durchschnittliche Haltedauer unter Kündigern in Tagen</h3>
		<?=$charts->get('avgRetentionByRessort');?>
	</figure>

	<figure class="">
		<h3 class="text-center">Verteilung nach Kündiger Cluster</h3>
		<?=$charts->create([
			'metric' => $churnSameDay . ',' . $churn30 . ',' . $churn90 . ',' . $churnAfter90 . ',' . ($numberOfOrders-$numberOfCancelled),
			'dimension' => "'am 1 Tag gekündigt','innerhalb 30 Tagen',' innerhalb 90 Tagen', 'nach 90 Tagen', 'aktive Kunden'",
			'color' => '#f77474',
			'height' => 350,
			'name' => 'Kündiger',
			'template' => 'charts/default_donut_chart',
		]);?>
	</figure>

</div>

<figure class="" style="margin-bottom:2em;">
	<h3 class="text-center">Verteilung - Kündigungszeitpunkt nach Haltedauer in Tagen</h3>
	<?=$retentionChart?>
</figure>


<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr;">
	<figure class="">
		<h3 class="text-center">Nutzer die direkt am Bestelltag gekündigt haben: <?=$churnSameDay?></h3>
		<?=$charts->get('cancellationsByRessortFirstDay');?>
	</figure>

	<figure class="">
		<h3 class="text-center">zwischen 0-31 Tagen gekündigt: <?=$churn30?></h3>
		<?=$charts->get('cancellationsByRessortFreeMonth31');?>
	</figure>

	<figure class="">
		<h3 class="text-center">zwischen 0-90 Tagen gekündigt: <?=$churn90?></h3>
		<?=$charts->get('cancellationsByRessortFreeMonth90');?>
	</figure>
</div>


</main>
