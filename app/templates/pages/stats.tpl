<main>

<?php include tpl('navigation/date-picker');?>

<h1>Allgemeine Statistiken</h1>

<p>Es werden nur Klicks auf selbstproduzierte Artikel gezählt, Übersichtsseiten, Bildergalerien, DPA-Artikel u.ä. sind außen vor.<br/>
Daten werden seit Lebensdauer des Artikels angezeigt. Der eingestellte Zeitraum filtert das <b>Publikationsdatum des Artikels!</b> Nicht die eigentlichen Daten! </p>
<p><b>Kündigungsdaten sind NICHT Live</b> und beziehen sich auf den Tag der letzten manuellen Aktualisierung des Artikels.<br />
Die Kündigerraten sind vor Februar 2021 nicht relevant, da hier zuviele Daten fehlen!</p>

<p>Alle Daten werden aus performance Gründen <b>1 Minute lang gecached.</b></p>

<p><b>Plusquote</b> = Plusartikel / Artikel | <b>Conversionrate</b> = Conversions / Besuche | <b>Artikel bis Conversion</b> = Artikel / Conversions
</p>

<p class="light-box" style="margin-bottom:2em;">
Artikel: <b><?=$articles?></b> &emsp; Klicks: <b class="blue"><?=number_format($pageviews,0,',','.')?></b> &emsp; Besuche: <b class="blue"><?=number_format($sessions,0,',','.')?></b> &emsp; Conversions: <b class="orange"><?=$conversions?></b>
&emsp; Gekündigt: <b class="redish"><?=$cancelled?></b>
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
			<th>A. bis Conversion</th>
			<th>Plusartikel</th>
			<th>Plus Quote</th>
			<th>Klicks</th>
			<th>Besuche</th>
			<th>Conversions</th>
			<th>Conversionrate</th>
			<th>Gekündigt</th>
			<th>K-Quote</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($ressortStats as $ressort => $stats): ?>
		<tr class="text-right">
			<td class="text-left"><a href="/ressort/<?=urlencode(str_replace('/', '-slash-', $ressort))?>"><?=ucwords($ressort)?></a></td>
			<td><?=$stats['artikel']?></td>

			<?php if ($stats['conversions'] > 0): ?>
			<td><?=round($stats['artikel'] / $stats['conversions'],1)?></td>
			<?php else: ?>
			<td>-</td>
			<?php endif ?>

			<td><?=$stats['plus']?></td>
			<td><?=round($stats['plus'] / $stats['artikel'] * 100,2)?>&nbsp;%</td>
			<td><div<?php if ($stats['pageviews'] > 0): ?> class="pageviews"<?php endif; ?>><?=number_format($stats['pageviews'],0,'.','.') ?? 0?></div></td>
			<td><?=number_format($stats['sessions'],0,'.','.') ?? 0?></td>
			<td><div<?php if ($stats['conversions'] > 0): ?> class="conversions"<?php endif; ?>><?=number_format($stats['conversions'],0,'.','.') ?? 0?></div></td>

			<td><?=round($stats['conversions'] / $stats['sessions'] * 100,3)?>&nbsp;%</td>
			<td><div<?php if ($stats['cancelled'] > 0): ?> class="cancelled"<?php endif; ?>><?=$stats['cancelled']?></div></td>

			<?php if ($stats['conversions'] > 0): ?>
			<td><?=round($stats['cancelled'] / $stats['conversions'] * 100,1)?>&nbsp;%</td>
			<?php else: ?>
			<td>-</td>
			<?php endif; ?>
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
			<th>A. bis Conversion</th>
			<th>Plusartikel</th>
			<th>Plus Quote</th>
			<th>Klicks</th>
			<th>Besuche</th>
			<th>Conversions</th>
			<th>Conversionrate</th>
			<th>Gekündigt</th>
			<th>K-Quote</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($typeStats as $type => $stats): ?>
		<tr class="text-right">
			<td class="text-left"><a href="/type/<?=urlencode(str_replace('/', '-slash-', $type))?>"><?=$type?></a></td>
			<td><?=$stats['artikel']?></td>

			<?php if ($stats['conversions'] > 0): ?>
			<td><?=round($stats['artikel'] / $stats['conversions'],2)?></td>
			<?php else: ?>
			<td>-</td>
			<?php endif ?>

			<td><?=$stats['plus']?></td>
			<td><?=round($stats['plus'] / $stats['artikel'] * 100,2)?> %</td>
			<td><div<?php if ($stats['pageviews'] > 0): ?> class="pageviews"<?php endif; ?>><?=number_format($stats['pageviews'],0,'.','.') ?? 0?></div></td>
			<td><?=number_format($stats['sessions'],0,'.','.') ?? 0?></td>
			<td><div<?php if ($stats['conversions'] > 0): ?> class="conversions"<?php endif; ?>><?=number_format($stats['conversions'],0,'.','.') ?? 0?></div></td>

			<td><?=round($stats['conversions'] / $stats['sessions'] * 100,3)?>&nbsp;%</td>
			<td><div<?php if ($stats['cancelled'] > 0): ?> class="cancelled"<?php endif; ?>><?=$stats['cancelled']?></div></td>

			<?php if ($stats['conversions'] > 0): ?>
			<td><?=round($stats['cancelled'] / $stats['conversions'] * 100,1)?>&nbsp;%</td>
			<?php else: ?>
			<td>-</td>
			<?php endif; ?>
		</tr>
		<?php endforeach; ?>
		</tbody>
		</table>
		<?php else: ?>
		<h3>keine Artikel</h3>
		<?php endif; ?>
	</div>

<?php if (auth_rights('author')): ?>
	<div>
		<h1>Statistiken nach Autorenprofil</h1>
		<?php if ($authorStats): ?>
		<table class="fancy mb wide js-sortable">
		<thead>
		<tr class="text-right">
			<th class="text-left">Autor</th>
			<th>Artikel</th>
			<th>Artikel bis Conversion</th>
			<th>Plusartikel</th>
			<th>Plus Quote</th>
			<th>Klicks</th>
			<th>Besuche</th>
			<th>Conversions</th>
			<th>Conversionrate</th>
			<th>Gekündigt</th>
			<th>K-Quote</th>
		</tr>
		</thead><tbody>
		<?php foreach ($authorStats as $author => $stats): ?>
		<tr class="text-right">
			<td class="text-left"><a href="/author/<?=urlencode(str_replace('/', '-slash-', $author))?>"><?=$author?></a></td>
			<td><?=$stats['artikel']?></td>

			<?php if ($stats['conversions'] > 0): ?>
			<td><?=round($stats['artikel'] / $stats['conversions'],2)?></td>
			<?php else: ?>
			<td>-</td>
			<?php endif ?>

			<td><?=$stats['plus']?></td>
			<td><?=round($stats['plus'] / $stats['artikel'] * 100,2)?> %</td>
			<td><div<?php if ($stats['pageviews'] > 0): ?> class="pageviews"<?php endif; ?>><?=number_format($stats['pageviews'],0,'.','.') ?? 0?></div></td>
			<td><?=number_format($stats['sessions'],0,'.','.') ?? 0?></td>
			<td><div<?php if ($stats['conversions'] > 0): ?> class="conversions"<?php endif; ?>><?=number_format($stats['conversions'],0,'.','.') ?? 0?></div></td>

			<td><?=round($stats['conversions'] / $stats['sessions'] * 100,3)?>&nbsp;%</td>
			<td><div<?php if ($stats['cancelled'] > 0): ?> class="cancelled"<?php endif; ?>><?=$stats['cancelled']?></div></td>

			<?php if ($stats['conversions'] > 0): ?>
			<td><?=round($stats['cancelled'] / $stats['conversions'] * 100,1)?>&nbsp;%</td>
			<?php else: ?>
			<td>-</td>
			<?php endif; ?>
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
