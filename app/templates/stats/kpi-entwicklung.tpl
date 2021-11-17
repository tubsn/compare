<main>

<?php include tpl('navigation/date-picker');?>

<h1><?=$page['title'] ?? 'Statistiken'?></h1>

<style>
.bronze {background:#ec8e49;}
.silver {background:#b7b7b7;}
.gold {background:#fdd765;}
.negative {background:#dd7474;}
</style>

<table class="fancy mb wide js-sortable">
<thead>
<tr>
	<th>Ressort</th>
	<th>Pageviews</th>
	<th>Subscribers</th>
	<th>Conversions</th>
	<th>Mediatime</th>
</tr>
</thead>
<tbody>
<?php foreach ($ressorts as $ressort => $stats): ?>
<tr>
	<td><?=ucfirst($ressort)?></td>
	<td class="<?=$stats['pvclass']?>"><?=$stats['pageviews']?>&thinsp;%</td>
	<td class="<?=$stats['subclass']?>"><?=$stats['subscribers']?>&thinsp;%</td>
	<td class="<?=$stats['convclass']?>"><?=$stats['conversions']?>&thinsp;%</td>
	<td class="<?=$stats['mclass']?>"><?=$stats['mediatime']?>&thinsp;%</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<hr>


<details>
<summary>Kennzahlen im selektierten Zeitraum</summary>

<table class="fancy mb wide js-sortable">
<thead>
<tr>
	<th>Ressort</th>
	<th>Ø-Pageviews</th>
	<th>Ø-Subscribers</th>
	<th>Ø-Conversions</th>
	<th>Ø-Mediatime</th>
</tr>
</thead>
<tbody>
<?php foreach ($currentStats as $ressort => $stats): ?>
<tr>
	<td><?=ucfirst($ressort)?></td>
	<td><?=$stats['pageviews']?></td>
	<td><?=$stats['subscribers']?></td>
	<td><?=$stats['conversions']?></td>
	<td><?=$stats['mediatime']?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<a class="button" href="/export/linkpulse/current">Aktuelle Daten exportieren</a>

</details>

<details>
<summary>Jahres-Durchschnitts-Werte</summary>

<table class="fancy mb wide js-sortable">
<thead>
<tr>
	<th>Ressort</th>
	<th>Ø-Pageviews</th>
	<th>Ø-Subscribers</th>
	<th>Ø-Conversions</th>
	<th>Ø-Mediatime</th>
</tr>
</thead>
<tbody>
<?php foreach ($baseStats as $ressort => $stats): ?>
<tr>
	<td><?=ucfirst($ressort)?></td>
	<td><?=$stats['pageviews']?></td>
	<td><?=$stats['subscribers']?></td>
	<td><?=$stats['conversions']?></td>
	<td><?=$stats['mediatime']?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<a class="button" href="/export/linkpulse/halftime">Jahres Daten exportieren</a>

</details>


</main>
