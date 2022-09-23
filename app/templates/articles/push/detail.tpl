<main>

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<?php if ($info): ?>
<p><?=$info?></p>
<?php endif; ?>


<p class="light-box" style="margin-bottom:2em;">
Versand: <b><?=$notification['delivered']?></b>
&emsp; Klicks: <b class="blue"><?=$notification['clicks']?></b>
&emsp; Klickrate: <b class="blue"><?=$notification['clickrate']?> %</b>
&emsp; Status: <b class="deepblue"><?=ucfirst($notification['status'])?></b>
&emsp; Paywall Klicks: <b class="conversions"><?=$notification['paywall_clicks']?></b>
&emsp; Abmeldungen/OptOuts: <b class="redish"><?=$notification['optOuts']?></b>
</p>

<figure style="width:500px; margin-bottom:2em;"><img src="<?=$notification['thumb']?>"></figure>

<p><?=$notification['text']?></p>

<hr>

<div class="col-2">
	<figure class="mb">
		<h3 class="text-center" style="margin:0">Auslieferungen der Pushnachricht</h3>
		<?=$charts->create([
			'metric' => $hourlyStats['delivered'],
			'dimension' => $hourlyStats['dimensions'],
			'color' => '#6088b4',
			'height' => 200,
			'stacked' => false,
			'showValues' => false,
			'name' => 'Geliefert',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center" style="margin:0">Klicks auf die Pushnachricht</h3>
		<?=$charts->create([
			'metric' => $hourlyStats['clicks'],
			'dimension' => $hourlyStats['dimensions'],
			'color' => '#314e6f',
			'height' => 200,
			'stacked' => false,
			'showValues' => false,
			'name' => 'Klicks',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>
</div>

<?php if ($notification['article']): ?>

<h3>Verlinkter Artikel</h3>
<details class="fancy">
<summary><?=$notification['article']['title']?> (ID: <?=$notification['article']['id']?>)</summary>
<div class="box">
<?php dump($notification['article']);?>
</div>
</details>
<?php endif ?>

<details>
	<summary>Restinfos</summary>
<?php dump($notification);?>
</details>

</main>
