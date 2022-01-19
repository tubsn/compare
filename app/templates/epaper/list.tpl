<main class="">


<nav class="sub-nav" style="display:block; text-align:center">
	<ul>
		<li><b>Ausgaben:</b></li>
		<li><a href="/epaper">Alle</a></li>
		<?php foreach ($ressortNavi as $naviname => $navilink): ?>
		<li><a class="noline" href="/epaper/ressort/<?=$navilink?>"><?=$naviname?></a></li>
		<?php endforeach; ?>
	</ul>
</nav>

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>


<p class="light-box" style="margin-bottom:2em;">
Sessions: <b class="conversions deepblue"><?=gnum($sessions)?></b>
&emsp; davon Indexseiten: <b class="deepblue"><?=gnum($sessions-$sessionsArticle)?></b>
&emsp; davon Artikel: <b class="deepblue"><?=gnum($sessionsArticle)?></b>
&emsp; pot. Anteil Downloader: <b class="redish"><?=percentage($sessions-$sessionsArticle, $sessions)?>&thinsp;%</b>
&emsp; Pageviews: <b class="blue"><?=gnum($pageviews)?></b>
&emsp; davon Artikel: <b class="blue"><?=gnum($pageviewsArticle)?></b>
</p>


<?php if ($info): ?>
<p><?=$info?></p>
<?php endif; ?>

<?php if (isset($chart)): ?>
<?=$chart?>
<?php endif; ?>

<?php if ($articles): ?>
<table class="fancy mb wide js-sortable">
<thead>
<tr class="text-right">
	<th style="text-align:center">ID</th>
	<th style="text-align:left">Titel</th>
	<th style="text-align:right">Ausgaben</th>
	<th>ET</th>
	<th>Pageviews</th>
	<th>⌀-Mediatime</th>
</tr>
</thead>
<tbody>

<?php foreach ($articles as $article): ?>
<tr class="text-right">


	<td class="text-center"><a href="/epaper/artikel/<?=$article['id']?>"><?=$article['id']?></a></td>

	<td class="text-left"><a target="_blank" href="<?=PORTAL_EPAPER_URL?><?=$article['url']?>"><?=$article['title']?></a></td>
	<td>
		<?php if (is_array($article['ressort'])): ?>

			<a href="/epaper/ressort/<?=$article['garessort']?>">(<?=count($article['ressort']);?>)</a>
			<?php foreach ($article['ressort'] as $ressort): ?>
			<!--<a href="/epaper/ressort/<?=$ressort?>"><?=$ressort?></a>-->
			<?php endforeach; ?>
			<?php else: ?>
			<a href="/epaper/ressort/<?=$article['garessort']?>"><?=$article['ressort']?></a>
		<?php endif; ?>
	</td>

	<td data-sortdate="<?=$article['pubdate']?>"><?=formatDate($article['pubdate'], 'd.m.Y')?></td>
	<td><?=$article['pageviews']?></td>
	<td data-sortdate="<?=number_format($article['mediatime'],2,'.',',')?>"><?=gnum($article['mediatime'],2)?>&thinsp;s</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php else: ?>
<h3>keine Artikel</h3>
<?php endif; ?>

</main>
