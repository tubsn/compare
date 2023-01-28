<main>

<?php include tpl('navigation/date-picker');?>

<h1><?=$page['title'] ?? 'Statistiken'?></h1>


	<figure>
		<?=$charts->create([
			'metric' => [$chart['mediatime'],$chart['buddy_mediatime'],$chart['buddy2_mediatime']],
			'dimension' => $chart['dimensions'],
			'color' => [COLOR_LR,COLOR_MOZ,COLOR_SWP],
			'height' => 950,
			'legend' => 'top',
			'area' => false,
			'stacked' => false,
			'showValues' => false,
			'name' => ['LR','MOZ','SWP'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

</main>