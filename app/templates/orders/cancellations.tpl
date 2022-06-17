<main>

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<p>
Auf dieser Seite sind Bestellungen gelistet, <b>die im eingestellten Zeitraum erfolgt sind</b>, und später zu einer Kündigung geführt haben. <br/>Es wird NICHT nach dem Kündigungszeitpunkt selektiert. Einige Diagramme wie die Kündigerquotenentwicklung stellen immer den Gesamtzeitraum dar.
</p>

<div class="meta-info-box">

	<ul>
		<li>Gesamtbestellungen: <b class="conversions"><?=$numberOfOrders?></b></li>

		<li>davon Gekündigt: <b class="redish"><?=$numberOfCancelled?></b></li>
		<li>Kündigerquote Aktuell: <b class="orange"><?=$cancelQuote?>&thinsp;%</b></li>
		<li title="Anzahl: <?=$churn90?>">K-Quote nach 90-Tagen: <b class="orange"><?=round($churn90 / $numberOfOrders * 100,1)?>&thinsp;%</b></li>
		<li title="Anzahl: <?=$churn30?>">K-Quote nach 30-Tagen: <b class="orange"><?=round($churn30 / $numberOfOrders * 100,1)?>&thinsp;%</b></li>
		<li title="Anzahl: <?=$churnSameDay?>">K-Quote am Bestelltag: <b class="orange"><?=round($churnSameDay / $numberOfOrders * 100,1)?>&thinsp;%</b></li>
		<li>⌀-Haltedauer: <b class="blue"><?=number_format($averageRetention,2,',','.')?> Tage</b></li>
	</ul>

</div>


<div class="col-2" style="grid-template-columns: 1fr 1fr; margin-top: 2em; margin-bottom:0em;">


		<figure class="mb">
			<h3 class="text-center">Bestelleingang</h3>
			<?=$charts->create([
				'metric' => $longterm['orders'],
				'dimension' => $longterm['dimensions'],
				'color' => '#314e6f',
				'height' => 280,
				'area' => true,
				'name' => 'Zuwachs an aktiven Kunden',
				'template' => 'charts/default_line_chart',
			]);?>
		</figure>


		<figure class="mb">
			<h3 class="text-center">Plusseiten Bestellquote</h3>
			<p class="nt text-center">Kunden die über die Plusseite gekauft haben</p>
			<?=$charts->create([
				'metric' => $longterm['quotePlusPage'],
				'dimension' => $longterm['dimensions'],
				'color' => '#314e6f',
				'height' => 250,
				'area' => true,
				'percent' => true,
				'name' => 'Anteil von Käufen über die Plusseite',
				'template' => 'charts/default_line_chart',
			]);?>
		</figure>

</div>








<div class="col-2" style="grid-template-columns: 1fr 1.5fr 1fr; margin-top: 2em; margin-bottom:0em;">

	<figure class="mb">
		<h3 class="text-center">Entwicklung Kündigerquote</h3>
		<p class="nt text-center">Quote steigt mit zunehmender Zeit.</p>
		<?=$charts->create([
			'metric' => $longterm['quote'],
			'dimension' => $longterm['dimensions'],
			'color' => '#f77474',
			'height' => 300,
			'area' => true,
			'percent' => true,
			'name' => 'Kündigerquote',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Bestelleingang und Kündiger pro Monat</h3>
		<p class="nt text-center">Abgänge beziehen sich hier auf die Zugänge des Monats.</p>
		<?=$charts->create([
			'metric' => [$longterm['orders'], $longterm['cancelledNegative']],
			'dimension' => $longterm['dimensions'],
			'color' => ['#314e6f', '#f77474'],
			'height' => 300,
			'area' => true,
			'stacked' => true,
			'showValues' => false,
			'xfont' => '13px',
			'name' => ['Zugänge', 'davon gekündigt'],
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>


	<?php if ($reasons_chart): ?>
	<figure class="">
		<h3 class="text-center">Kündigungsgründe</h3>
		<?=$charts->create([
			'metric' => $reasons_chart['metrics'] ,
			'dimension' => $reasons_chart['dimensions'],
			'color' => '#f77474',
			'height' => 350,
			'legend' => 'bottom',
			'name' => 'Kündiger',
			'template' => 'charts/default_donut_chart',
		]);?>
	</figure>
	<?php else: ?>
	<figure style="width:100%; text-align:center">
		<h3 class="text-center">Kündigungsgründe</h3>
		<p class="nt orangebg"> Keine Daten Verfügbar</p>
	</figure>
	<?php endif ?>

</div>


<div class="col-2" style="grid-template-columns: 2fr 1fr;">

	<figure class="mb">
		<h3 class="text-center">Aktivkunden nach Aktivitätsdauer</h3>
		<p class="nt text-center">Kunden, die nach ihrem Probezeitraum von 31 oder 91 Tagen noch im Abo waren. (NP)</p>
		<?=$charts->create([
			'metric' => [$charts->cut($longterm['active'],1),
			 			 $charts->cut($longterm['activeAfter90'],1),
			  	 		 $charts->cut($longterm['activeAfter30'],1),
			  	 		 $charts->cut($longterm['orders'],1)],
			'dimension' => $charts->cut($longterm['dimensions'],1),
			'color' => ['#a2b495', '#aeb9c5', '#8a9aad' ,'#4f6884'],
			'height' => 300,
			'stacked' => false,
			'area' => false,
			'showValues' => false,
			'legend' => 'top',
			'name' => ['heute noch aktiv', 'mindestens 91 Tage aktiv', 'mindestens 31 Tage aktiv', 'Bestelleingang'],
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Eintritt ins Bezahlabo</h3>
		<p class="nt text-center">Anteil an aktiven Kunden nach Probezeitraum.</p>
		<?=$charts->create([
			'metric' => [$charts->cut($longterm['quoteActiveAfter30'],1),
			 			 $charts->cut($longterm['quoteActiveAfter90'],1)],
			'dimension' => $charts->cut($longterm['dimensions'],1),
			'color' => ['#7e91aa', '#314e6f'],
			'height' => 300,
			'percent' => true,
			'area' => true,
			'legend' => 'top',
			'name' => ['mindestens 31 Tage aktiv', 'mindestens 91 Tage aktiv'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

</div>

<div class="col-2" style="grid-template-columns: 1fr 4fr;">


	<figure class="mb">
		<h3 class="text-center">Langzeit Leser</h3>
		<p class="nt text-center"><?=$activeAfterOneYear ?? 0?> Kunden waren bzw. sind länger als ein Jahr aktiv. <b>(Datenbasis seit April 21)</b></p>
		<?=$charts->create([
			'metric' => $yearlyActiveTimespan['orders'],
			'dimension' => "'Im ersten Jahr', 'im zweiten Jahr'",
			'color' => '#4e6783',
			'height' => 300,
			'showValues' => false,
			'name' => 'Aktive Kunden',
			'prefix' => 'Jahr ',
			'name' => ['Im ersten Jahr', 'im zweiten Jahr'],
			'template' => 'charts/default_donut_chart',
		]);?>
	</figure>


	<figure class="mb">
		<h3 class="text-center">Verteilung aktiver Kunden nach Monaten</h3>
		<p class="nt text-center">In der Grafik erkennt man wie sich unsere Treue Leserschaft aufteilt. <b>(Datenbasis seit April 21)</b></p>
		<?=$charts->create([
			'metric' => $monthlyActiveTimespan['orders'],
			'dimension' => $monthlyActiveTimespan['dimensions'],
			'color' => '#4e6783',
			'height' => 300,
			'showValues' => false,
			'name' => 'Aktive Kunden',
			'prefix' => 'Monat ',
			'name' => 'Kunden in diesem Monat',
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>


</div>

<figure class="" style="margin-bottom:1em;">
	<h3 class="text-center">Verteilung - Kündigungszeitpunkt nach Haltedauer in Tagen</h3>
	<p class="nt text-center">Hier lassen sich kritische Zeiträume ablesen, zu denen Nutzer gehäuft ihr Abo Kündigen (Abhängig vom eingestellten Zeitraum).</p>
	<?=$charts->get('cancellations_by_retention_days', 400);?>
</figure>


<div class="col-2" style="grid-template-columns: 1fr 1fr;">

	<figure class="mb">
		<h3 class="text-center">Entwicklung Kündigerquoten nach Zeitspanne</h3>
		<p class="nt text-center">Rückwirkende Kündigerquoten vergangener Monate nach einem, 30 oder 90 Tagen.</p>
		<?=$charts->create([
			'metric' => [$longterm['quoteChurn90'], $longterm['quoteChurn30'], $longterm['quoteChurnSameDay'], ],
			'dimension' => $longterm['dimensions'],
			'color' => '#f77474',
			'height' => 600,
			'area' => false,
			'percent' => true,
			'legend' => 'top',
			'name' => ['Kündigerquote nach 90 Tagen', 'Kündigerquote nach 30 Tagen', 'Quote-Bestelltag Kündiger'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<div>


		<figure class="mb">
			<h3 class="text-center">Monatlicher Zuwachs an Netto-Neukunden</h3>
			<p class="nt text-center">Tatsächliches Abowachstum (schrumpft kontinuierlich durch neue Kündigungen).</p>
			<?=$charts->create([
				'metric' => $longterm['active'],
				'dimension' => $longterm['dimensions'],
				'color' => '#314e6f',
				'height' => 250,
				'area' => true,
				'name' => 'Zuwachs an aktiven Kunden',
				'template' => 'charts/default_line_chart',
			]);?>
		</figure>


		<figure class="">
			<h3 class="text-center">Durchschnittliche Haltedauer unter Kündigern in Tagen</h3>
			<p class="nt text-center">Wenn Nutzer kündigen wo halten Abos am längsten?</p>
			<?=$charts->get('avg_retention_by');?>
		</figure>


	</div>

</div>



<h3 class="text-center">Leser die am Bestelltag kündigten nach Cluster</h3>
<p class="nt text-center">Hier lässt sich ablesen, in welchem Ressort Nutzer häufig, direkt am ersten Tag Kündigen (Anteil).</p>

<div class="col-2" style="grid-template-columns: 1fr 1fr ; align-items: center; justify-items: center; margin-top: 2em; margin-bottom:0em;">

	<figure style="width:100%;">
		<h3 class="text-center">nach Ressort</h3>
		<?=$charts->get('first_day_churns_by' , 'article_ressort');?>
	</figure>

	<figure style="width:100%;">
		<h3 class="text-center">nach #-Tag</h3>
		<?=$charts->get('first_day_churns_by' , 'article_tag');?>
	</figure>

	<figure style="width:100%;">
		<h3 class="text-center">nach Themencluster</h3>
		<?=$charts->get('first_day_churns_by' , 'article_type');?>
	</figure>

	<figure style="width:100%;">
		<h3 class="text-center">nach Audience</h3>
		<?=$charts->get('first_day_churns_by' , 'article_audience');?>
	</figure>


</div>

</main>
