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
&emsp; Subscriberviews: <b class="deepblue"><?=number_format($subscriberviews,0,',','.')?></b>
&emsp; ⌀-Abonennten online am Tag: <b class="blue" style="color:#C52233"><?=gnum($premiumAvg)?></b>
<!--&emsp; Conversions Plusseite: <b class="blue"><?=$plusOnly?></b>-->
<!--&emsp; Conversions Aboshop: <b class="blue"><?=$aboshopOnly?></b>-->
&emsp; ⌀-Mediatime: <b class="green"><?=number_format($avgmediatime,0,',','.')?>&thinsp;s</b>
&emsp; Gekündigt: <b class="redish"><?=$numberOfCancelled?></b>
&emsp; Kündigerquote: <b class="orange"><?=$cancelQuote?>&thinsp;%</b>
&emsp; ⌀-Haltedauer bei Kündigern: <b class="blue"><?=number_format($averageRetention,2,',','.')?> Tage</b>
</p>

<hr/>


<div class="col-2" style="grid-template-columns: 2fr 1fr;">

	<figure class="mb">
		<h3 class="text-center">Subscriberviews nach Ressort</h3>
		<?=$charts->get('avg_subscriberviews_by', 'ressort');?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Subscriberviews im Zeitverlauf</h3>
		<?=$charts->get('subscriberviews_by_date_wholepage');?>
	</figure>

</div>

<div class="col-2" style="grid-template-columns: 1fr 2fr;">

	<figure class="mb">
		<h3 class="text-center">Conversions im Zeitverlauf</h3>
		<?=$charts->get('conversions_by_date');?>
	</figure>


	<figure class="mb">
		<h3 class="text-center">Conversions nach Ressort</h3>
		<?=$charts->get('conversions_by', 'article_ressort');?>
	</figure>

</div>



<div class="col-2" style="grid-template-columns: 1fr 1fr;">

	<figure class="">
		<h3 class="text-center">Ø-Mediatime nach Ressort (in Sekunden)</h3>
		<?=$charts->get('mediatime_by', 'ressort');?>
	</figure>

	<figure class="">
		<h3 class="text-center">Ø-Pageviews nach Ressort</h3>
		<?=$charts->get('avg_pageviews_by', 'ressort');?>
	</figure>

</div>

<figure class="mb">
	<h3 class="text-center">Ø-Aufkommen an Besuchern / Abonnenten am Tag</h3>
	<?=$charts->create([
		'metric' => [
			$premiumUsers['users'],
			$premiumUsers['users_reg'],
	     ],
		'dimension' => $premiumUsers['dimensions'],
		'color' => ['#2F5772', '#C52233'],
		'height' => 250,
		'legend' => 'top',
		'tickamount' => 12,
		'stacked' => false,
		'area' => false,
		'xfont' => '13px',
		'showValues' => false,
		'name' => ['Besucher pro Tag', 'Abonnenten pro Tag'],
		'template' => 'charts/default_bar_chart',
	]);?>
</figure>

</main>
