<main>

<?php include tpl('navigation/date-picker');?>

<h1><?=$page['title'] ?? 'Statistiken'?></h1>


<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr">

	<figure class="mb">
		<h3 class="text-center">Bestellungen</h3>
		<?=$charts->create([
			'metric' => $cancellations['orders'],
			'dimension' => $cancellations['dimensions'],
			'color' => '#6ea681',
			'height' => 350,
			'name' => 'Bestellungen',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>


	<figure class="mb">
		<h3 class="text-center">Aktive Kunden des Monats (stand jetzt)</h3>
		<?=$charts->create([
			'metric' => $cancellations['active'],
			'dimension' => $cancellations['dimensions'],
			'color' => '#6088b4',
			'height' => 350,
			'name' => 'Bestellungen',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Kündigerquote (Gesamt)</h3>
		<?=$charts->create([
			'metric' => $cancellations['quote'],
			'dimension' => $cancellations['dimensions'],
			'color' => '#f77474',
			'height' => 350,
			'name' => 'Tag 1 Kündiger',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

</div>


<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr;">

	<figure class="mb">
		<h3 class="text-center">Entwicklung Kündigerquote (am Bestelltag)</h3>
		<?=$charts->create([
			'metric' => $cancellations['quoteChurnSameDay'],
			'dimension' => $cancellations['dimensions'],
			'color' => '#f77474',
			'height' => 350,
			'name' => 'Tag 1 Kündiger',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Kündigerquote (nach 30 Tagen)</h3>
		<?=$charts->create([
			'metric' => $cancellations['quoteChurn30'],
			'dimension' => $cancellations['dimensions'],
			'color' => '#f77474',
			'height' => 350,
			'name' => 'Kündiger nach 30 Tagen',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Kündigerquote (nach 90 Tagen)</h3>
		<?=$charts->create([
			'metric' => $cancellations['quoteChurn90'],
			'dimension' => $cancellations['dimensions'],
			'color' => '#f77474',
			'height' => 350,
			'name' => 'Bestellungen',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

</div>



</main>
