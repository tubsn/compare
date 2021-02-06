<main>

<div class="calendar-container">
	<div class="calendar-picker">
		<form action="/settimeframe" method="post">
			<select name="timeframe" class="js-timeframe">
				<?php if (session('timeframe') == 'Zeitraum'): ?>
				<option>gewählter Zeitraum</option>
				<?php endif; ?>
				<?php foreach (TIMEFRAMES as $timeframe): ?>
				<?php if (session('timeframe') == $timeframe): ?>
				<option selected><?=session('timeframe')?></option>
				<?php else: ?>
				<option><?=$timeframe?></option>
				<?php endif; ?>
				<?php endforeach ?>
			</select>
		</form>
		&thinsp;
		<form method="post" action="/settimeframe">
			<fieldset>
				<input type="date" name="from" value="<?=session('from')?>"> -
				<input type="date" name="to" value="<?=session('to')?>">
			</fieldset>
			<button class="calendar-button" type="submit"></button>
		</form>
	</div>
</div>


<h1>Allgemeine Statistiken</h1>

<p>Hinweis es werden nur Klicks auf selbstproduzierte Artikel gezählt, Übersichtsseiten, Bildergalerien, DPA-Artikel u.ä. sind außen vor.<br/>Die Daten werden aus performance Gründen <b>1 Minute lang gecached.</b></p>

<p><b>Plusquote</b> = Plusartikel / Artikel | <b>Conversionrate</b> = Conversions / Besuche | <b>Artikel bis Conversion</b> = Artikel / Conversions
</p>

<p class="light-box" style="margin-bottom:2em;">
Artikel: <b><?=$articles?></b> &emsp; Klicks: <b class="blue"><?=number_format($pageviews,0,',','.')?></b> &emsp; Besuche: <b class="blue"><?=number_format($sessions,0,',','.')?></b> &emsp; Conversions: <b class="orange"><?=$conversions?></b>
</p>

<p class="light-box" style="margin-bottom:2em;">
	<?php if (session('from')): ?>
	Filter-Zeitraum: <b><?=session('from')?></b> bis <b><?=session('to')?></b>
	<?php else: ?>
	Filter-Zeitraum: <b>alle Daten</b>
	<?php endif ?>
</p>

<section class="stats-layout">
	<div>
		<h1>Statistiken nach Ressort</h1>
		<?php if ($ressortStats): ?>
		<table class="fancy mb wide js-sortable">
		<thead>
		<tr class="text-right">
			<th class="text-left">Ressort</th>
			<th>Artikel</th>
			<th>Plusartikel</th>
			<th>Plus Quote</th>
			<th>Klicks</th>
			<th>Besuche</th>
			<th>Conversions</th>
			<th>Artikel bis Conversion</th>
			<th>Conversionrate</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($ressortStats as $ressort => $stats): ?>
		<tr class="text-right">
			<td class="text-left"><a href="/ressort/<?=urlencode(str_replace('/', '-slash-', $ressort))?>"><?=ucwords($ressort)?></a></td>
			<td><?=$stats['artikel']?></td>
			<td><?=$stats['plus']?></td>
			<td><?=round($stats['plus'] / $stats['artikel'] * 100,2)?>&nbsp;%</td>
			<td><div<?php if ($stats['pageviews'] > 0): ?> class="pageviews"<?php endif; ?>><?=number_format($stats['pageviews'],0,'.','.') ?? 0?></div></td>
			<td><?=number_format($stats['sessions'],0,'.','.') ?? 0?></td>
			<td><div<?php if ($stats['conversions'] > 0): ?> class="conversions"<?php endif; ?>><?=number_format($stats['conversions'],0,'.','.') ?? 0?></div></td>
			<?php if ($stats['conversions'] > 0): ?>
			<td><?=round($stats['artikel'] / $stats['conversions'],2)?></td>
			<?php else: ?>
			<td>-</td>
			<?php endif ?>
			<td><?=round($stats['conversions'] / $stats['sessions'] * 100,3)?>&nbsp;%</td>
		</tr>
		<?php endforeach; ?>
		</tbody>
		</table>
		<?php else: ?>
		<h3>keine Artikel</h3>
		<?php endif; ?>
	</div>

	<div>
		<h1>Statistiken nach Inhaltstyp</h1>
		<?php if ($typeStats): ?>
		<table class="fancy mb wide js-sortable">
		<thead>
		<tr class="text-right">
			<th class="text-left">Klasse</th>
			<th>Artikel</th>
			<th>Plusartikel</th>
			<th>Plus Quote</th>
			<th>Klicks</th>
			<th>Besuche</th>
			<th>Conversions</th>
			<th>Artikel bis Conversion</th>
			<th>Conversionrate</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($typeStats as $type => $stats): ?>
		<tr class="text-right">
			<td class="text-left"><a href="/type/<?=urlencode(str_replace('/', '-slash-', $type))?>"><?=$type?></a></td>
			<td><?=$stats['artikel']?></td>
			<td><?=$stats['plus']?></td>
			<td><?=round($stats['plus'] / $stats['artikel'] * 100,2)?> %</td>
			<td><div<?php if ($stats['pageviews'] > 0): ?> class="pageviews"<?php endif; ?>><?=number_format($stats['pageviews'],0,'.','.') ?? 0?></div></td>
			<td><?=number_format($stats['sessions'],0,'.','.') ?? 0?></td>
			<td><div<?php if ($stats['conversions'] > 0): ?> class="conversions"<?php endif; ?>><?=number_format($stats['conversions'],0,'.','.') ?? 0?></div></td>
			<?php if ($stats['conversions'] > 0): ?>
			<td><?=round($stats['artikel'] / $stats['conversions'],2)?></td>
			<?php else: ?>
			<td>-</td>
			<?php endif ?>
			<td><?=round($stats['conversions'] / $stats['sessions'] * 100,3)?>&nbsp;%</td>
		</tr>
		<?php endforeach; ?>
		</tbody>
		</table>
		<?php else: ?>
		<h3>keine Artikel</h3>
		<?php endif; ?>
	</div>

<?php if (auth_rights('type')): ?>
	<div>
		<h1>Statistiken nach Autorenprofil</h1>
		<?php if ($authorStats): ?>
		<table class="fancy mb wide js-sortable">
		<thead>
		<tr class="text-right">
			<th class="text-left">Autor</th>
			<th>Artikel</th>
			<th>Plusartikel</th>
			<th>Plus Quote</th>
			<th>Klicks</th>
			<th>Besuche</th>
			<th>Conversions</th>
			<th>Artikel bis Conversion</th>
			<th>Conversionrate</th>
		</tr>
		</thead><tbody>
		<?php foreach ($authorStats as $author => $stats): ?>
		<tr class="text-right">
			<td class="text-left"><a href="/author/<?=urlencode(str_replace('/', '-slash-', $author))?>"><?=$author?></a></td>
			<td><?=$stats['artikel']?></td>
			<td><?=$stats['plus']?></td>
			<td><?=round($stats['plus'] / $stats['artikel'] * 100,2)?> %</td>
			<td><div<?php if ($stats['pageviews'] > 0): ?> class="pageviews"<?php endif; ?>><?=number_format($stats['pageviews'],0,'.','.') ?? 0?></div></td>
			<td><?=number_format($stats['sessions'],0,'.','.') ?? 0?></td>
			<td><div<?php if ($stats['conversions'] > 0): ?> class="conversions"<?php endif; ?>><?=number_format($stats['conversions'],0,'.','.') ?? 0?></div></td>
			<?php if ($stats['conversions'] > 0): ?>
			<td><?=round($stats['artikel'] / $stats['conversions'],2)?></td>
			<?php else: ?>
			<td>-</td>
			<?php endif ?>
			<td><?=round($stats['conversions'] / $stats['sessions'] * 100,3)?>&nbsp;%</td>
		</tr>
		<?php endforeach; ?>
		</tbody>
		</table>
		<?php else: ?>
		<h3>keine Artikel</h3>
		<?php endif; ?>
	</div>
<?php endif ?>

</section>

<p><b>Plusquote</b> = Plusartikel / Artikel | <b>Conversionrate</b> = Conversions / Besuche | <b>Artikel bis Conversion</b> = Artikel / Conversions</p>

</main>
