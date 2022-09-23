<main>

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<figure class="mb">
	<?=$charts->create([
		'metric' => [$data['plenigo'], $data['kilkaya']],
		'dimension' => $data['dimensions'],
		'color' => ['#f77474', '#1572A1'],
		'height' => 650,
		'legend' => 'top',

		'stacked' => false,
		'showValues' => true,
		'name' => ['Plenigo', 'Kilkaya'],
		'template' => 'charts/default_bar_chart',
	]);?>
</figure>



</main>
