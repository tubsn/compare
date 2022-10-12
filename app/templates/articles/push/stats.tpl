<main>

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<?php if ($info): ?>
<p><?=$info?></p>
<?php endif; ?>

<hr>

<div class="col-2">
	<figure class="mb">
		<h3 class="text-center" style="margin:0">Klickrate (nur bei Mindestausspielungen)</h3>
		<?=$charts->create([
			'metric' => $hours['clickrate'],
			'dimension' => $hours['dimensions'],
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
		<h3 class="text-center" style="margin:0">Pushmeldungen erstellt</h3>
		<?=$charts->create([
			'metric' => $hours['created'],
			'dimension' => $hours['dimensions'],
			'color' => '#E7AB79',
			'height' => 450,
			'stacked' => false,
			'showValues' => false,
			'name' => 'Erstellt',
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>

</div>


<div class="col-2">
	<figure class="mb">
		<h3 class="text-center" style="margin:0">von Nutzern geklickt</h3>
		<?=$charts->create([
			'metric' => $hours['clicks'],
			'dimension' => $hours['dimensions'],
			'color' => '#4C3A51',
			'height' => 450,
			'stacked' => false,
			'showValues' => false,
			'name' => 'Klicks',
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center" style="margin:0">an Nutzer ausgespielt</h3>
		<?=$charts->create([
			'metric' => $hours['delivered'],
			'dimension' => $hours['dimensions'],
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
