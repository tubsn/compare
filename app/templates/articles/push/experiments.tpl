<main>

<?php include tpl('navigation/date-picker');?>

<style>
	.calendar-timeframe, .calendar-datepicker {display:none;}
</style>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<?php if ($info): ?>
<p><?=$info?></p>
<?php endif; ?>


<?php
//dump($stats);
?>

<?php foreach ($topics as $topic): ?>
	<a href="?topic=<?=$topic?>"><?=$topic?></a>
<?php endforeach ?>

<hr>


	<figure class="mb">
		<h3 class="text-center" style="margin:0">⌀-Klickrate <?=$selected?></h3>
		<?=$charts->create([
			'metric' => $stats['avgclickrate'],
			'dimension' => $stats['dimensions'],
			'color' => '#B25068',
			'height' => 550,
			'stacked' => false,
			'showValues' => false,
			'percent' => true,
			'name' => 'Klickrate',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>


</main>
