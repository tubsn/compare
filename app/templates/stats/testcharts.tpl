<main>

<h1 class="text-center"><?=$page['title'] ?? 'Statistiken'?></h1>
<?php include tpl('navigation/date-picker');?>

['#df886d', '#0967a8', '#e50046']

<?=dump($active)?>

<?php

$metric = [

'1,3,4,2',
'2,34,5,5',

]
?>

	<figure class="mb">
		<h3 class="text-center">⌀-Mediatime Entwicklung in Sekunden (GA)</h3>
		<?=$charts->create([
			'metric' => $metric,
			'dimension' => "'Juli2021','August2021'",
			'color' => ['#a2b495', '#aeb9c5', '#8a9aad' ,'#4f6884'],
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'name' => 'stuff',
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>


<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr">



</div>

</main>
