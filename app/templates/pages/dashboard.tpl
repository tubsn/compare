<main>

<?php include tpl('navigation/date-picker');?>

<h1>
	Conversions: <span class="conversions"><?=$numberOfOrders?></span>
	<?php if ($mediatime > 0): ?>
	<!--
	&ensp; Lesedauer:
	<span class="greenbg">
		<?php if ($mtDays): ?><?=$mtDays?>t<?php endif; ?>
		<?php if ($mtHours): ?><?=$mtHours?>h<?php endif; ?>
		<?php if ($mtMinutes): ?><?=$mtMinutes?>m<?php endif; ?>
		<?php if ($mtSeconds): ?><?=$mtSeconds?>s<?php endif; ?>
	</span>
	-->
	<?php endif; ?>
	&ensp; <span title="⌀-Zahl von online Abonnenten pro Tag">Daily Active Subs:</span> <span class="das"><?=gnum($premiumAvg)?></span>
	&ensp; Pageviews: <span class="pageviews"><?=gnum($pageviews)?></span>
</h1>

<hr>

<p class="light-box" style="margin-bottom:2em; margin-top:1em; width:100%; text-align:center; box-sizing:border-box">
Artikel: <b><?=$articles?></b>
&emsp; Subscriberviews: <b class="deepblue"><?=number_format($subscriberviews,0,',','.')?></b>

&emsp; ⌀-Besucher pro Tag: <b class="blue"><?=gnum($usersAvg)?></b>
&emsp; ⌀-Mediatime: <b class="green"><?=number_format($avgmediatime,0,',','.')?>&thinsp;s</b>
&emsp; Conv. Plusseite: <b class="blue"><?=$plusOnly?></b>
&emsp; Conv. Aboshop: <b class="blue"><?=$aboshopOnly?></b>

&emsp; Gekündigt: <b class="redish"><?=$numberOfCancelled?></b>
&emsp; Kündigerquote: <b class="orange"><?=$cancelQuote?>&thinsp;%</b>
&emsp; ⌀-Haltedauer: <b class="blue"><?=number_format($averageRetention,2,',','.')?> Tage</b>
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

<p class="text-center" style="margin-bottom:2em; ">(<b>Achtung:</b> Tracking von Plusnutzern (Daily Active Subscribers) seit August'22 leider gestört)</p>

<div class="col-2" style="grid-template-columns: 1fr 2fr;">

<figure class="mb">
	<h3 class="text-center">Ø-Besucher pro Tag</h3>
	<?=$charts->create([
		'metric' => $premiumUsers['users'],
		'dimension' => $premiumUsers['dimensions'],
		'color' => '#2F5772',
		'height' => 300,
		'legend' => 'top',
		'tickamount' => 12,
		'stacked' => false,
		'area' => true,
		'xfont' => '13px',
		'showValues' => false,
		'name' => 'Besucher pro Tag',
		'template' => 'charts/default_line_chart',
	]);?>
</figure>

<figure class="mb">
	<h3 class="text-center">Daily Active Subscribers (Ø-Abonnenten am Tag)</h3>
	<?=$charts->create([
		'metric' => $premiumUsers['subscribers'],
		'dimension' => $premiumUsers['dimensions'],
		'color' => '#d99fd2',
		'height' => 300,
		'legend' => 'top',
		'tickamount' => 12,
		'stacked' => false,
		'area' => true,
		'xfont' => '13px',
		'showValues' => false,
		'name' => 'Abonnenten pro Tag',
		'template' => 'charts/default_bar_chart',
	]);?>
</figure>
</div>

</main>
