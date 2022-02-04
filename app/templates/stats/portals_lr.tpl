<main>

<h1 class="text-center">Portalentwicklung LR</h1>



	<figure class="mb">
		<h3 class="text-center">aktive Abonnenten Entwicklung</h3>
		<?=$charts->create([
			'metric' => [$charts->cut_left($lr['sales']['paying'],0),
			 			 $charts->cut_left($lr['sales']['trial1month'],0),
			 			 $charts->cut_left($lr['sales']['trial3for3'],0),
			 			 $charts->cut_left($lr['sales']['reduced'],0),
			  	 		 $charts->cut_left($lr['sales']['yearly'],0)],
			'dimension' => $charts->cut_left($lr['sales']['dimensions'],0),
			'color' => ['#1572A1', '#9AD0EC', '#9AD0EC', '#EFDAD7', '#D885A3'],
			'height' => 450,
			'legend' => 'top',
			'xfont' => '13px',
			'stacked' => true,
			'showValues' => false,
			'name' => ['Vollabo', 'Probe 1Monat', 'Probe 3Für3', '99cent (PrintKombi)', 'Jahresabos'],
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>


<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr; margin-top:2em;">




	<figure class="mb">
		<h3 class="text-center">Vollabos + Proben + Jahresabos</h3>
		<?=$charts->create([
			'metric' => $charts->cut_left($lr['sales']['active'],0),
			'dimension' => $charts->cut_left($lr['sales']['dimensions'],0),
			'color' => '#1572A1',
			'height' => 350,
			'legend' => 'top',
			'xfont' => '13px',
			'area' => false,
			'showValues' => false,
			'name' => 'LR',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">3für3 Proben</h3>
		<?=$charts->create([
			'metric' => $charts->cut_left($lr['sales']['trial3for3'],12),
			'dimension' => $charts->cut_left($lr['sales']['dimensions'],12),
			'color' => '#9AD0EC',
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'xfont' => '13px',
			'showValues' => false,
			'name' => 'LR',
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Kündigereingang</h3>
		<?=$charts->create([
			'metric' => $charts->cut_left($lr['sales']['cancelled'],1),
			'dimension' => $charts->cut_left($lr['sales']['dimensions'],1),
			'color' => '#DD4A48',
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'xfont' => '13px',			
			'showValues' => false,
			'name' => 'LR',
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>

</div>


<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr; margin-top:2em;">

	<figure class="mb">
		<h3 class="text-center">Subscriber Entwicklung (Linkpulse)</h3>
		<?=$charts->create([
			'metric' => $charts->cut_left($lr['kpi']['subscribers'],1),
			'dimension' => $charts->cut_left($lr['kpi']['dimensions'],1),
			'color' => '#05a5c8',
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'showValues' => false,
			'name' => 'LR',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Plusartikel-Quote</h3>
		<?=$charts->create([
			'metric' => $charts->cut_left($lr['kpi']['quotePlus'],7),
			'dimension' => $charts->cut_left($lr['kpi']['dimensions'],7),
			'color' => '#05a5c8',
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'percent' => true,
			'showValues' => false,
			'name' => 'LR',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">⌀-Mediatime Entwicklung in Sekunden (GA)</h3>
		<?=$charts->create([
			'metric' => $lr['kpi']['avgmediatime'],
			'dimension' => $lr['kpi']['dimensions'],
			'color' => '#05a5c8',
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'name' => 'LR',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>


</div>




<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr; margin-top:2em;">

	<figure class="mb">
		<h3 class="text-center">Eingehende Bestellungen</h3>
		<?=$charts->create([
			'metric' => $lr['order']['orders'],
			'dimension' => $lr['order']['dimensions'],
			'color' => '#05a5c8',
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'name' => 'LR',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>


	<figure class="mb">
		<h3 class="text-center">Aktuell ungekündigte (Aktive-) Kunden</h3>
		<?=$charts->create([
			'metric' => $lr['order']['active'],
			'dimension' => $lr['order']['dimensions'],
			'color' => '#05a5c8',
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'name' => 'LR',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Kündigerquoten Gesamt</h3>
		<?=$charts->create([
			'metric' => $lr['order']['quote'],
			'dimension' => $lr['order']['dimensions'],
			'color' => '#05a5c8',
			'height' => 350,
			'legend' => 'top',
			'percent' => true,
			'area' => false,
			'name' => 'LR',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

</div>

<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr">

	<figure class="mb">
		<h3 class="text-center">Kündigerquoten am 1 Tag</h3>
		<?=$charts->create([
			'metric' => $lr['order']['quoteChurnSameDay'],
			'dimension' => $lr['order']['dimensions'],
			'color' => '#05a5c8',
			'height' => 350,
			'legend' => 'top',
			'percent' => true,
			'area' => false,
			'name' => 'LR',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>


	<figure class="mb">
		<h3 class="text-center">Kündigerquoten nach 30 Tagen</h3>
		<?=$charts->create([
			'metric' => $charts->cut($lr['order']['quoteChurn30'],1),
			'dimension' => $charts->cut($lr['order']['dimensions'],1),
			'color' => '#05a5c8',
			'height' => 350,
			'legend' => 'top',
			'percent' => true,
			'area' => false,
			'name' => 'LR',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Kündigerquoten nach 90 Tagen</h3>
		<?=$charts->create([
			'metric' => $charts->cut($lr['order']['quoteChurn90'],2),
			'dimension' => $charts->cut($lr['order']['dimensions'],2),
			'color' => '#05a5c8',
			'height' => 350,
			'legend' => 'top',
			'percent' => true,
			'area' => false,
			'name' => 'LR',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

</div>



</main>
