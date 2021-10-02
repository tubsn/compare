<?php if ($tableData): ?>
<table class="fancy wide js-sortable font-small compact mbig">
	<thead><tr>
		<th><?=$tableName ?? 'Name' ?></th>
		<th>Conv</th>
		<th>Aktiv</th>
		<th>Künd</th>
		<th>Ø-Tage</th>
		<th class="text-right">Quote</th>
	</tr></thead>
	<tbody>
<?php foreach ($tableData as $index => $value): ?>
	<tr>
		<?php if ($tableName): ?>
		<td class="narrow"><a href="/<?=strTolower($tableName)?>/<?=$index?>"><?=ucfirst(empty($index)? 'leer' : $index)?></a></td>
		<?php else: ?>
		<td class="narrow"><?=ucfirst(empty($index)? 'leer' : $index)?></td>
		<?php endif; ?>

		<td><?=$value['all']?></td>
		<td class="blue"><?=$value['active']?></td>
		<td class="redish"><?=$value['cancelled']?></td>
		<td><?=$value['retention'] ?? '-'?></td>
		<td class="text-right"><?=$value['quote'] ?? '-'?> %</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>
