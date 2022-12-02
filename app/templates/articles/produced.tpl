<main>

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<?php if ($info): ?>
<p><?=$info?></p>
<?php endif; ?>


<table class="fancy">
<?php foreach ($articles as $ressort => $counts): ?>
	
<tr>
	<td><?=ucfirst($ressort)?>
	<td><?=round(array_sum($counts) / count($counts),2) ?></td>
</tr>



<?php endforeach ?>
</table>

<details><summary>Artikel Produktion pro Tag im Detail</summary>
<?php dump($articles)?>
</details>


</main>
