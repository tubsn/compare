<main class="">

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<p><a href="/epaper/ressorts">Ressortliste</a> | <a href="/epaper">Artikelliste</a></p>

<!--
<p class="light-box" style="margin-bottom:2em;">
Gesamtbestellungen: <b class="conversions"></b>
&emsp; davon Plusseite: <b class="blue"></b>
&emsp; davon Aboshop: <b class="blue"></b>
&emsp; davon Gekündigt: <b class="redish"></b>
&emsp; Kündigerquote: <b class="orange">&thinsp;%</b>
&emsp; ⌀-Haltedauer: <b class="blue"><?=number_format('1',2,',','.')?> Tage</b>
</p>
-->


<?php if ($articles): ?>
<table class="fancy mb wide js-sortable">
<thead>
<tr class="text-left">
	<th>ID</th>
	<th>Titel</th>
	<th>Ressort</th>
	<th>Datum</th>
	<th>Pageviews</th>
	<th>Mediatime</th>
</tr>
</thead>
<tbody>

<?php foreach ($articles as $articles): ?>
<tr class="text-left">
	<td><?=$articles['id']?></td>
	<td><a href="/epaper/artikel/<?=$articles['id']?>"><?=$articles['title']?></a></td>
	<td><a href="/epaper/ressort/<?=$articles['garessort']?>"><?=$articles['ressort']?></a></td>
	<td><?=$articles['pubdate']?></td>
	<td><?=$articles['pageviews']?></td>
	<td><?=$articles['mediatime']?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php else: ?>
<h3>keine Artikel</h3>
<?php endif; ?>

</main>
