<table class="fancy mb wide js-sortable font-small compact">
<thead>
<tr class="text-left">
	<th class="text-left">TransaktionsID</th>
	<th class="text-left">Kunde</th>
	<th>Datum</th>
	<th>Uhrzeit</th>
	<th>Provider</th>
	<th>Zahlmethode</th>
	<th>Aktion</th>
	<th>Betrag</th>
	<th>Fehlercode</th>
	<th>Nachricht</th>
</tr>
</thead>
<tbody>

<?php foreach ($transactions as $transaction): ?>
<tr class="text-left">

	<td class="narrower" title="<?=$transaction['plenigoTransactionId']?>"><?=$transaction['plenigoTransactionId']?></td>
	<td><a target="_blank" href="https://backend.plenigo.com/<?=PLENIGO_COMPANY_ID?>/customers/<?=$transaction['customerId']?>/show"><?=$transaction['customerId']?></a></td>
	<td><?=date("Y-m-d", strtotime($transaction['transactionDate']))?></td>
	<td><?=date("H:i", strtotime($transaction['transactionDate']))?></td>

	<?php if ($transaction['paymentProvider'] == 'STRIPE'): ?>
	<td><span class="stripe"><?=$transaction['paymentProvider']?></span></td>
	<?php elseif ($transaction['paymentProvider'] == 'PAYPAL'): ?>
	<td><span class="paypal"><?=$transaction['paymentProvider']?></span></td>
	<?php else: ?>
	<td><?=$transaction['paymentProvider']?></td>
	<?php endif ?>
	<td><?=$transaction['paymentMethod']?></td>
	<td><?=$transaction['paymentAction']?></td>
	<td data-sortdate="<?=$transaction['amount']*1000?>"><?=number_format($transaction['amount'], 2,',','.')?>&thinsp;€</td>
	<td><?=$transaction['errorCode'] ?? '-'?></td>
	<td><?=$transaction['errorMessage'] ?? '-'?></td>

</tr>
<?php endforeach; ?>
</tbody>
</table>