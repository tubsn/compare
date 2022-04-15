<main class="reader-detail">
<div class="reader-layout mb">

<section>

	<h1>Nutzer: <a class="reader-id" target="_blank" title="In Plenigo öffnen" href="https://backend.plenigo.com/<?=PLENIGO_COMPANY_ID?>/customers/<?=$reader['user_id']?>/show"><?=$reader['user_id']?></a></h1>
	<h3>Portal: <?=$reader['portal']?? $reader['error']?></h3>

	<p>
		Conversion Wahrscheinlichkeit: <?=round($reader['conversion_score'],2) * 100?>&thinsp;%
		<div style="margin-bottom:1em; grid-template-columns: 1fr 1fr; grid-gap:0.5em;">
			<div class="indicator plusleser" style="margin-top:-15px;">
				<div style="width:<?=round($reader['conversion_score'],2) * 100?>%;"></div>
			</div>
		</div>
	</p>

	<p>
		<?php if ($subscription): ?>
		<?php if ($subscription['customer_city']): ?>
		Location: <b><?=$subscription['customer_city'] ?? $subscription['ga_city']?></b><br>
		<?php endif; ?>

		<?php if (!$subscription['customer_city'] && $subscription['ga_city']): ?>
		Location: <b><?=$subscription['ga_city']?></b> (laut IP)<br>
		<?php endif; ?>
		<?php endif; ?>

		<?php if ($mostRead): ?>
		Lieblingsressort: <a href="/ressort/<?=key($mostRead)?>"><?=ucfirst(key($mostRead))?></a><br />
		<?php endif; ?>

		<?php if ($audience): ?>
		Lieblingsaudience: <a href="/audience/<?=key($audience)?>"><?=ucfirst(key($audience))?></a><br />
		<?php endif; ?>

		<?php if ($cluster): ?>
		Lieblingsthema: <a href="/type/<?=key($cluster)?>"><?=ucfirst(key($cluster))?></a><br />
		<?php endif; ?>
	</p>

	<?php if ($reader['first_seen']): ?>
	<p>Tracking seit: <b><?=formatDate($reader['first_seen'],'d.m.Y')?></b></p>
	<?php endif; ?>

</section>


<section style="justify-self:center">

	<?php if ($subscription): ?>
	<h3>Aktuell laufendes Abo:</h3>

	<div class="current-subscription" style="min-width:500px">

		<div style="float:right; text-align:right;">
		vom <b><?=formatDate($subscription['subscription_start_date'],'d.m.Y')?></b>
		<?php if ($subscription['subscription_end_date']): ?>
			<br>
		bis <b><?=formatDate($subscription['subscription_end_date'],'d.m.Y')?></b>
		<?php endif; ?>
		</div>

		<b><?=$subscription['subscription_title']?> - <?=gnum($subscription['subscription_price'],2)?>€</b>

		<br />

		Quelle: <b><?=ucfirst($subscription['article_ressort'] ?? $subscription['order_origin'])?></b>

		(ID: <a class="noline" target="_blank" title="In Plenigo öffnen" href="https://backend.plenigo.com/<?=PLENIGO_COMPANY_ID?>/subscriptions/chain/<?=$subscription['subscription_id']?>/show"><?=$subscription['subscription_id']?></a>)

	</div>

	<?php if ($subscription['cancelled']): ?>
	<div class="current-subscription cancelled">bereits gekündigt<br />
		<b>Grund: <?=CANCELLATION_REASON[$subscription['cancellation_reason'] ?? 0]?></b>
	</div>
	<?php endif; ?>


	<?php else: ?>
	<h3>Zur Zeit kein aktives Abo</h3>
	<?php endif; ?>

	<details class="mt">
	<summary>Bestell-Historie - <b class=""><?=$orderCount?></b> Käufe <small>(Klicken zum aufklappen)</small>:

		<p style="margin-left:.8em;">
		<?php if ($orderSources): ?>
		<?php foreach ($orderSources as $ressort => $amount): ?>
			<small class="subscription-ressorts" href="/ressort/<?=$ressort?>"><?=ucfirst($ressort)?> (<?=$amount?>)</small>
		<?php endforeach; ?>
		<?php endif; ?>
		</p>
	</summary>



	<ul class="order-list" style="margin-bottom:0.5em;">
	<?php foreach ($orders as $order): ?>
		<li>
			<div>
			<?=$order['order_title']?> - <?=gnum($order['order_price'],2)?>&thinsp;€<br />
			Quelle: <b><?=ucfirst($order['article_ressort'] ?? $order['order_origin'])?></b> (ID: <b><a class="noline" target="_blank" title="In Plenigo öffnen" href="https://backend.plenigo.com/<?=PLENIGO_COMPANY_ID?>/orders/<?=$order['order_id']?>/show"><?=$order['order_id']?></a></b>)



			<?php if ($order['retention']): ?>
			<br>Haltedauer: <b><?=$order['retention']?> Tage</b>
			<?php endif; ?>

			</div>

			<div style="align-self:center; justify-self:end; text-align:right;">
			von <b><?=formatDate($order['order_date'],'d.m.Y')?></b><br>
			<?php if ($order['subscription_end_date']): ?>
			bis <b><?=formatDate($order['subscription_end_date'],'d.m.Y')?></b>
			<?php endif; ?>
			</div>
		</li>
	<?php endforeach; ?>
	</ul>

	<?php if ($orders && count($orders)>1): ?>
	Gesamthaltedauer bisheriger Abos: <b><?=array_sum(array_column($orders,'retention'))?> Tage</b><br/>
	(Kauf bis Kündigungseingang - Lesedauer ist ggf. größer)
	<?php endif ?>

	</details>

</section>


<section>

	<h3>Drive-User-Segment: <div class="pageviews"><?=ucfirst($reader['user_segment'])?></div></h3>

	<div class="col-2" style="grid-template-columns: min-content 1fr; grid-gap:1em;">

		<figure style="background:#ddd; padding:2em; width:100px;">
			<img src="/styles/img/flundr/icon-login.svg" style="width:100%;"/>
		</figure>

		<article>
			<?php if ($reader['last_seen']): ?>
			<p>
				Zuletzt gesehen: <b><?=formatDate($reader['last_seen'],'d.m.Y')?></b>
				(<?=substr(TAGESNAMEN[formatDate($reader['last_seen'],'w')],0,2)?>)
				<br />Uhrzeit: <?=formatDate($reader['last_seen'],'h:i')?>&thinsp;Uhr
			</p>
			<?php endif; ?>
			<p>
				Nutzungsdauer letzte Woche: <br/>
				<b class="greenbg">Ø-<?=gnum($reader['media_time_last_week']/60/7)?> Minuten</b> pro Tag
			</p>
			<p>
				Gesamt Mediatime:<br/><b>
				<?=gmdate("H", $reader['media_time_total']);?>h
				<?=gmdate("i", $reader['media_time_total']);?>m
				<?=gmdate("s", $reader['media_time_total']);?>s
			</b>
			</p>

		</article>

	</div>

</section>

</div>

<?php include tpl('pages/reader-article-list');?>

</main>
