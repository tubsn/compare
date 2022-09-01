<main class="">

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<p>Übersicht einlaufender Bestellungen auf Basis der Plenigo Daten, bei denen sich ein Artikel zuordnen lässt.</p>

<?php if ($orders_by_day): ?>

<?php foreach ($orders_by_day as $day => $orders): ?>

<h1 class="text-center"><?=$day?> | Gesamt-Bestellungen: <?=count($orders)?></h1>

<?php 
	$ressorts = @array_count_values(array_column($orders, 'article_ressort'));
	$ids = @array_count_values(array_column($orders, 'article_id'));
	arsort($ids);
	arsort($ressorts);
	$articles = array_group_by('article_id', $orders);

	$out = [];
	foreach ($articles as $id => $article) {
		$article[0]['conversions'] = $ids[$id] ?? 0;
		$article[0]['article_id'] = $id ?? 0;
		array_push($out, $article[0]);
	}
	$articles = $out;
	usort($articles, function($a, $b) {
	    return $b['conversions'] <=> $a['conversions'];
	});
?>


<table class="fancy mb wide js-sortable" style="margin-bottom:4em;">
<thead>
<tr class="text-left">
	<th>Thumb</th>
	<th>Conv.</th>
	<th>Dachzeile</th>
	<th>Überschrift</th>
	<th>Ressort</th>
	<th>Gesamt Conv.</th>
	<th>Gesamt Künd.</th>
</tr>
</thead>

<tbody>
<?php foreach ($articles as $id => $article): ?>
	<?php if (empty($article['conversions']) || empty($article['article_title'])): ?>
		<?php continue;?>
	<?php endif ?>
<tr>
	<td><img style="width:60px" src="<?=$article['article_image']?>"></td>
	<td class="text-center"><span class="conversions"><?=$article['conversions']?></span></td>
	<td><?=$article['article_kicker']?></td>
	<td><a href="/artikel/<?=$article['article_id']?>"><?=$article['article_title']?></a></td>
	<td><?=ucfirst($article['article_ressort'])?></td>
	<td><?=$article['article_conversions']?></td>
	<td><?=$article['article_cancellations']?></td>
</tr>	
<?php endforeach ?>
</tbody>
</table>

<?php endforeach ?>

<?php else: ?>
<h3>keine Conversions</h3>
<?php endif; ?>


</main>
