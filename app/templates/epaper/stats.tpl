<main class="">

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<?php if ($info): ?>
<p><?=$info?></p>
<?php endif; ?>


<p class="light-box" style="margin-bottom:2em;">
Sessions: <b class="conversions deepblue"><?=gnum($sessions)?></b>
&emsp; davon Indexseiten: <b class="deepblue"><?=gnum($sessions-$sessionsArticle)?></b>
&emsp; davon Artikel: <b class="deepblue"><?=gnum($sessionsArticle)?></b>
&emsp; pot. Downloader: <b class="redish"><?=percentage($sessions-$sessionsArticle, $sessions)?>&thinsp;%</b>*
&emsp; Pageviews: <b class="blue"><?=gnum($pageviews)?></b>
&emsp; davon Artikel: <b class="blue"><?=gnum($pageviewsArticle)?></b>
&emsp; Klicks vom Hauptportal (Navi): <b class="orange"><?=gnum($epaperBtnClicks)?></b>
</p>



<div style="display:grid; grid-template-columns: 1fr 1fr; grid-gap:2vw;">

	<figure class="">
		<h3 class="text-center">Ausgaben-Verteilung</h3>
		<?=$charts->get('epaper_stats_by_ressort');?>
	</figure>

	<figure class="">
		<h3 class="text-center">Pageviews-Entwicklung</h3>
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
			<th>Sessions Artikel</th>
			<th>Pot. Downloader*</th>
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
			<td><?=gnum($stats['sessions_article'])?></td>
			<td><?=percentage($stats['sessions']-$stats['sessions_article'], $stats['sessions'])?>&thinsp;%</td>
			<!--<td><?=gnum($stats['mediatime'])?></td>-->
			<td><?=gnum($stats['avgmediatime'],2)?>&thinsp;s</td>
		</tr>
<?php endforeach ?>
	</tbody>
</table>

<small><b>*Potentielle Downloader:</b> Sessions, die nur Indexseiten und keinen einzigen Artikel aufgerufen haben.</small>

</main>
