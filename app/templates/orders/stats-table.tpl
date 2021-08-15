<?php if ($tableData): ?>
<table class="fancy wide js-sortable">
	<thead><tr>
		<th><?=$tableName ?? 'Name' ?></th>
		<th>Conv</th>
		<th>Aktiv</th>
		<th>Gekündigt</th>
		<th>Tage</th>
		<th>Quote</th>
	</tr></thead>
	<tbody>
<?php foreach ($tableData as $index => $value): ?>
	<tr>
		<?php if ($tableName): ?>
		<td><a href="/<?=strTolower($tableName)?>/<?=$index?>"><?=ucfirst(empty($index)? 'leer' : $index)?></a></td>
		<?php else: ?>
		<td><?=ucfirst(empty($index)? 'leer' : $index)?></a></td>
		<?php endif; ?>

		<td><?=$value['all']?></td>
		<td><?=$value['active']?></td>
		<td><?=$value['cancelled']?></td>
		<td><?=$value['retention'] ?? '-'?></td>
		<td><?=$value['quote'] ?? '-'?> %</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>
