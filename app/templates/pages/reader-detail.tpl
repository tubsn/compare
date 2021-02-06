<main class="">

<article>

	<h1>Nutzer: <?=$user['firstname']?> <?=$user['lastname']?></h1>
	E-Mail: <?=$user['email']?><br />
	Geschlecht: <?=$user['gender']?><br />
	Stadt: <?=$user['city']?><br />


	<h3>Abos:</h3>
	<?php if ($subscriptions): ?>
	<?php foreach ($subscriptions as $subscription): ?>
	<div class="box">
		<h3><?=$subscription['title']?></h3>
		ProduktID: <?=$subscription['productId']?><br />
		Preis: <?=$subscription['price']?> <?=$subscription['currency']?><br />
		Bezahlmethode: <?=$subscription['paymentMethod']?><br />
		Start: <?=date('d.m.Y H:i',$subscription['startDate'])?> Uhr<br />
		<?php if ($subscription['cancellationDate']): ?>
		Kündigung: <?=date('d.m.Y H:i',$subscription['cancellationDate'])?> Uhr<br />
		<?php endif ?>		
	</div>		
	<?php endforeach ?>
	<?php else: ?>
	<p>keine aktiven Abos</p>
	<?php endif ?>


	<h3>Gekaufte Produkte:</h3>
	<?php if ($products): ?>
	<?php foreach ($products as $product): ?>
	<div class="box">
		Produkt: <?=$product['title']?> (ID: <?=$product['id']?>)<br />
		Bezahlmethode: <?=$product['paymentMethod']?><br />
		Preis: <?=$product['price']?> <?=$product['currency']?><br />
		Typ: <?=$product['productType']?><br />
		Startdatum:  <?=date('d.m.Y H:i',$product['startDate'])?> Uhr<br />
		Enddatum: <?=$product['endDate'] ?? 'offen'?><br />
	</div>		
	<?php endforeach ?>
	<?php else: ?>
	<p>keine aktiven Produkte</p>
	<?php endif ?>

	<hr/>

	<h3>Gelesene Artikel:</h3>
	<?php if ($articles): ?>
	<table class="fancy wide js-sortable condensed">
		<thead>
			<tr>
				<th>Dachzeile</th>
				<th></th>
				<th>Title</th>
				<th>Inhaltstyp</th>
				<th>Ressort</th>
				<th>Autor</th>
				<th>Klicks</th>
				<th>Conversions</th>
				<th>Publikationsdatum</th>
				<th>Lesegerät</th>
				<th>Referer</th>
				<th>Lesezeitpunkt</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($articles as $article): ?>
		<tr>
			<td class="narrower text-right"><?=$article['kicker'] ?? '-'?></td>
			<td><?php if (isset($article['plus']) && $article['plus']): ?><div class="bluebg">+</div><?php endif; ?></td>
			<td><a href="/artikel/<?=$article['id']?>"><?=$article['title']?></a></td>
			<td><?=$article['type'] ?? '-'?></td>
			<td><?=ucwords($article['ressort'] ?? '-')?></td>
			<td><?=$article['author'] ?? '-'?></td>
			<td class="text-right"><div<?php if (isset($article['pageviews']) && $article['pageviews']> 2500): ?> class="pageviews"<?php endif; ?>><?=number_format($article['pageviews'] ?? 0,0,'.','.')?></div></td>
			<td class="text-right"><div<?php if (isset($article['conversions']) && $article['conversions'] > 0): ?> class="conversions"<?php endif; ?>><?=number_format($article['conversions'] ?? 0,0,'.','.')?></div></td>
			<td><?=$article['pubdate'] ?? '-'?></td>
			<td><?=ucwords($article['device'])?></td>
			<td><?=ucwords($article['channel'])?></td>
			<td><?=date('Y.m.d H:i', $article['date'])?>&nbsp;Uhr</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php else: ?>
	Keine Lesedaten Vorhanden	
	<?php endif ?>

</article>

</main>