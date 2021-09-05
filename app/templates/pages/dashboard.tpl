<main>

<?php include tpl('navigation/date-picker');?>

<h1>
	Conversions: <span class="conversions"><?=$numberOfOrders?></span>
	<?php if ($mediatime > 0): ?>
	&ensp; Lesedauer:
	<span class="greenbg">
		<?php if ($mtDays): ?><?=$mtDays?>t<?php endif; ?>
		<?php if ($mtHours): ?><?=$mtHours?>h<?php endif; ?>
		<?php if ($mtMinutes): ?><?=$mtMinutes?>m<?php endif; ?>
		<?php if ($mtSeconds): ?><?=$mtSeconds?>s<?php endif; ?>
	</span>
	<?php endif; ?>
	&ensp; Pageviews: <span class="pageviews"><?=number_format($pageviews,0,',','.')?></span>
</h1>

<hr>

<p class="light-box" style="margin-bottom:2em; margin-top:1em; width:100%; text-align:center; box-sizing:border-box">
Produzierte Artikel: <b><?=$articles?></b>
&emsp; Subscribers: <b class="deepblue"><?=number_format($subscribers,0,',','.')?></b>
&emsp; Conversions Plusseite: <b class="blue"><?=$plusOnly?></b>
&emsp; Conversions Extern: <b class="blue"><?=$externalOnly?></b>
&emsp; ⌀-Mediatime: <b class="green"><?=number_format($avgmediatime,0,',','.')?>&thinsp;s</b>
&emsp; Gekündigt: <b class="redish"><?=$numberOfCancelled?></b>
&emsp; Kündigerquote: <b class="orange"><?=$cancelQuote?>&thinsp;%</b>
&emsp; ⌀-Haltedauer bei Kündigern: <b class="blue"><?=number_format($averageRetention,2,',','.')?> Tage</b>
</p>

<hr/>

<div class="col-2" style="grid-template-columns: 2fr 1fr;">

	<figure class="mb">
		<h3 class="text-center">Subscriber nach Ressort</h3>
		<?=$charts->get('subscribersByRessort');?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Subscriber im Zeitverlauf</h3>
		<?=$charts->get('subscribersByDate');?>
	</figure>

</div>

<div class="col-2" style="grid-template-columns: 1fr 2fr;">

	<figure class="mb">
		<h3 class="text-center">Conversions im Zeitverlauf</h3>
		<?=$charts->get('conversionsByDate');?>
	</figure>


	<figure class="mb">
		<h3 class="text-center">Conversions nach Ressort</h3>
		<?=$charts->get('conversionsByRessortWithValues');?>
	</figure>

</div>

<hr>


<div class="col-2" style="grid-template-columns: 1fr 1fr;">

	<figure class="">
		<h3 class="text-center">Ø-Mediatime nach Ressort (in Sekunden)</h3>
		<?=$charts->get('mediatimeByRessort');?>
	</figure>

	<figure class="">
		<h3 class="text-center">Ø-Pageviews nach Ressort</h3>
		<?=$charts->get('avgPageviewsByRessort');?>
	</figure>

</div>

</main>
