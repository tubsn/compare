<main class="">

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<?php if ($info): ?>
<p><?=$info?></p>
<?php endif; ?>



<p class="light-box" style="margin-bottom:2em;">
Gesamtsessions: <b class="conversions"><?=gnum($sessions)?></b>
&emsp; Pageviews: <b class="blue"><?=gnum($pageviews)?></b>
&emsp; davon Indexseiten: <b class="blue"><?=gnum($pageviews-$pageviewsArticle)?></b>
&emsp; davon Artikel: <b class="blue"><?=gnum($pageviewsArticle)?></b>
&emsp; pot. Downloader Anteil: <b class="redish"><?=percentage($pageviews-$pageviewsArticle, $pageviews)?>&thinsp;%</b>
&emsp; Klicks vom Hauptportal (Navi): <b class="orange"><?=gnum($epaperBtnClicks)?></b>
</p>




<div style="display:grid; grid-template-columns: 1fr 1fr; grid-gap:2vw;">

	<figure class="">
		<h3 class="text-center">Ressortvergleich</h3>
		<?=$charts->get('epaper_stats_by_ressort');?>
	</figure>

	<figure class="">
		<h3 class="text-center">Pageviews Entwicklung</h3>
		<?=$charts->get('epaper_stats_by_date');?>
	</figure>

</div>


<table class="fancy js-sortable wide">
	<thead>
		<tr>
			<th>Metric</th>
			<th>Pageviews</th>
			<th>Pageviews Artikel</th>
			<th>Sessions</th>
			<!--<th>Mediatime</th>-->
			<th>⌀-Mediatime</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($dailyStats as $name => $stats): ?>
		<tr>
			<td><a href="/epaper/ressort/<?=$ep->ressort_url($name)?>"><?=$name?></a></td>
			<td><?=gnum($stats['pageviews'])?></td>
			<td><?=gnum($stats['pageviews_article'])?></td>
			<td><?=gnum($stats['sessions'])?></td>
			<!--<td><?=gnum($stats['mediatime'])?></td>-->
			<td><?=gnum($stats['avgmediatime'],2)?>&thinsp;s</td>
		</tr>
<?php endforeach ?>
	</tbody>
</table>


</main>
