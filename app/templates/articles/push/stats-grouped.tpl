<main>

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<?php if ($info): ?>
<p><?=$info?></p>
<?php endif; ?>

<hr>


	<figure class="mb">
		<h3 class="text-center" style="margin:0">Klickraten nach Uhrzeit</h3>
		<?=$charts->create([
			'metric' => [
				$statsGrouped['Amateursportler'],
				$statsGrouped['Crime-Fans'],
				$statsGrouped['Familien'],
				$statsGrouped['FCE-Fans'],
				$statsGrouped['Foodies'],
				$statsGrouped['Gesundheitsbewusste'],
				$statsGrouped['Pendler'],
				$statsGrouped['Rettungsengel'],
				$statsGrouped['Unternehmer'],
				$statsGrouped['leer'],
			],
			'dimension' => $statsGrouped['dimensions'],
			'color' => '#4C3A51',
			'height' => 450,
			'area' => false,
			'stacked' => false,
			'legend' => 'top',
			'showValues' => false,
			'name' => [
				'Amateursportler',
				'Crime-Fans',
				'Familien',
				'FCE-Fans',
				'Foodies',
				'Gesundheitsbewusste',
				'Pendler',
				'Rettungsengel',
				'Unternehmer',
				'leer',
			],
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>






<div class="col-2">
	<figure class="mb">
		<h3 class="text-center" style="margin:0">Klickrate</h3>
		<?=$charts->create([
			'metric' => $stats['clickrate'],
			'dimension' => $stats['dimensions'],
			'color' => '#B25068',
			'height' => 450,
			'stacked' => false,
			'showValues' => false,
			'percent' => true,
			'name' => 'Klickrate',
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center" style="margin:0">Opt Outs</h3>
		<?=$charts->create([
			'metric' => $stats['avg_opt_outs'],
			'dimension' => $stats['dimensions'],
			'color' => '#E7AB79',
			'height' => 450,
			'stacked' => false,
			'showValues' => false,
			'name' => 'Opt-Outs',
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>

</div>


<div class="col-2">
	<figure class="mb">
		<h3 class="text-center" style="margin:0">Anzahl Pushmeldungen</h3>
		<?=$charts->create([
			'metric' => $stats['notifications'],
			'dimension' => $stats['dimensions'],
			'color' => '#4C3A51',
			'height' => 450,
			'stacked' => false,
			'showValues' => false,
			'name' => 'Pushmeldungen',
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center" style="margin:0">an Nutzer ausgespielt</h3>
		<?=$charts->create([
			'metric' => $stats['avg_delivered'],
			'dimension' => $stats['dimensions'],
			'color' => '#774360',
			'height' => 450,
			'stacked' => false,
			'showValues' => false,
			'name' => 'Geliefert',
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>

</div>



</main>
