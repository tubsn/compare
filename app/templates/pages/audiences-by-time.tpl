<main>
<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<?php if ($info): ?>
<p><?=$info?></p>
<?php endif; ?>


<nav class="sub-nav" style="display:block; text-align:center">
	<ul>
		<li><b>Audiences:</b></li>
		<?php foreach ($audienceList as $audience): ?>
		<li><a class="noline" href="/stats/pubtime/<?=urlencode(str_replace('/', '-slash-', $audience))?>"><?=ucwords($audience)?></a></li>
		<?php endforeach; ?>
	</ul>
</nav>




	<div style="display:grid; grid-template-columns: 1fr 1fr; grid-gap:2vw;">


<figure class="mb">
	<h3 class="text-center">Publikationszeit der Artikel</h3>
	<?=$charts->create([
		'metric' => $chart['articles'],
		'dimension' => $chart['dimensions'],
		'color' => '#515151',
		'height' => 250,
		'legend' => 'top',
		'suffix' => 'h',
		'stacked' => false,
		'showValues' => false,
		'name' => 'Artikel',
		'template' => 'charts/default_bar_chart',
	]);?>
</figure>

<figure class="mb">
	<h3 class="text-center">Kaufzeitpunkt in Artikeln (laut Plenigo)</h3>
	<?=$charts->create([
		'metric' => $chart['orders'],
		'dimension' => $chart['dimensions'],
		'color' => '#df886d',
		'height' => 250,
		'legend' => 'top',
		'suffix' => 'h',
		'stacked' => false,
		'showValues' => false,
		'name' => 'Käufe',
		'template' => 'charts/default_bar_chart',
	]);?>
</figure>

	</div>

<figure class="mb">
	<h3 class="text-center">Verteilung gelesener Artikel (laut Google Analytics)</h3>
	<?=$charts->create([
		'metric' => $chart['sessions'],
		'dimension' => $chart['dimensions'],
		'color' => '#6088b4',
		'height' => 350,
		'legend' => 'top',
		'suffix' => 'h',
		'stacked' => false,
		'showValues' => false,
		'name' => 'Leser',
		'template' => 'charts/default_bar_chart',
	]);?>
</figure>




</main>
