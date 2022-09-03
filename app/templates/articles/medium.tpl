<main class="detail-layout">

<div>
<h1><?=$article['title']?></h1>
<?=$article['description']?></div>

<table class="fancy js-sortable">
	<thead>
		<th>Prozent</th>
		<th>Quelle</th>
		<th>Pageviews</th>
		<th>Conversions</th>
	</thead>
	<tbody>
<?php foreach ($medium as $item): ?>
	<tr>
		<td><?=round($item['Pageviews'] / $article['pageviews'] * 100,1)?>&thinsp;%</td>
		<td><?=$item['Source']?></td>
		<td><?=$item['Pageviews']?></td>
		<td><?=$item['Itemquantity']?></td>
	</tr>
<?php endforeach ?>
	</tbody>
</table>

<img src="<?=$article['image']?>"/>

</main>
